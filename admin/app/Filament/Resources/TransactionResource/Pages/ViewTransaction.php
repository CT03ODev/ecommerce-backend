<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Transaction Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('order.order_number')
                            ->label('Order Number')
                            ->url(fn ($record) => OrderResource::getUrl('view', ['record' => $record->order])),
                        TextEntry::make('created_at')
                            ->label('Transaction Date')
                            ->dateTime(),
                        TextEntry::make('transaction_type')
                            ->label('Transaction Type')
                            ->badge(),
                        TextEntry::make('payment_method')
                            ->label('Payment Method')
                            ->badge(),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money('USD'),
                        TextEntry::make('currency')
                            ->label('Currency'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'completed' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'info',
                                default => 'secondary',
                            }),
                        TextEntry::make('gateway_transaction_id')
                            ->label('Gateway Transaction ID')
                            ->copyable(),
                    ]),

                Section::make('Order Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('order.user.name')
                            ->label('Customer Name'),
                        TextEntry::make('order.user.email')
                            ->label('Customer Email'),
                        TextEntry::make('order.total_amount')
                            ->label('Order Total')
                            ->money('USD'),
                        TextEntry::make('order.status')
                            ->label('Order Status')
                            ->badge(),
                    ]),

                Section::make('Gateway Information')
                    ->schema([
                        TextEntry::make('gateway_response')
                            ->label('Gateway Response')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : null)
                            ->prose()
                            ->columnSpanFull(),
                        TextEntry::make('gateway_error')
                            ->label('Gateway Error')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : null)
                            ->prose()
                            ->color('danger')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
