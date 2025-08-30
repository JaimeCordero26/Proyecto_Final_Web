<x-app-layout>
    <div class="container mx-auto p-6 max-w-xl text-center">
        <h1 class="text-2xl font-bold mb-2 text-white">Pago rechazado</h1>
        <p class="text-gray-600">Intenta con otra tarjeta.</p>
        <a href="{{ route('cart.checkout.pay') }}" class="inline-block mt-6 text-indigo-600 hover:underline">Volver al pago</a>
    </div>
</x-app-layout>
