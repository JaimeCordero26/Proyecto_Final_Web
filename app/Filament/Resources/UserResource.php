<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends \Filament\Resources\Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Administración';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información Personal')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->label('Nombre'),
                    
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(User::class, 'email', ignoreRecord: true)
                        ->maxLength(255)
                        ->label('Correo Electrónico'),
                    
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->minLength(8)
                        ->label('Contraseña')
                        ->helperText('Mínimo 8 caracteres. Dejar vacío para mantener la actual (solo al editar)'),
                    
                    TextInput::make('password_confirmation')
                        ->password()
                        ->same('password')
                        ->required(fn (string $context): bool => $context === 'create')
                        ->label('Confirmar Contraseña')
                        ->dehydrated(false),
                ])->columns(2),

            Forms\Components\Section::make('Configuración de Cuenta')
                ->schema([
                    Toggle::make('email_verified_at')
                        ->label('Email Verificado')
                        ->dehydrateStateUsing(fn ($state) => $state ? now() : null)
                        ->default(true),
                    
                    Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->label('Roles')
                        ->helperText('Selecciona uno o más roles para este usuario'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nombre'),
                
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email'),
                
                BadgeColumn::make('roles.name')
                    ->label('Roles')
                    ->colors([
                        'danger' => 'admin',
                        'warning' => 'manager',
                        'success' => 'user',
                        'primary' => fn ($state): bool => !in_array($state, ['admin', 'manager', 'user']),
                    ]),
                
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->boolean()
                    ->label('Verificado')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->label('Creado')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->since()
                    ->label('Actualizado')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Filtrar por Rol'),
                
                Tables\Filters\Filter::make('verified')
                    ->query(fn ($query) => $query->whereNotNull('email_verified_at'))
                    ->label('Solo Verificados'),
                
                Tables\Filters\Filter::make('unverified')
                    ->query(fn ($query) => $query->whereNull('email_verified_at'))
                    ->label('Solo No Verificados'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}