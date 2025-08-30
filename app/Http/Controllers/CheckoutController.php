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

    public function pay(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío.');
        }

        if ($request->get('payment_method') === 'card') {
            // Lógica para Stripe (tarjeta)
            return $this->processStripePayment($cart, $request->get('total_amount'));
        }

        if ($request->get('payment_method') === 'paypal') {
            // Lógica para PayPal (aún no implementada)
            return redirect()->route('shop.index')->with('success', '¡Pago con PayPal en desarrollo!');
        }

        return redirect()->route('checkout.index')->with('error', 'Método de pago no válido.');
    }

    private function processStripePayment($cart, $total)
    {
        Stripe::setApiKey(config('services.stripe.secret', env('STRIPE_SECRET')));

        $lineItems = [];
        foreach ($cart as $id => $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => (int) round($item['price'] * 100),
                ],
                'quantity' => (int) $item['quantity'],
            ];
        }

        $session = CheckoutSession::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.cancel'),
        ]);

        return redirect($session->url, 303);
    }
    
    public function success(Request $request)
    {
        return view('checkout.success');
    }

    public function cancel()
    {
        return redirect()->route('cart.index')->with('error', 'Pago cancelado.');
    }
}