<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->paginate(12);
        return view('shop.index', compact('products'));
    }

    public function show(Product $product)
    {
        return view('shop.product', compact('product'));
    }
}
