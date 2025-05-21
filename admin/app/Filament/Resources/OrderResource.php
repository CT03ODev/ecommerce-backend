<?php

namespace App\Filament\Resources;

use App\Enums\OrderStatus;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Order';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Order Details')
                    ->description('Create or edit an order')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->native(false)
                            ->relationship('user', 'name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('address_id', null)),
                        Forms\Components\Select::make('address_id')
                            ->native(false)
                            ->label('Shipping Address')
                            ->options(function (callable $get) {
                                $userId = $get('user_id');
                                if (!$userId) return [];
                                
                                return \App\Models\Address::where('user_id', $userId)
                                    ->pluck('name', 'id');
                            })
                            ->required(),
                        Forms\Components\TextInput::make('order_number')
                            ->default('ORD-' . Str::random(10))
                            ->readOnly()
                            ->dehydrated()
                            ->required(),
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->required()
                            ->readOnly()
                            ->dehydrated()
                            ->suffix('$'),
                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Tax amount (5%)')
                            ->numeric()
                            ->required()
                            ->readOnly()
                            ->dehydrated()
                            ->suffix('$'),
                        Forms\Components\TextInput::make('shipping_amount')
                            ->numeric()
                            ->required()
                            ->live(debounce: 500)
                            ->suffix('$')
                            ->afterStateUpdated(function ($state, $get, callable $set) {
                                // Tính lại tổng tiền khi thay đổi phí ship
                                $subtotal = collect($get('orderItems'))
                                    ->sum(fn ($item) => $item['quantity'] * $item['unit_price']);
                                
                                $shippingAmount = (float) $state ?? 0;
                                $total = $subtotal + $shippingAmount;
                                $taxAmount = $total * 0.05; // 5% tax
                                
                                $set('total_amount', $total + $taxAmount);
                                $set('tax_amount', $taxAmount);
                            }),
                        Forms\Components\Hidden::make('status')
                            ->default(OrderStatus::PENDING->value),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                Section::make('Order Items')
                    ->description('Add products to this order')
                    ->schema([
                        Repeater::make('orderItems')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->native(false)
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('product_variant_id', null);
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('unit_price', $product->price);
                                            }
                                        }
                                    }),
                                Forms\Components\Select::make('product_variant_id')
                                    ->native(false)
                                    ->label('Variant (Size/Color)')
                                    ->options(function (callable $get) {
                                        $productId = $get('product_id');
                                        if (!$productId) return [];
                                        
                                        return \App\Models\ProductVariant::where('product_id', $productId)
                                            ->get()
                                            ->mapWithKeys(function ($variant) {
                                                return [
                                                    $variant->id => "Size: {$variant->size} - Color: {$variant->color} (Stock: {$variant->stock})"
                                                ];
                                            });
                                    })
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                        if ($state) {
                                            $variant = \App\Models\ProductVariant::find($state);
                                            if ($variant) {
                                                $price = $variant->price ?? $variant->product->price ?? 0;
                                                $set('unit_price', $price);
                                                $set('subtotal', $price * ($get('quantity') ?? 1));
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1)
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, $get, callable $set) {
                                        $unitPrice = $get('unit_price');
                                        $subtotal = $state * $unitPrice;
                                        $set('subtotal', $subtotal);
                                        
                                        // Tính tổng tiền và thuế
                                        $subtotal = collect($get('../../orderItems'))
                                            ->sum(fn ($item) => ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0));
                                        
                                        $shippingAmount = (float) $get('../../shipping_amount') ?? 0;
                                        $total = $subtotal + $shippingAmount;
                                        $taxAmount = $total * 0.05; // 5% tax
                                        
                                        $set('../../total_amount', $total + $taxAmount);
                                        $set('../../tax_amount', $taxAmount);
                                    }),
                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->required()
                                    ->readOnly()
                                    ->suffix('$'),
                                Forms\Components\TextInput::make('subtotal')
                                    ->numeric()
                                    ->readOnly()
                                    ->required()
                                    ->suffix('$'),
                            ])
                            ->columns(5)
                            ->defaultItems(1)
                            ->addActionLabel('Thêm sản phẩm')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address.name')
                    ->label('Shipping Address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('USD')
                    ->suffix('$')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_amount')
                    ->money('USD')
                    ->suffix('$')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_amount')
                    ->money('USD')
                    ->suffix('$')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => OrderStatus::CANCELLED->value,
                        'warning' => OrderStatus::PENDING->value,
                        'primary' => OrderStatus::PROCESSING->value,
                        'success' => OrderStatus::SHIPPED->value,
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter by User'),
                Tables\Filters\SelectFilter::make('status')
                    ->options(OrderStatus::values())
                    ->label('Filter by Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('change_status')
                    ->label('Change Status')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options(OrderStatus::values())
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data): void {
                        $record->update(['status' => $data['status']]);
                        
                        // Nếu trạng thái là delivered, cập nhật transaction sang completed
                        if ($data['status'] === OrderStatus::DELIVERED->value) {
                            $record->transactions()
                                ->where('status', '!=', 'completed')
                                ->update(['status' => 'completed']);
                        } else if ($data['status'] === OrderStatus::CANCELLED->value) {
                            $record->transactions()
                                ->where('status', '!=', 'refunded')
                                ->update(['status' => 'refunded']);
                        }
                    })
                    ->visible(fn (Order $record): bool => $record->status !== OrderStatus::DELIVERED->value),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
