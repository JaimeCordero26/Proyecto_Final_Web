<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrderResource extends \Filament\Resources\Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationGroup = 'Ventas';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = Auth::user();

        if ($user?->hasRole('admin') || $user?->can('orders.viewAll')) {
            return $query;
        }

        return $query->where('user_id', $user?->id);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->default(fn () => Auth::id())
                ->disabled(fn () => ! Auth::user()?->hasRole('admin') && ! Auth::user()?->can('orders.assignUser'))
                ->required(),

            Select::make('status')
                ->options([
                    'pending'   => 'pending',
                    'paid'      => 'paid',
                    'shipped'   => 'shipped',
                    'completed' => 'completed',
                    'cancelled' => 'cancelled',
                ])
                ->required(),

            TextInput::make('subtotal')->numeric()->minValue(0)->required(),
            TextInput::make('tax')->numeric()->minValue(0)->required(),
            TextInput::make('shipping')->numeric()->minValue(0)->required(),
            TextInput::make('total')->numeric()->minValue(0)->required(),
            TextInput::make('payment_method'),
            TextInput::make('reference'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')->label('Cliente')->searchable()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('total')->money('USD', true)->sortable(),
                TextColumn::make('created_at')->dateTime()->since()->label('Fecha'),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('PDF Ventas')
                    ->url(route('reports.sales.pdf'))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
