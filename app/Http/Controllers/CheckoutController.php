<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'El carrito está vacío.');
        }

        [$subtotal, $tax, $shipping, $total] = $this->totalsFromCart($cart);

        return view('checkout.index', compact('cart','subtotal','tax','shipping','total'));
    }

    public function createOrder(Request $request)
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('error', 'Carrito vacío.');
        }

        [$subtotal, $tax, $shipping, $total] = $this->totalsFromCart($cart);

        $order = Order::create([
            'user_id'        => Auth::id() ?? 1,
            'status'         => 'pending',
            'subtotal'       => $subtotal,
            'tax'            => $tax,
            'shipping'       => $shipping,
            'total'          => $total,
            'payment_method' => 'fake',
            'reference'      => 'ORD' . now()->timestamp,
        ]);

        foreach ($cart as $pid => $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => (int) $pid,
                'title'      => $item['name'] ?? $item['title'] ?? 'Producto',
                'unit_price' => $item['price'],
                'quantity'   => $item['quantity'],
                'line_total' => $item['price'] * $item['quantity'],
            ]);
        }

        if (Auth::check()) {
            Cart::where('user_id', Auth::id())->delete();
        } else {
            session()->forget('cart');
        }

        session()->put('last_order_id', $order->id);

        return redirect()->route('cart.checkout.pay');
    }

    public function payForm()
    {
        $order = Order::find(session('last_order_id'));

        if (! $order) {
            return redirect()->route('cart.checkout')->with('error', 'No hay pedido para pagar.');
        }

        if ($order->status !== 'pending') {
            return redirect()->route('cart.checkout.success');
        }

        return view('checkout.pay', compact('order'));
    }

    public function processFakePayment(Request $request)
    {
        $order = Order::find(session('last_order_id'));

        if (! $order || $order->status !== 'pending') {
            return redirect()->route('shop.index')->with('error', 'Pedido inválido.');
        }

        $validated = $request->validate([
            'card_name'   => ['required','string','max:100'],
            'card_number' => ['required','digits_between:13,19'],
            'exp_month'   => ['required','integer','between:1,12'],
            'exp_year'    => ['required','integer','min:' . now()->year, 'max:' . (now()->year + 10)],
            'cvc'         => ['required','digits_between:3,4'],
        ]);

        $lastDigit = substr($validated['card_number'], -1);
        $approved = ((int) $lastDigit % 2) === 0;

        if (! $approved) {
            return redirect()->route('cart.checkout.failed')
                ->with('error', 'Pago rechazado (simulado). Intenta con otra tarjeta.');
        }

        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->decrement('stock', $item->quantity);
            }
        }

        $order->update(['status' => 'paid']);

        session()->forget('last_order_id');

        return redirect()->route('cart.checkout.success')->with('success', 'Pago aprobado (simulado).');
    }

    public function success() { return view('checkout.success'); }
    public function failed()  { return view('checkout.failed');  }
    public function cancel()  { return redirect()->route('cart.index')->with('error', 'Pago cancelado.'); }

    private function getCart(): array
    {
        if (Auth::check()) {
            $items = Cart::with(['product.images' => fn($q) => $q->orderBy('position')])
                ->where('user_id', Auth::id())
                ->get();

            $cart = [];
            foreach ($items as $i) {
                if (! $i->product) continue;
                $cart[$i->product_id] = [
                    'name'     => $i->product->title,
                    'price'    => $i->product->price,
                    'quantity' => $i->quantity,
                    'image'    => optional($i->product->images->first())->url,
                ];
            }
            return $cart;
        }

        return session()->get('cart', []);
    }

    private function totalsFromCart(array $cart): array
    {
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $tax      = round($subtotal * 0.13, 2);
        $shipping = 2500;
        $total    = $subtotal + $tax + $shipping;
        return [$subtotal, $tax, $shipping, $total];
    }
}
