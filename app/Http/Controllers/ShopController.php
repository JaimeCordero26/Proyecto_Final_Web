<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Events\ProductViewed;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'images'])->paginate(12);
        foreach ($products as $product) {
            event(new ProductViewed($product));
            $product->load('images');
        }
        
        return view('shop.index', compact('products'));
    }

    public function show(Product $product)
    {
        event(new ProductViewed($product));
        $product->load('images');
        
        return view('shop.product', compact('product'));
    }
}