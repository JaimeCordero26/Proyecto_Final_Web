<x-app-layout>

    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Catálogo de productos</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($products as $product)
                <div class="product-card">
                    @if($product->images->count() > 0)
                        <img src="{{ asset($product->images->first()->url) }}" 
                             alt="{{ $product->name }}"
                             class="product-image">
                    @else
                        <div class="no-image">Sin imagen disponible</div>
                    @endif
                    
                    <h3 class="product-name">{{ $product->name }}</h3>
                    <p class="price">${{ number_format($product->price, 2) }}</p>
                    <p class="category">{{ $product->category->name }}</p>
                    
                    <div class="btn-container">
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="add-to-cart-btn">
                                🛒 Agregar al carrito
                            </button>
                        </form>
                    </div>
                    
                    @if($product->images->count() > 1)
                        <div class="additional-images">
                            @foreach($product->images->slice(1) as $image)
                                <img src="{{ asset($image->url) }}" 
                                     alt="{{ $product->name }} - Imagen {{ $loop->iteration + 1 }}"
                                     class="thumb-image">
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>

    {{-- Estilos CSS --}}
    <style>
        .product-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .product-name {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .price {
            font-size: 1.1rem;
            color: #4CAF50;
            font-weight: bold;
            margin-bottom: 8px;
            transition: color 0.3s ease;
        }
        
        .price:hover {
            color: #38761d;
        }

        .category {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 16px;
        }

        .btn-container {
            text-align: center;
        }

        .add-to-cart-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .add-to-cart-btn:hover {
            background-color: #45a049;
        }

        .no-image {
            background-color: #f3f4f6;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-bottom: 12px;
            color: #6b7280;
        }
    </style>
</x-app-layout>