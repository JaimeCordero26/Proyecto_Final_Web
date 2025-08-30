<?php
namespace App\Listeners;

use App\Events\ProductViewed;
use App\Models\ProductImage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessProductImages
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(ProductViewed $event): void
    {
        $product = $event->product;
        
        $needsProcessing = $product->images()->count() === 0 || 
                          $product->images()->where('url', 'like', 'http%')->exists();
        
        if ($needsProcessing) {
            $this->processProductImages($product);
        }
    }

    protected function processProductImages($product)
    {
        try {
            // Si ya tiene imágenes externas, eliminarlas primero
            $externalImages = $product->images()->where('url', 'like', 'http%')->get();
            if ($externalImages->count() > 0) {
                Log::info("Eliminando {$externalImages->count()} imágenes externas del producto {$product->id}");
                $externalImages->each->delete();
            }

            // Procesar desde raw_payload
            $this->createImagesFromRawPayload($product);
            
        } catch (\Exception $e) {
            Log::error("Error procesando imágenes del producto {$product->id}: " . $e->getMessage());
        }
    }

    protected function createImagesFromRawPayload($product)
    {
        try {
            $rawPayload = $product->raw_payload;
            
            if (!isset($rawPayload['images']) || !is_array($rawPayload['images'])) {
                Log::info("Producto {$product->id} no tiene imágenes en raw_payload");
                return;
            }

            $position = 1;
            foreach ($rawPayload['images'] as $imageData) {
                $imageUrl = $this->extractImageUrl($imageData);
                
                if (!$imageUrl || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    Log::warning("URL de imagen inválida en producto {$product->id}: " . json_encode($imageData));
                    continue;
                }

                $localPath = $this->downloadAndStoreImage($imageUrl, $product->id, $position);
                
                if ($localPath) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $localPath,
                        'position' => $position
                    ]);
                    
                    Log::info("Imagen descargada para producto {$product->id}: {$localPath}");
                }
                
                $position++;
            }
            
        } catch (\Exception $e) {
            Log::error("Error procesando raw_payload del producto {$product->id}: " . $e->getMessage());
        }
    }

    protected function extractImageUrl($imageData)
    {
        // Si es string, es la URL directa
        if (is_string($imageData)) {
            return $imageData;
        }
        
        // Si es array, buscar la URL en las claves posibles
        if (is_array($imageData)) {
            $possibleKeys = ['url', 'src', 'image', 'path', 'link', 'href'];
            
            foreach ($possibleKeys as $key) {
                if (isset($imageData[$key]) && is_string($imageData[$key])) {
                    return $imageData[$key];
                }
            }
        }
        
        return null;
    }

    protected function downloadAndStoreImage($imageUrl, $productId, $position)
    {
        try {
            // Configurar contexto con timeout y user agent
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 15,
                    'user_agent' => 'Mozilla/5.0 (compatible; Laravel/10.0)',
                    'follow_location' => true,
                    'max_redirects' => 3
                ]
            ]);

            $imageContent = @file_get_contents($imageUrl, false, $context);
            
            if ($imageContent === false) {
                Log::warning("No se pudo descargar la imagen: {$imageUrl}");
                return null;
            }

            // Verificar que el contenido sea realmente una imagen
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($imageContent);
            
            if (!str_starts_with($mimeType, 'image/')) {
                Log::warning("El contenido descargado no es una imagen válida: {$imageUrl} (MIME: {$mimeType})");
                return null;
            }

            // IMPORTANTE: Normalizar SIEMPRE a .jpg
            $filename = $this->generateFileName($imageUrl, $position);
            $filePath = "products/{$productId}/{$filename}";

            // Guardar usando Storage
            Storage::disk('public')->put($filePath, $imageContent);
            
            Log::info("Imagen guardada: {$filePath} (Original: {$imageUrl})");
            
            return $filePath;
            
        } catch (\Exception $e) {
            Log::error("Error descargando imagen {$imageUrl}: " . $e->getMessage());
            return null;
        }
    }

    protected function generateFileName($imageUrl, $position)
    {
        // Obtener el nombre base del archivo
        $filename = basename($imageUrl);
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        
        // Si no hay nombre base válido, usar posición
        if (empty($baseName) || strlen($baseName) < 2) {
            $baseName = "image_{$position}";
        }
        
        // Limpiar el nombre (solo caracteres alfanuméricos y algunos especiales)
        $baseName = preg_replace('/[^a-zA-Z0-9._-]/', '', $baseName);
        
        // SIEMPRE usar extensión .jpg
        return $baseName . '.jpg';
    }
}