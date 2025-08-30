<x-app-layout>
<div class="container mx-auto p-6 text-center">
<h1 class="text-3xl font-bold mb-4 text-green-600">¡Pago Exitoso!</h1>
<p class="text-lg text-gray-700 mb-8">Tu pedido ha sido procesado y será enviado pronto. ¡Gracias por tu compra!</p>
<div class="inline-flex items-center space-x-2 bg-green-100 p-4 rounded-lg">
<svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
<span class="text-green-800 font-semibold">Confirmación de Pago Enviada por Correo</span>
</div>
<div class="mt-8">
<a href="{{ route('home') }}" class="bg-blue-500 text-white px-6 py-3 rounded-lg font-bold">
Volver al Inicio
</a>
</div>
</div>
</x-app-layout>