@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Carrito de Compras</h1>
    
    @if(count($cart) > 0)
        <div class="bg-white shadow-md rounded-lg p-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Producto</th>
                        <th class="text-left py-2">Precio</th>
                        <th class="text-left py-2">Cantidad</th>
                        <th class="text-left py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach($cart as $id => $item)
                        @php 
                            $itemTotal = $item['price'] * $item['quantity'];
                            $total += $itemTotal;
                        @endphp
                        <tr class="border-b">
                            <td class="py-3">{{ $item['name'] }}</td>
                            <td class="py-3">₡{{ number_format($item['price'], 2) }}</td>
                            <td class="py-3">{{ $item['quantity'] }}</td>
                            <td class="py-3">₡{{ number_format($itemTotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-semibold">
                        <td colspan="3" class="text-right py-3">Total:</td>
                        <td class="py-3">₡{{ number_format($total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="mt-6">
                <a href="{{ route('shop.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded mr-3">
                    Seguir Comprando
                </a>
                <button class="bg-blue-500 text-white px-4 py-2 rounded">
                    Proceder al Pago
                </button>
            </div>
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg p-6 text-center">
            <p class="text-gray-600 text-lg">Tu carrito está vacío</p>
            <a href="{{ route('shop.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded mt-4 inline-block">
                Ir a Comprar
            </a>
        </div>
    @endif
</div>
@endsection