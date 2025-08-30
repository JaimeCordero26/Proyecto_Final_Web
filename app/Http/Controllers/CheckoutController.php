<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'El carrito está vacío.');
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $tax = $subtotal * 0.13;
        $shipping = 2500;
        $total = $subtotal + $tax + $shipping;

        return view('checkout.index', compact('cart', 'subtotal', 'tax', 'shipping', 'total'));
    }

    public function createSession(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Carrito vacío.');
        }

        // Crea la orden local en "pending"
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $tax = $subtotal * 0.13;
        $shipping = 2500;
        $total = $subtotal + $tax + $shipping;

        $order = Order::create([
            'user_id' => Auth::id() ?? 1,
            'status' => 'pending',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'payment_method' => 'stripe',
            'reference' => 'ORD' . time(),
        ]);

        // Prepara línea(s) para Stripe (en centavos)
        $lineItems = [];
        foreach ($cart as $pid => $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'crc', // o 'usd'
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    // Stripe trabaja en la unidad más pequeña (centavos).
                    // Si tus precios ya están en CRC enteros, multiplica por 100 solo si son decimales.
                    'unit_amount' => (int) round($item['price'] * 100),
                ],
                'quantity' => (int) $item['quantity'],
            ];
        }

        // Shipping como línea aparte (opcional)
        if ($shipping > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'crc',
                    'product_data' => ['name' => 'Envío'],
                    'unit_amount' => (int) round($shipping * 100),
                ],
                'quantity' => 1,
            ];
        }

        // Impuesto como línea aparte (simple). También podés usar tax rates de Stripe.
        if ($tax > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'crc',
                    'product_data' => ['name' => 'Impuesto (13%)'],
                    'unit_amount' => (int) round($tax * 100),
                ],
                'quantity' => 1,
            ];
        }

        Stripe::setApiKey(config('services.stripe.secret', env('STRIPE_SECRET')));

        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => route('cart.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('cart.checkout.cancel'),
            // Guardamos order_id para identificar en el webhook
            'metadata' => [
                'order_id' => (string) $order->id,
            ],
        ]);

        // Guarda los items de la orden (snapshot) para tu historial
        foreach ($cart as $pid => $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => (int) $pid,
                'title' => $item['name'],
                'unit_price' => $item['price'],
                'quantity' => $item['quantity'],
                'line_total' => $item['price'] * $item['quantity'],
            ]);
        }

        // Limpia carrito en sesión, dejá la orden pendiente hasta el webhook
        session()->forget('cart');

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        // La confirmación "real" la hace el webhook (estado = paid).
        return view('checkout.success');
    }

    public function cancel()
    {
        return redirect()->route('cart.index')->with('error', 'Pago cancelado.');
    }
}
