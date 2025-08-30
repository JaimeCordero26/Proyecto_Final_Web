<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    /**
     * Mostrar la página de checkout
     */
    public function show()
    {
        // Obtener el carrito según el tipo de usuario (igual que CartController)
        if (Auth::check()) {
            // Usuario autenticado - carrito desde BD
            $cartItems = \App\Models\Cart::with('product.images')
                ->where('user_id', Auth::id())
                ->get();
            
            $cart = [];
            foreach ($cartItems as $item) {
                $cart[$item->product_id] = [
                    "name" => $item->product->name,
                    "quantity" => $item->quantity,
                    "price" => $item->product->price,
                    "image" => $item->product->images->first()->url ?? null,
                    "product" => $item->product
                ];
            }
        } else {
            // Usuario guest - carrito desde session
            $cart = session('cart', []);
        }
        
        // Verificar que el carrito no esté vacío
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }
        
        // Calcular totales
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $tax = $subtotal * 0.13; // 13% impuesto en Costa Rica
        $shipping = 2000; // ₡2000 de envío (puedes hacer esto dinámico)
        $total = $subtotal + $tax + $shipping;
        
        return view('checkout.index', compact('cart', 'subtotal', 'tax', 'shipping', 'total'));
    }
    
    /**
     * Procesar el pago (método POST)
     */
    public function process(Request $request)
    {
        // Validar datos del formulario
        $request->validate([
            'cart_data' => 'required',
            'total' => 'required|numeric|min:0'
        ]);
        
        // Obtener carrito según el tipo de usuario
        if (Auth::check()) {
            // Usuario autenticado - carrito desde BD
            $cartItems = \App\Models\Cart::with('product')->where('user_id', Auth::id())->get();
            $cart = [];
            foreach ($cartItems as $item) {
                $cart[$item->product_id] = [
                    "name" => $item->product->name,
                    "quantity" => $item->quantity,
                    "price" => $item->product->price,
                    "product" => $item->product
                ];
            }
        } else {
            // Usuario guest - carrito desde session
            $cart = session('cart', []);
        }
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }
        
        // Aquí procesarías el pago con tu gateway de pago
        // Por ahora simulamos un pago exitoso
        
        // Limpiar el carrito después del pago exitoso
        if (Auth::check()) {
            \App\Models\Cart::where('user_id', Auth::id())->delete();
        } else {
            session()->forget('cart');
        }
        
        return redirect()->route('checkout.success')->with('success', 'Pago procesado exitosamente');
    }
    
    /**
     * Método alternativo para el pago (si prefieres usar este nombre)
     */
    public function pay(Request $request)
    {
        // Obtener carrito según el tipo de usuario
        if (Auth::check()) {
            // Usuario autenticado - carrito desde BD
            $cartItems = \App\Models\Cart::with('product')->where('user_id', Auth::id())->get();
            $cart = [];
            foreach ($cartItems as $item) {
                $cart[$item->product_id] = [
                    "name" => $item->product->name,
                    "quantity" => $item->quantity,
                    "price" => $item->product->price,
                    "product" => $item->product
                ];
            }
        } else {
            // Usuario guest - carrito desde session
            $cart = session('cart', []);
        }
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }
        
        // Redirigir al checkout normal
        return redirect()->route('checkout.show');
    }
    
    /**
     * Página de éxito después del pago
     */
    public function success()
    {
        return view('checkout.success');
    }
}