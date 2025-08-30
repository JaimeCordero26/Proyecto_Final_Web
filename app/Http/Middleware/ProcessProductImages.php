<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Log;

class ProcessProductImages
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si estamos en la ruta del shop
        if ($request->route()->getName() === 'shop.index') {
            $this->processMissingImages();
        }

        return $next($request);
    }

    protected function processMissingImages()
    {
        // Obtener productos sin imágenes en la base de datos
        $productsWithoutImages = Product::whereDoesntHave('images')->get();

        foreach ($productsWithoutImages as $product) {
            $this->createImagesFromRawPayload($product);
        }
    }

    protected function createImagesFromRawPayload(Product $product)
    {
        try {
            $rawPayload = $product->raw_payload;
            
            if (isset($rawPayload['images']) && is_array($rawPayload['images'])) {
                $position = 1;
                
                foreach ($rawPayload['images'] as $imageUrl) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $imageUrl,
                        'position' => $position++
                    ]);
                }
                
                Log::info("Imágenes procesadas para producto: {$product->id}");
            }
        } catch (\Exception $e) {
            Log::error("Error procesando imágenes para producto {$product->id}: " . $e->getMessage());
        }
    }
}