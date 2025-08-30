<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6 text-white">Catálogo de productos</h1>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($products as $product)
                <div class="product-card bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    
                    @if($product->images->count() > 0)
                        <!-- Carrusel de imágenes -->
                        <div class="relative image-carousel">
                            <div class="carousel-container relative overflow-hidden">
                                <div class="carousel-track flex transition-transform duration-300 ease-in-out">
                                    @foreach($product->images as $image)
                                        <img src="{{ $image->full_url }}"
                                             alt="{{ $product->title }} - Imagen {{ $loop->iteration }}"
                                             class="carousel-image w-full h-48 object-cover flex-shrink-0">
                                    @endforeach
                                </div>
                                
                                @if($product->images->count() > 1)
                                    <!-- Botones de navegación -->
                                    <button class="carousel-prev absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-opacity-75 transition-all text-sm">
                                        &#8249;
                                    </button>
                                    <button class="carousel-next absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-opacity-75 transition-all text-sm">
                                        &#8250;
                                    </button>
                                @endif
                            </div>
                            
                            @if($product->images->count() > 1)
                                <!-- Indicadores -->
                                <div class="carousel-dots flex justify-center space-x-2 mt-2 p-2">
                                    @foreach($product->images as $image)
                                        <button class="dot w-2 h-2 rounded-full bg-gray-300 {{ $loop->first ? 'bg-blue-500' : '' }}" 
                                                data-slide="{{ $loop->index }}"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-gray-200 h-48 flex items-center justify-center text-gray-500">
                            Sin imagen disponible
                        </div>
                    @endif

                    <!-- Contenido del producto -->
                    <div class="p-4">
                        <h3 class="text-lg font-bold mb-2 line-clamp-2">{{ $product->title }}</h3>
                        <p class="text-green-600 text-xl font-bold mb-2">${{ number_format($product->price, 2) }}</p>
                        <p class="text-gray-600 text-sm mb-4">{{ $product->category->name }}</p>
                        
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition-colors font-medium">
                                🛒 Agregar al carrito
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>

    <!-- JavaScript del carrusel -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousels = document.querySelectorAll('.image-carousel');
            
            carousels.forEach(carousel => {
                const track = carousel.querySelector('.carousel-track');
                const images = carousel.querySelectorAll('.carousel-image');
                const prevBtn = carousel.querySelector('.carousel-prev');
                const nextBtn = carousel.querySelector('.carousel-next');
                const dots = carousel.querySelectorAll('.dot');
                
                if (images.length <= 1) {
                    return; // No hacer nada si solo hay una imagen
                }
                
                let currentSlide = 0;
                
                function updateCarousel() {
                    const translateX = -currentSlide * 100;
                    track.style.transform = `translateX(${translateX}%)`;
                    
                    // Actualizar dots
                    dots.forEach((dot, index) => {
                        if (index === currentSlide) {
                            dot.classList.add('bg-blue-500');
                            dot.classList.remove('bg-gray-300');
                        } else {
                            dot.classList.remove('bg-blue-500');
                            dot.classList.add('bg-gray-300');
                        }
                    });
                }
                
                // Navegación con botones
                if (nextBtn) {
                    nextBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentSlide = (currentSlide + 1) % images.length;
                        updateCarousel();
                    });
                }
                
                if (prevBtn) {
                    prevBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentSlide = (currentSlide - 1 + images.length) % images.length;
                        updateCarousel();
                    });
                }
                
                // Navegación con dots
                dots.forEach((dot, index) => {
                    dot.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentSlide = index;
                        updateCarousel();
                    });
                });
                
                // Auto-slide cada 5 segundos (opcional)
                let autoSlide = setInterval(() => {
                    currentSlide = (currentSlide + 1) % images.length;
                    updateCarousel();
                }, 5000);
                
                // Pausar auto-slide cuando se hace hover
                carousel.addEventListener('mouseenter', () => {
                    clearInterval(autoSlide);
                });
                
                carousel.addEventListener('mouseleave', () => {
                    autoSlide = setInterval(() => {
                        currentSlide = (currentSlide + 1) % images.length;
                        updateCarousel();
                    }, 5000);
                });
                
                // Inicializar
                updateCarousel();
            });
        });
    </script>

    <!-- Estilos adicionales -->
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-card:hover {
            transform: translateY(-2px);
        }
        
        .dot.bg-blue-500 {
            transform: scale(1.2);
        }

        /* Mejoras adicionales para el carrusel */
        .carousel-image {
            transition: opacity 0.3s ease;
        }
        
        .image-carousel:hover .carousel-prev,
        .image-carousel:hover .carousel-next {
            opacity: 1;
        }
        
        .carousel-prev,
        .carousel-next {
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        
        .carousel-prev:hover,
        .carousel-next:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }
        
        /* Responsive adjustments */
        @media (max-width: 640px) {
            .carousel-prev,
            .carousel-next {
                width: 6px;
                height: 6px;
                font-size: 12px;
            }
            
            .dot {
                width: 6px;
                height: 6px;
            }
        }
    </style>
</x-app-layout>