@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Catálogo de productos</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach($products as $product)
        <div class="border rounded-lg p-4 shadow hover:shadow-lg transition">
            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-40 object-cover mb-3">
            <h2 class="font-semibold">{{ $product->name }}</h2>
            <p class="text-gray-600">₡{{ number_format($product->price, 2) }}</p>
            <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-2">
                @csrf
                <button class="bg-blue-500 text-white px-4 py-2 rounded">Agregar</button>
            </form>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>
@endsection
