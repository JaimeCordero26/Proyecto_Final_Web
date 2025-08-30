<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function salesPdf()
    {
        $orders = Order::whereIn('status', ['paid','shipped','completed'])
            ->latest()->limit(200)->get();

        $total = $orders->sum('total');

        $pdf = Pdf::loadView('reports.sales', [
            'orders' => $orders,
            'total' => $total,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('sales-report-'.now()->format('Ymd-His').'.pdf');
    }
}
