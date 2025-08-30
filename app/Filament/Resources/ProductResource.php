<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;

class ProductResource extends \Filament\Resources\Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Gestión de Catálogo';
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('category_id')
                ->relationship('category', 'name')
                ->required()
                ->searchable(),
            TextInput::make('title')->required()->maxLength(255),
            Textarea::make('description')->rows(4),
            TextInput::make('price')->numeric()->minValue(0)->required(),
            TextInput::make('stock')->numeric()->minValue(0)->default(0),

            Repeater::make('images')
                ->relationship() // usa hasMany(ProductImage)
                ->schema([
                    TextInput::make('url')->url()->required()->label('Image URL'),
                    TextInput::make('position')->numeric()->default(1),
                ])
                ->orderable('position')
                ->collapsible()
                ->grid(2)
                ->label('Imágenes'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('title')->searchable()->limit(40)->sortable(),
            TextColumn::make('category.name')->label('Category')->sortable()->searchable(),
            TextColumn::make('price')->money('USD', true)->sortable(), // cambia a CRC si quieres
            TextColumn::make('stock')->sortable(),
            TextColumn::make('images_count')
                ->counts('images')   // cuenta relación
                ->label('Imgs')
                ->sortable(),
            TextColumn::make('created_at')->dateTime()->since()->label('Creado'),
        ])->defaultSort('id', 'desc')
          ->filters([
              Tables\Filters\SelectFilter::make('category_id')->relationship('category', 'name')->label('Category'),
          ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
