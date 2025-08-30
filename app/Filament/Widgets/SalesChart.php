<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Ventas (últimos 14 días)';

    protected function getData(): array
    {
        $from = now()->subDays(13)->startOfDay();

        $rows = Order::select(
                DB::raw("DATE(created_at) as d"),
                DB::raw("SUM(total) as t")
            )
            ->whereIn('status', ['paid','shipped','completed'])
            ->where('created_at', '>=', $from)
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $labels = [];
        $data = [];
        $cursor = $from->copy();
        $map = $rows->pluck('t', 'd');

        for ($i = 0; $i < 14; $i++) {
            $key = $cursor->toDateString();
            $labels[] = $cursor->format('d/m');
            $data[] = (float) ($map[$key] ?? 0);
            $cursor->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => '₡ Ventas',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
