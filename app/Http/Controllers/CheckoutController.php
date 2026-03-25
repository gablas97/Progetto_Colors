<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private const SHIPPING_COST     = 5.90;
    private const FREE_SHIPPING_MIN = 50.00;

    public function index()
    {
        $cart = $this->resolveCart();

        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Il carrello è vuoto.');
        }

        $cart->load(['items.product', 'items.productVariant']);

        $subtotal     = $cart->subtotal;
        $shippingCost = $subtotal >= self::FREE_SHIPPING_MIN ? 0 : self::SHIPPING_COST;
        $total        = $subtotal + $shippingCost;

        $defaultAddress = null;
        if (auth()->check()) {
            $defaultAddress = auth()->user()
                ->addresses()
                ->where('type', '!=', 'billing')
                ->where('is_default', true)
                ->first()
                ?? auth()->user()->addresses()->where('type', '!=', 'billing')->first();
        }

        return view('checkout.index', compact('cart', 'subtotal', 'shippingCost', 'total', 'defaultAddress'));
    }

    public function store(Request $request)
    {
        $cart = $this->resolveCart();

        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Il carrello è vuoto.');
        }

        $cart->load(['items.product', 'items.productVariant']);

        $rules = [
            'shipping_first_name'  => ['required', 'string', 'max:100'],
            'shipping_last_name'   => ['required', 'string', 'max:100'],
            'shipping_company'     => ['nullable', 'string', 'max:150'],
            'shipping_address'     => ['required', 'string', 'max:255'],
            'shipping_city'        => ['required', 'string', 'max:100'],
            'shipping_province'    => ['required', 'string', 'size:2'],
            'shipping_postal_code' => ['required', 'string', 'max:10'],
            'shipping_phone'       => ['nullable', 'string', 'max:30'],
            'notes'                => ['nullable', 'string', 'max:1000'],
        ];

        if (!auth()->check()) {
            $rules['guest_email'] = ['required', 'email', 'max:255'];
        }

        $data = $request->validate($rules);

        $subtotal     = $cart->subtotal;
        $shippingCost = $subtotal >= self::FREE_SHIPPING_MIN ? 0 : self::SHIPPING_COST;
        $total        = $subtotal + $shippingCost;

        $orderId = null;

        DB::transaction(function () use ($data, $cart, $subtotal, $shippingCost, $total, &$orderId) {
            $order = Order::create([
                'user_id'              => auth()->id(),
                'guest_email'          => auth()->check() ? null : $data['guest_email'],
                'shipping_first_name'  => $data['shipping_first_name'],
                'shipping_last_name'   => $data['shipping_last_name'],
                'shipping_company'     => $data['shipping_company'] ?? null,
                'shipping_address'     => $data['shipping_address'],
                'shipping_city'        => $data['shipping_city'],
                'shipping_province'    => strtoupper($data['shipping_province']),
                'shipping_postal_code' => $data['shipping_postal_code'],
                'shipping_country'     => 'IT',
                'shipping_phone'       => $data['shipping_phone'] ?? null,
                'billing_same_as_shipping' => true,
                'subtotal'             => $subtotal,
                'discount_amount'      => 0,
                'shipping_cost'        => $shippingCost,
                'tax_amount'           => 0,
                'total'                => $total,
                'payment_method'       => 'pending',
                'payment_status'       => 'pending',
                'status'               => 'pending',
                'notes'                => $data['notes'] ?? null,
                'source'               => 'web',
            ]);

            foreach ($cart->items as $item) {
                $price = $item->product->price;
                $order->items()->create([
                    'product_id'         => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name'       => $item->product->name,
                    'product_sku'        => $item->productVariant?->sku ?? $item->product->sku,
                    'variant_name'       => $item->productVariant?->name,
                    'product_image'      => $item->product->main_image,
                    'price'              => $price,
                    'quantity'           => $item->quantity,
                    'vat_rate'           => 22,
                    'subtotal'           => $price * $item->quantity,
                    'tax_amount'         => 0,
                    'total'              => $price * $item->quantity,
                ]);
            }

            $cart->clear();
            $orderId = $order->id;
        });

        session(['last_order_id' => $orderId]);

        return redirect()->route('checkout.success');
    }

    public function success()
    {
        $orderId = session('last_order_id');
        if (!$orderId) return redirect()->route('home');

        $order = Order::with('items')->findOrFail($orderId);

        // Sicurezza: solo il proprietario o ospite con ordine in sessione può vederlo
        if ($order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }

    private function resolveCart(): ?Cart
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())->first();
        }

        $sessionId = session('cart_session_id');
        if (!$sessionId) return null;

        return Cart::where('session_id', $sessionId)->first();
    }
}
