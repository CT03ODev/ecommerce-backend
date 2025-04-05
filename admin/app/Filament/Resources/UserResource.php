<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    
    protected static ?string $navigationGroup = 'Roles and Permissions';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->minLength(4)
                            ->maxLength(255),
                        TextInput::make('email')
                            ->required()
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->minLength(4)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(4)
                            ->maxLength(255),
                        Select::make('roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload(),
                        FileUpload::make('profile_photo_path')
                            ->label('Avatar')
                            ->image()
                            ->imageEditor()
                            ->directory('/profile-photos')
                            ->required()
                            ->optimize('webp'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('email')
                    ->sortable(),
                TextColumn::make('roles')
                    ->formatStateUsing(function (String $state): ?string {
                        // return $state;
                        $roles = json_decode('['.$state.']');
                        // dd($roles);
                        $roles = Arr::pluck($roles, 'name');
                        return Arr::join($roles, ',');
                    })
                    ->badge()
                    ->separator(','),
                ImageColumn::make('profile_photo_path')->label('Avatar'),
            ])
            ->filters([
                //
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
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}
