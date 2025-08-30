<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('cliente')
                ->state(fn (Order $record) => $record->user->name ?? 'N/A'),
            ExportColumn::make('status')->label('Estado'),
            ExportColumn::make('subtotal'),
            ExportColumn::make('tax')->label('Impuesto'),
            ExportColumn::make('shipping')->label('Envío'),
            ExportColumn::make('total'),
            ExportColumn::make('payment_method')->label('Método'),
            ExportColumn::make('reference')->label('Referencia'),
            ExportColumn::make('created_at')->label('Creado'),
            ExportColumn::make('updated_at')->label('Actualizado'),
        ];
    }

    public function getFileName(Export $export): string
    {
        return 'orders-' . now()->format('Ymd-His');
    }

    public function getFormats(): array
    {
        return [
            ExportFormat::Xlsx,
            ExportFormat::Csv,
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de órdenes finalizó: ' . number_format($export->successful_rows) . ' '
              . str('fila')->plural($export->successful_rows) . ' exportada(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' '
                   . str('fila')->plural($failedRowsCount) . ' falló/fallaron al exportar.';
        }

        return $body;
    }
}
