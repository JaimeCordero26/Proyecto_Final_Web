<x-app-layout>
    <div class="container mx-auto p-6 max-w-lg">
        <h1 class="text-2xl font-bold mb-4 text-white">Pagar</h1>
        <div class="bg-white shadow rounded p-5">
            <div class="mb-4 text-sm text-gray-600">
                Pedido #{{ $order->id }} — Total: <strong>₡{{ number_format($order->total, 2) }}</strong>
            </div>

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('cart.checkout.process') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium">Nombre en la tarjeta</label>
                    <input type="text" name="card_name" value="{{ old('card_name') }}" class="w-full border rounded px-3 py-2" required>
                    @error('card_name') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Número de tarjeta</label>
                    <input type="text" inputmode="numeric" name="card_number" value="{{ old('card_number') }}" class="w-full border rounded px-3 py-2" placeholder="4111111111111111" required>
                    <p class="text-xs text-gray-500 mt-1">**Simulación:** si termina en dígito PAR → aprobado, IMPAR → rechazado.</p>
                    @error('card_number') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium">Mes</label>
                        <input type="number" name="exp_month" min="1" max="12" value="{{ old('exp_month') }}" class="w-full border rounded px-3 py-2" required>
                        @error('exp_month') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Año</label>
                        <input type="number" name="exp_year" min="{{ now()->year }}" max="{{ now()->year + 10 }}" value="{{ old('exp_year') }}" class="w-full border rounded px-3 py-2" required>
                        @error('exp_year') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium">CVC</label>
                    <input type="number" name="cvc" value="{{ old('cvc') }}" class="w-full border rounded px-3 py-2" required>
                    @error('cvc') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="text-right">
                    <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">Pagar</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
