<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        h1 { margin: 0; }
        .footer { margin-top: 8px; font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <h1>Reporte de Ventas</h1>
    <div class="footer">Generado: {{ $generatedAt }}</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Método</th>
                <th>Referencia</th>
                <th>Fecha</th>
                <th>Total (₡)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $o)
                <tr>
                    <td>{{ $o->id }}</td>
                    <td>{{ $o->user->name ?? 'N/A' }}</td>
                    <td>{{ $o->status }}</td>
                    <td>{{ $o->payment_method ?? '-' }}</td>
                    <td>{{ $o->reference ?? '-' }}</td>
                    <td>{{ $o->created_at->format('Y-m-d H:i') }}</td>
                    <td style="text-align:right">{{ number_format($o->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total general: ₡ {{ number_format($total, 2) }}</strong></p>
</body>
</html>
