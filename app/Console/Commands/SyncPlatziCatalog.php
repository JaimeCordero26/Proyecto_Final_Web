<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SyncPlatziCatalog extends Command
{
    protected $signature = 'platzi:sync 
                            {--download-images : Descarga imágenes al storage/app/public y reescribe URLs}
                            {--max=0 : Máximo de productos a sincronizar (0 = todos)}
                            {--dry-run : No guarda nada, solo simula}';

    protected $description = 'Sincroniza categorías, productos e imágenes desde la Platzi Fake Store API';

    public function handle(): int
    {
        $base = rtrim(config('services.platzi.base') ?? env('PLATZI_API_BASE', 'https://api.escuelajs.co/api/v1'), '/');
        $limit = (int) (env('PLATZI_API_PRODUCTS_LIMIT', 50));
        $download = (bool) $this->option('download-images');
        $dryRun = (bool) $this->option('dry-run');
        $max = (int) $this->option('max');

        $this->info("Base URL: {$base}");
        $this->info("Limit por página: {$limit}");
        $this->info("Descargar imágenes: " . ($download ? 'sí' : 'no'));
        $this->info("Dry-run: " . ($dryRun ? 'sí' : 'no'));
        $this->newLine();

        $this->syncCategories("{$base}/categories", $dryRun);

        $imported = $this->syncProducts("{$base}/products", $limit, $download, $dryRun, $max);

        $this->newLine();
        $this->info("Productos procesados: {$imported}");

        return self::SUCCESS;
    }

    protected function syncCategories(string $url, bool $dryRun = false): void
    {
        $this->line('> Sincronizando categorías...');

        $resp = Http::timeout(30)->retry(3, 500)->get($url);

        if (!$resp->successful()) {
            $this->error("Error al obtener categorías: {$resp->status()}");
            return;
        }

        $categories = $resp->json();
        if (!is_array($categories)) {
            $this->error("Respuesta de categorías inválida");
            return;
        }

        $count = 0;
        foreach ($categories as $cat) {

            $externalId = data_get($cat, 'id');
            $name = data_get($cat, 'name');
            $image = data_get($cat, 'image');

            if (!$externalId || !$name) {
                continue;
            }

            $payload = [
                'name' => $name,
                'image_url' => $image,
            ];

            if ($dryRun) {
                $this->line(" - [DRY] Category {$externalId} {$name}");
                $count++;
                continue;
            }

            Category::query()->updateOrCreate(
                ['external_id' => $externalId],
                $payload
            );
            $this->line(" - Category {$externalId} {$name}");
            $count++;
        }

        $this->info("Categorías sincronizadas: {$count}");
    }

    protected function syncProducts(string $url, int $limit, bool $download, bool $dryRun, int $max): int
    {
        $this->line('> Sincronizando productos...');

        $offset = 0;
        $totalImported = 0;

        while (true) {
            $pageUrl = $url . '?' . http_build_query([
                'limit' => $limit,
                'offset' => $offset,
            ]);

            $resp = Http::timeout(60)->retry(3, 800)->get($pageUrl);
            if (!$resp->successful()) {
                $this->warn(" - Página con error [offset={$offset}]: {$resp->status()}");
                break;
            }

            $items = $resp->json();
            if (!is_array($items) || count($items) === 0) {
                $this->line(" - No hay más productos.");
                break;
            }

            foreach ($items as $p) {
                $externalId = data_get($p, 'id');
                $title = data_get($p, 'title');
                $price = (float) data_get($p, 'price', 0);
                $description = data_get($p, 'description');
                $images = data_get($p, 'images', []);
                $catExtId = data_get($p, 'category.id');
                $raw = $p;

                if (!$externalId || !$title || !$catExtId) {
                    continue;
                }

                $category = Category::query()->where('external_id', $catExtId)->first();
                if (!$category) {
                    // fallback por nombre
                    $categoryName = data_get($p, 'category.name');
                    $category = Category::query()->firstOrCreate(
                        ['name' => $categoryName ?: 'Uncategorized'],
                        ['external_id' => $catExtId, 'image_url' => data_get($p, 'category.image')]
                    );
                }
                $stock = 50;

                if ($dryRun) {
                    $this->line(" - [DRY] Product {$externalId} {$title}");
                } else {
                    DB::transaction(function () use ($externalId, $title, $price, $description, $category, $images, $raw, $download) {
                        $product = Product::query()->updateOrCreate(
                            ['external_id' => $externalId],
                            [
                                'category_id' => $category->id,
                                'title' => $title,
                                'description' => $description,
                                'price' => $price,
                                'stock' => 50,
                                'raw_payload' => $raw,
                            ]
                        );

                        $this->syncImages($product->id, $images, $download);
                    });
                }

                $totalImported++;

                if ($max > 0 && $totalImported >= $max) {
                    $this->line(" - Alcanzado límite --max={$max}");
                    return $totalImported;
                }
            }

            $offset += $limit;
            usleep(200000); // 200ms
        }

        return $totalImported;
    }

    protected function syncImages(int $productId, array $images, bool $download): void
    {
        ProductImage::query()->where('product_id', $productId)->delete();

        $pos = 1;
        foreach ($images as $imgUrl) {
            $finalUrl = $imgUrl;

            if ($download && is_string($imgUrl) && Str::startsWith($imgUrl, ['http://', 'https://'])) {
                try {
                    $contents = Http::timeout(60)->retry(2, 500)->get($imgUrl)->body();
                    $ext = pathinfo(parse_url($imgUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'jpg';
                    $path = "products/{$productId}/".Str::padLeft((string)$pos, 2, '0').'.'.$ext;
                    Storage::disk('public')->put($path, $contents);
                    $finalUrl = Storage::url($path); // /storage/products/...
                } catch (\Throwable $e) {
                    Log::warning("No se pudo descargar imagen: {$imgUrl} => ".$e->getMessage());
                }
            }

            ProductImage::create([
                'product_id' => $productId,
                'url' => $finalUrl,
                'position' => $pos++,
            ]);
        }
    }
}