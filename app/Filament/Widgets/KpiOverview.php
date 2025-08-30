<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class KpiOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $today = now()->startOfDay();

        $totalSales = Order::whereIn('status', ['paid','shipped','completed'])->sum('total');
        $todaySales = Order::whereIn('status', ['paid','shipped','completed'])
            ->where('created_at', '>=', $today)
            ->sum('total');

        return [
            Stat::make('Productos', (string) Product::count())
                ->description('Registrados')
                ->icon('heroicon-o-cube'),

            Stat::make('Categorías', (string) Category::count())
                ->description('Disponibles')
                ->icon('heroicon-o-tag'),

            Stat::make('Ventas Totales', '₡' . number_format($totalSales, 2))
                ->description('Acumulado')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Ventas Hoy', '₡' . number_format($todaySales, 2))
                ->description('Desde 00:00')
                ->icon('heroicon-o-sparkles'),
        ];
    }
}
