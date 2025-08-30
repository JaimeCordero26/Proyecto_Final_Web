<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $cartItems = Cart::with('product.images')
                ->where('user_id', Auth::id())
                ->get();
            
            $cart = [];
            foreach ($cartItems as $item) {
                $cart[$item->product_id] = [
                    "name" => $item->product->title,
                    "quantity" => $item->quantity,
                    "price" => $item->product->price,
                    "image" => $item->product->images->first()->url ?? null,
                    "product" => $item->product
                ];
            }
        } else {
            $cart = session()->get('cart', []);
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $tax = $subtotal * 0.13;
        $shipping = 2500;
        $total = $subtotal + $tax + $shipping;

        return view('cart.index', compact('cart', 'subtotal', 'tax', 'shipping', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        if (Auth::check()) {
            // Usuario autenticado - guardar en BD
            Cart::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'product_id' => $product->id
                ],
                [
                    'quantity' => DB::raw('quantity + 1')
                ]
            );
            
            $message = 'Producto agregado al carrito!';
        } else {
            $cart = session()->get('cart', []);

            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity']++;
            } else {
                $cart[$product->id] = [
                    "name" => $product->title,
                    "quantity" => 1,
                    "price" => $product->price,
                    "image" => $product->images->first()->url ?? null,
                    "product" => $product
                ];
            }

            session()->put('cart', $cart);
            $message = 'Producto agregado al carrito!';
        }

        return redirect()->route('cart.index')->with('success', $message);
    }

    public function remove(Product $product)
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->delete();
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$product->id])) {
                unset($cart[$product->id]);
                session()->put('cart', $cart);
            }
        }

        return redirect()->route('cart.index')->with('success', 'Producto eliminado del carrito');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if (Auth::check()) {
            if ($request->quantity == 0) {
                Cart::where('user_id', Auth::id())
                    ->where('product_id', $product->id)
                    ->delete();
            } else {
                Cart::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'product_id' => $product->id
                    ],
                    [
                        'quantity' => $request->quantity
                    ]
                );
            }
        } else {
            $cart = session()->get('cart', []);
            if (isset($cart[$product->id])) {
                if ($request->quantity == 0) {
                    unset($cart[$product->id]);
                } else {
                    $cart[$product->id]['quantity'] = $request->quantity;
                }
                session()->put('cart', $cart);
            }
        }

        return redirect()->route('cart.index')->with('success', 'Carrito actualizado');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        
        if (Auth::check()) {
            $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();
            $cart = [];
            foreach ($cartItems as $item) {
                $cart[$item->product_id] = [
                    "quantity" => $item->quantity,
                    "price" => $item->product->price,
                    "product" => $item->product
                ];
            }
        }

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'El carrito está vacío');
        }

        return view('checkout.index', compact('cart'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'phone' => 'required|string',
            'payment_method' => 'required|in:cash,card'
        ]);

        $cart = [];
        if (Auth::check()) {
            $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();
            foreach ($cartItems as $item) {
                $cart[$item->product_id] = [
                    "quantity" => $item->quantity,
                    "price" => $item->product->price,
                    "product" => $item->product
                ];
            }
        } else {
            $cart = session()->get('cart', []);
        }

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'El carrito está vacío.');
        }
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $tax = $subtotal * 0.13;
        $shipping = 2500;
        $total = $subtotal + $tax + $shipping;
        $order = Order::create([
            'user_id' => Auth::id(),
            'customer_name' => $request->name,
            'customer_email' => $request->email,
            'customer_address' => $request->address,
            'customer_city' => $request->city,
            'customer_phone' => $request->phone,
            'status' => 'pending',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'payment_method' => $request->payment_method,
            'reference' => 'ORD' . time() . rand(1000, 9999),
        ]);

        foreach ($cart as $productId => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity']
            ]);
        }

        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            session()->forget('cart');
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', '¡Pedido realizado con éxito! Número de orden: ' . $order->reference);
    }

    public function clear()
    {
        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            session()->forget('cart');
        }

        return redirect()->route('cart.index')->with('success', 'Carrito vaciado');
    }
}