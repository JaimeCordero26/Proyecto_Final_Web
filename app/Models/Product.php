<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['external_id','category_id','title','description','price','stock','raw_payload'];
    protected $casts = ['raw_payload' => 'array'];


public function category()
{
    return $this->belongsTo(Category::class)->withDefault([
        'name' => 'Sin categoría'
    ]);
}
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
      public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
    
}
