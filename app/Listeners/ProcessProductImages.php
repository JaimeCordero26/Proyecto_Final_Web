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
            $externalImages = $product->images()->where('url', 'like', 'http%')->get();
            if ($externalImages->count() > 0) {
                Log::info("Eliminando {$externalImages->count()} imágenes externas del producto {$product->id}");
                $externalImages->each->delete();
            }

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
                        'url'        => $localPath,
                        'position'   => $position,
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
        if (is_string($imageData)) {
            return $imageData;
        }

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
            $context = stream_context_create([
                'http' => [
                    'method'          => 'GET',
                    'timeout'         => 15,
                    'user_agent'      => 'Mozilla/5.0 (compatible; Laravel/10.0)',
                    'follow_location' => true,
                    'max_redirects'   => 3,
                ],
            ]);

            $imageContent = @file_get_contents($imageUrl, false, $context);

            if ($imageContent === false) {
                Log::warning("No se pudo descargar la imagen: {$imageUrl}");
                return null;
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($imageContent);

            if (!str_starts_with($mimeType, 'image/')) {
                Log::warning("El contenido descargado no es imagen válida: {$imageUrl} (MIME: {$mimeType})");
                return null;
            }

            $extension = match ($mimeType) {
                'image/jpeg', 'image/jpg' => 'jpeg',
                'image/png'               => 'png',
                'image/webp'              => 'webp',
                'image/gif'               => 'gif',
                default                   => 'jpg',
            };

            $filename = $this->generateFileName($imageUrl, $position, $extension);
            $filePath = "products/{$productId}/{$filename}";

            Storage::disk('public')->put($filePath, $imageContent, 'public');

            Log::info("Imagen guardada: {$filePath} (Original: {$imageUrl}, MIME: {$mimeType})");

            return $filePath;

        } catch (\Exception $e) {
            Log::error("Error descargando imagen {$imageUrl}: " . $e->getMessage());
            return null;
        }
    }

    protected function generateFileName($imageUrl, $position, $extension = 'jpg')
    {
        $filename = basename(parse_url($imageUrl, PHP_URL_PATH) ?? '');
        $baseName = pathinfo($filename, PATHINFO_FILENAME);

        if (empty($baseName) || strlen($baseName) < 2) {
            $baseName = "image_{$position}";
        }

        $baseName = preg_replace('/[^a-zA-Z0-9._-]/', '', $baseName);

        return $baseName . '.' . strtolower($extension);
    }
}
