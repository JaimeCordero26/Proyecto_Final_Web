<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'url', 'position'];

    protected function fullUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getFullUrl()
        );
    }

    public function getFullUrl()
    {

        if (str_starts_with($this->url, 'http')) {
            return $this->url;
        }

        return Storage::disk('public')->url($this->url);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}