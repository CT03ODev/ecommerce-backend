<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Filament\Resources\VoucherResource\RelationManagers;
use App\Models\Voucher;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Voucher Information')
                    ->description('Create or edit a voucher')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->required(),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        Select::make('discount_type')
                            ->options([
                                'fixed' => 'Fixed Amount',
                                'percentage' => 'Percentage'
                            ])
                            ->required(),
                        TextInput::make('discount_value')
                            ->numeric()
                            ->required()
                            ->prefix(fn (Get $get) => $get('discount_type') === 'percentage' ? '%' : '$'),
                        TextInput::make('minimum_spend')
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('maximum_discount')
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('usage_limit')
                            ->numeric()
                            ->minValue(1),
                        DateTimePicker::make('start_date'),
                        DateTimePicker::make('end_date'),
                        Toggle::make('is_active')
                            ->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('discount_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fixed' => 'success',
                        'percentage' => 'info',
                    }),
                TextColumn::make('discount_value')
                    ->money(fn (Model $record) => $record->discount_type === 'fixed' ? 'USD' : false)
                    ->suffix(fn (Model $record) => $record->discount_type === 'percentage' ? '%' : '')
                    ->sortable(),
                TextColumn::make('usage_count')
                    ->sortable(),
                TextColumn::make('usage_limit')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->dateTime()
                    ->sortable(),
                ToggleColumn::make('is_active')
            ])
            ->filters([
                Filter::make('active')->query(fn (Builder $query): Builder => $query->where('is_active', true))->label('Active Only'),
                Filter::make('expired')->query(fn (Builder $query): Builder => $query->where('end_date', '<', now()))->label('Expired'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}
