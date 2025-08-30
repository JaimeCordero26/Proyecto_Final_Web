<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Proceder al Pago</h1>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Resumen del Pedido</h2>
            <table class="w-full mb-6">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Producto</th>
                        <th class="text-left py-2">Cantidad</th>
                        <th class="text-left py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $item)
                    <tr class="border-b">
                        <td class="py-3">{{ $item['name'] }}</td>
                        <td class="py-3">{{ $item['quantity'] }}</td>
                        <td class="py-3">₡{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-semibold">
                        <td colspan="2" class="text-right py-3">Total a pagar:</td>
                        <td class="py-3">₡{{ number_format($total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            <h2 class="text-xl font-semibold mb-4">Método de Pago</h2>
            <form action="{{ route('checkout.pay') }}" method="POST">
                @csrf
                <input type="hidden" name="total_amount" value="{{ $total }}">

                <div class="mb-4">
                    <label for="payment_method_card" class="inline-flex items-center">
                        <input type="radio" name="payment_method" id="payment_method_card" value="card" checked>
                        <span class="ml-2">Tarjeta de Crédito (Stripe)</span>
                    </label>
                </div>
                <div class="mb-4">
                    <label for="payment_method_paypal" class="inline-flex items-center">
                        <input type="radio" name="payment_method" id="payment_method_paypal" value="paypal">
                        <span class="ml-2">PayPal</span>
                    </label>
                </div>

                <div class="mt-6 text-center">
                    <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded-lg font-bold">
                        Confirmar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>