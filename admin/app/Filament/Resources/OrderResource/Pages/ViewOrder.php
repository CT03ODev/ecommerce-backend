<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Order Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('order_number')
                            ->label('Order Number'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('user.name')
                            ->label('Customer'),
                        TextEntry::make('address.name')
                            ->label('Shipping Address'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'processing' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'secondary',
                            }),
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),

                Section::make('Payment Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('total_amount')
                            ->label('Total Amount')
                            ->money('USD'),
                        TextEntry::make('tax_amount')
                            ->label('Tax')
                            ->money('USD'),
                        TextEntry::make('shipping_amount')
                            ->label('Shipping Fee')
                            ->money('USD'),
                    ]),

                Section::make('Products')
                    ->schema([
                        RepeatableEntry::make('orderItems')
                            ->schema([
                                ImageEntry::make('product.thumbnail')
                                    ->label('Image')
                                    ->height(60),
                                TextEntry::make('product.name')
                                    ->label('Product Name'),
                                TextEntry::make('productVariant.size')
                                    ->label('Size'),
                                TextEntry::make('productVariant.color')
                                    ->label('Color'),
                                TextEntry::make('quantity')
                                    ->label('Quantity'),
                                TextEntry::make('unit_price')
                                    ->label('Unit Price')
                                    ->money('USD'),
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->money('USD')
                                    ->state(fn ($record): float => $record->quantity * $record->unit_price),
                            ])
                            ->columns(7)
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('change_status')
                ->label('Change Status')
                ->form([
                    Select::make('status')
                        ->options(OrderStatus::values())
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->record->update(['status' => $data['status']]);
                    
                    // Nếu trạng thái là delivered, cập nhật transaction sang completed
                    if ($data['status'] === OrderStatus::DELIVERED->value) {
                        $this->record->transactions()
                            ->where('status', '!=', 'completed')
                            ->update(['status' => 'completed']);
                    }
                })
                ->visible(fn (): bool => $this->record->status !== OrderStatus::DELIVERED->value),
        ];
    }
}
