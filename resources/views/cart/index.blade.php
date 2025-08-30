<x-app-layout>
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
                        @foreach($cart as $id => $item)
                            @php
                                $itemTotal = $item['price'] * $item['quantity'];
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
                            <td colspan="3" class="text-right py-3">Subtotal:</td>
                            <td class="py-3">₡{{ number_format($subtotal ?? 0, 2) }}</td>
                        </tr>
                        <tr class="font-semibold">
                            <td colspan="3" class="text-right py-3">Impuesto (13%):</td>
                            <td class="py-3">₡{{ number_format($tax ?? 0, 2) }}</td>
                        </tr>
                        <tr class="font-semibold">
                            <td colspan="3" class="text-right py-3">Envío:</td>
                            <td class="py-3">₡{{ number_format($shipping ?? 0, 2) }}</td>
                        </tr>
                        <tr class="font-semibold text-xl">
                            <td colspan="3" class="text-right py-3">Total:</td>
                            <td class="py-3">₡{{ number_format($total ?? 0, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('shop.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition-colors">
                        Seguir Comprando
                    </a>
                    
                    @auth
                        <!-- Opción 1: Formulario simple -->
                      <a href="{{ route('checkout.show') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-block transition-colors">
    Proceder al Checkout
</a>
                        
                        <!-- Opción 2: Formulario POST con validación -->
                        <form action="{{ route('checkout.process') }}" method="POST" class="inline" id="checkoutForm">
                            @csrf
                            <input type="hidden" name="cart_data" value="{{ json_encode($cart) }}">
                            <input type="hidden" name="total" value="{{ $total ?? 0 }}">
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition-colors" id="payButton">
                                <span id="buttonText">Proceder al Pago</span>
                                <span id="loadingText" class="hidden">Procesando...</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors">
                            Inicia Sesión para Continuar
                        </a>
                    @endauth
                </div>

                <!-- Mostrar errores si los hay -->
                @if ($errors->any())
                    <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white shadow-md rounded-lg p-6 text-center">
                <p class="text-gray-600 text-lg">Tu carrito está vacío</p>
                <a href="{{ route('shop.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mt-4 inline-block transition-colors">
                    Ir a Comprar
                </a>
            </div>
        @endif
    </div>

    <!-- JavaScript para mejorar la experiencia -->
    <script>
        document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
            const button = document.getElementById('payButton');
            const buttonText = document.getElementById('buttonText');
            const loadingText = document.getElementById('loadingText');
            
            // Validar que el carrito no esté vacío
            const cartData = JSON.parse(document.querySelector('input[name="cart_data"]').value);
            if (Object.keys(cartData).length === 0) {
                e.preventDefault();
                alert('Tu carrito está vacío');
                return;
            }
            
            // Mostrar estado de carga
            button.disabled = true;
            buttonText.classList.add('hidden');
            loadingText.classList.remove('hidden');
            
            // Revertir estado si hay error (opcional)
            setTimeout(() => {
                if (button.disabled) {
                    button.disabled = false;
                    buttonText.classList.remove('hidden');
                    loadingText.classList.add('hidden');
                }
            }, 10000); // 10 segundos timeout
        });
    </script>
</x-app-layout>