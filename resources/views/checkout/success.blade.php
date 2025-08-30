<x-app-layout>
    <div class="container mx-auto p-6 max-w-xl text-center">
        <h1 class="text-2xl font-bold mb-2 text-white">¡Pago aprobado!</h1>
        <p class="text-gray-600">Tu pedido está en estado <strong>paid</strong>.</p>
        <a href="{{ route('shop.index') }}" class="inline-block mt-6 text-indigo-600 hover:underline">
            Volver a la tienda
        </a>
    </div>
</x-app-layout>
