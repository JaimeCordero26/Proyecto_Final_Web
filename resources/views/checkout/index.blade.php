<x-app-layout>
    <div class="container mx-auto p-6 max-w-3xl">
        <h1 class="text-2xl font-bold mb-4 text-white">Checkout</h1>

        <div class="bg-white shadow rounded p-4 mb-6">
            @foreach($cart as $pid => $item)
                <div class="flex justify-between py-2 border-b">
                    <div>
                        <div class="font-medium">{{ $item['name'] ?? $item['title'] }}</div>
                        <div class="text-sm text-gray-500">x {{ $item['quantity'] }}</div>
                    </div>
                    <div>₡{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                </div>
            @endforeach

            <div class="pt-4 space-y-1 text-right">
                <div>Subtotal: <strong>₡{{ number_format($subtotal, 2) }}</strong></div>
                <div>Impuesto (13%): <strong>₡{{ number_format($tax, 2) }}</strong></div>
                <div>Envío: <strong>₡{{ number_format($shipping, 2) }}</strong></div>
                <div class="text-xl">Total: <strong>₡{{ number_format($total, 2) }}</strong></div>
            </div>
        </div>

        <form action="{{ route('cart.checkout.create') }}" method="POST" class="text-right">
            @csrf
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded">
                Pagar ahora
            </button>
        </form>
    </div>
</x-app-layout>
