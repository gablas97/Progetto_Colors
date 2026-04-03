<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Discount;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Checkout\Session as StripeSession;

class CheckoutController extends Controller
{
    private const SHIPPING_COST     = 5.90;
    private const FREE_SHIPPING_MIN = 50.00;

    // =========================================================================
    // Pagina checkout
    // =========================================================================

    public function index()
    {
        $cart = $this->resolveCart();

        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Il carrello è vuoto.');
        }

        $cart->load(['items.product', 'items.productVariant']);

        $subtotal     = $cart->subtotal;
        $shippingCost = $subtotal >= self::FREE_SHIPPING_MIN ? 0 : self::SHIPPING_COST;
        $discount     = $this->resolveSessionDiscount($subtotal, $shippingCost);
        $total        = $subtotal + $shippingCost - $discount['amount'];

        $defaultAddress   = null;
        $billingAddresses = collect();
        if (auth()->check()) {
            $user = auth()->user();
            $defaultAddress = $user->addresses()
                ->where('type', '!=', 'billing')
                ->where('is_default', true)
                ->first()
                ?? $user->addresses()->where('type', '!=', 'billing')->first();

            $billingAddresses = $user->addresses()
                ->whereIn('type', ['billing', 'both'])
                ->get();
        }

        return view('checkout.index', compact('cart', 'subtotal', 'shippingCost', 'total', 'defaultAddress', 'discount', 'billingAddresses'));
    }

    // =========================================================================
    // AJAX: valida codice sconto
    // =========================================================================

    public function applyDiscount(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);

        $cart = $this->resolveCart();
        if (!$cart || $cart->isEmpty()) {
            return response()->json(['valid' => false, 'message' => 'Carrello vuoto.'], 422);
        }

        $subtotal = $cart->subtotal;
        $shippingCost = $subtotal >= self::FREE_SHIPPING_MIN ? 0 : self::SHIPPING_COST;

        $discount = Discount::active()->byCode($request->code)->first();

        if (!$discount) {
            return response()->json(['valid' => false, 'message' => 'Codice sconto non valido.']);
        }

        if (!$discount->canApplyToOrder($subtotal, auth()->id())) {
            if ($discount->min_order_amount && $subtotal < $discount->min_order_amount) {
                return response()->json([
                    'valid'   => false,
                    'message' => 'Ordine minimo per questo codice: € ' . number_format($discount->min_order_amount, 2, ',', '.'),
                ]);
            }
            return response()->json(['valid' => false, 'message' => 'Codice sconto non applicabile.']);
        }

        $discountAmount = $discount->type === 'shipping'
            ? $shippingCost
            : $discount->calculateDiscount($subtotal);

        $newTotal = $subtotal + $shippingCost - $discountAmount;

        session(['checkout_discount' => [
            'code'   => strtoupper($request->code),
            'amount' => $discountAmount,
            'label'  => $this->discountLabel($discount),
        ]]);

        return response()->json([
            'valid'           => true,
            'code'            => strtoupper($request->code),
            'discount_amount' => number_format($discountAmount, 2, ',', '.'),
            'new_total'       => number_format($newTotal, 2, ',', '.'),
            'label'           => $this->discountLabel($discount),
        ]);
    }

    public function removeDiscount()
    {
        session()->forget('checkout_discount');
        return response()->json(['ok' => true]);
    }

    // =========================================================================
    // Creazione ordine e redirect al pagamento
    // =========================================================================

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
            'payment_method'       => ['required', 'in:stripe,paypal,satispay'],
        ];

        if (!auth()->check()) {
            $rules['guest_email'] = ['required', 'email', 'max:255'];
        }

        $billingDifferent  = $request->boolean('billing_different');
        $usingSavedBilling = $billingDifferent
            && $request->filled('billing_saved_id')
            && $request->input('billing_saved_id') !== 'new';

        if ($billingDifferent && !$usingSavedBilling) {
            $rules['billing_first_name']  = ['required', 'string', 'max:100'];
            $rules['billing_last_name']   = ['required', 'string', 'max:100'];
            $rules['billing_company']     = ['nullable', 'string', 'max:150'];
            $rules['billing_vat_number']  = ['nullable', 'string', 'max:30'];
            $rules['billing_tax_code']    = ['nullable', 'string', 'max:20'];
            $rules['billing_address']     = ['required', 'string', 'max:255'];
            $rules['billing_city']        = ['required', 'string', 'max:100'];
            $rules['billing_province']    = ['required', 'string', 'size:2'];
            $rules['billing_postal_code'] = ['required', 'string', 'max:10'];
            $rules['billing_sdi_code']    = ['nullable', 'string', 'max:7'];
            $rules['billing_pec']         = ['nullable', 'email', 'max:255'];
        }

        $data = $request->validate($rules);

        // Risolvi indirizzo di fatturazione da salvato se necessario
        $savedBillingAddr = null;
        if ($usingSavedBilling) {
            $savedBillingAddr = Address::where('id', $request->input('billing_saved_id'))
                ->where('user_id', auth()->id())
                ->whereIn('type', ['billing', 'both'])
                ->first();
            abort_unless($savedBillingAddr, 422);
        }

        $subtotal     = $cart->subtotal;
        $shippingCost = $subtotal >= self::FREE_SHIPPING_MIN ? 0 : self::SHIPPING_COST;
        $discount     = $this->resolveSessionDiscount($subtotal, $shippingCost);
        $total        = $subtotal + $shippingCost - $discount['amount'];

        $orderId = null;

        DB::transaction(function () use ($data, $cart, $subtotal, $shippingCost, $discount, $total, $billingDifferent, $savedBillingAddr, &$orderId) {
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
                'billing_same_as_shipping' => !$billingDifferent,
                'billing_first_name'       => $billingDifferent ? ($savedBillingAddr?->first_name  ?? $data['billing_first_name'])  : null,
                'billing_last_name'        => $billingDifferent ? ($savedBillingAddr?->last_name   ?? $data['billing_last_name'])   : null,
                'billing_company'          => $billingDifferent ? ($savedBillingAddr?->company     ?? ($data['billing_company'] ?? null))    : null,
                'billing_vat_number'       => $billingDifferent ? ($savedBillingAddr?->vat_number  ?? ($data['billing_vat_number'] ?? null))  : null,
                'billing_tax_code'         => $billingDifferent ? ($savedBillingAddr?->tax_code    ?? ($data['billing_tax_code'] ?? null))    : null,
                'billing_address'          => $billingDifferent ? ($savedBillingAddr?->address     ?? $data['billing_address'])    : null,
                'billing_city'             => $billingDifferent ? ($savedBillingAddr?->city        ?? $data['billing_city'])       : null,
                'billing_province'         => $billingDifferent ? strtoupper($savedBillingAddr?->province ?? $data['billing_province']) : null,
                'billing_postal_code'      => $billingDifferent ? ($savedBillingAddr?->postal_code ?? $data['billing_postal_code']) : null,
                'billing_country'          => $billingDifferent ? 'IT' : null,
                'billing_sdi_code'         => $billingDifferent ? ($savedBillingAddr?->sdi_code    ?? ($data['billing_sdi_code'] ?? null))    : null,
                'billing_pec'              => $billingDifferent ? ($savedBillingAddr?->pec         ?? ($data['billing_pec'] ?? null))         : null,
                'subtotal'             => $subtotal,
                'discount_amount'      => $discount['amount'],
                'discount_code'        => $discount['code'],
                'shipping_cost'        => $shippingCost,
                'tax_amount'           => 0,
                'total'                => $total,
                'payment_method'       => $data['payment_method'],
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

        // Applica l'uso del codice sconto (fuori dalla transazione per evitare lock)
        if ($discount['code']) {
            $discountModel = Discount::active()->byCode($discount['code'])->first();
            $discountModel?->incrementUsage(auth()->id(), $orderId);
        }

        session()->forget('checkout_discount');

        $order = Order::findOrFail($orderId);

        return match($data['payment_method']) {
            'stripe'   => $this->redirectToStripe($order),
            'satispay' => $this->redirectToSatispay($order),
            default    => $this->redirectToPayPal($order),
        };
    }

    // =========================================================================
    // Stripe
    // =========================================================================

    private function redirectToStripe(Order $order)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => ['name' => $item->product_name . ($item->variant_name ? ' – ' . $item->variant_name : '')],
                    'unit_amount'  => (int) round($item->price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        }

        if ($order->shipping_cost > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => ['name' => 'Spedizione'],
                    'unit_amount'  => (int) round($order->shipping_cost * 100),
                ],
                'quantity' => 1,
            ];
        }

        if ($order->discount_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'eur',
                    'product_data' => ['name' => 'Sconto (' . $order->discount_code . ')'],
                    'unit_amount'  => -(int) round($order->discount_amount * 100),
                ],
                'quantity' => 1,
            ];
        }

        $session = StripeSession::create([
            'mode'                => 'payment',
            'payment_method_types' => ['card'],
            'line_items'          => $lineItems,
            'metadata'            => ['order_id' => $order->id],
            'success_url'         => route('checkout.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'          => route('checkout.index'),
        ]);

        session(['stripe_session_order_' . $session->id => $order->id]);

        return redirect($session->url);
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) return redirect()->route('home');

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = StripeSession::retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect()->route('checkout.index')->with('error', 'Pagamento non completato. Riprova.');
        }

        $orderId = $session->metadata->order_id ?? session('stripe_session_order_' . $sessionId);
        if (!$orderId) return redirect()->route('home');

        $order = Order::findOrFail($orderId);

        if (!$order->isPaid()) {
            $order->markAsPaid($session->payment_intent);
            $order->update(['status' => 'processing']);
        }

        session()->forget('stripe_session_order_' . $sessionId);
        session(['last_order_id' => $order->id]);

        return redirect()->route('checkout.success');
    }

    // =========================================================================
    // PayPal
    // =========================================================================

    private function redirectToPayPal(Order $order)
    {
        $provider = new PayPalClient(config('paypal'));
        $provider->getAccessToken();

        $paypalOrder = $provider->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => $order->order_number,
                'description'  => 'Ordine Colors S.r.l.',
                'amount'       => [
                    'currency_code' => 'EUR',
                    'value'         => number_format($order->total, 2, '.', ''),
                ],
            ]],
            'application_context' => [
                'return_url' => route('checkout.paypal.success'),
                'cancel_url' => route('checkout.index'),
                'brand_name' => 'Colors S.r.l.',
                'locale'     => 'it-IT',
            ],
        ]);

        if (isset($paypalOrder['id'])) {
            session(['paypal_order_' . $paypalOrder['id'] => $order->id]);

            $approvalLink = collect($paypalOrder['links'])
                ->firstWhere('rel', 'approve')['href'] ?? null;

            if ($approvalLink) {
                return redirect($approvalLink);
            }
        }

        // Fallback: se PayPal non risponde correttamente
        $order->update(['payment_method' => 'pending', 'payment_status' => 'failed']);
        return redirect()->route('checkout.index')->with('error', 'Errore PayPal. Riprova o scegli un altro metodo di pagamento.');
    }

    public function paypalSuccess(Request $request)
    {
        $token = $request->query('token');
        if (!$token) return redirect()->route('home');

        $orderId = session('paypal_order_' . $token);
        if (!$orderId) return redirect()->route('home');

        $provider = new PayPalClient(config('paypal'));
        $provider->getAccessToken();

        $result = $provider->capturePaymentOrder($token);

        if (($result['status'] ?? '') !== 'COMPLETED') {
            return redirect()->route('checkout.index')->with('error', 'Pagamento PayPal non completato. Riprova.');
        }

        $order = Order::findOrFail($orderId);

        if (!$order->isPaid()) {
            $captureId = $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
            $order->markAsPaid($captureId);
            $order->update(['status' => 'processing']);
        }

        session()->forget('paypal_order_' . $token);
        session(['last_order_id' => $order->id]);

        return redirect()->route('checkout.success');
    }

    // =========================================================================
    // Satispay
    // =========================================================================

    private function redirectToSatispay(Order $order)
    {
        $config = config('services.satispay');

        \SatispayGBusiness\Api::setSandbox((bool) $config['sandbox']);
        \SatispayGBusiness\Api::setPublicKey($config['public_key']);
        \SatispayGBusiness\Api::setPrivateKey($config['private_key']);
        \SatispayGBusiness\Api::setKeyId($config['key_id']);

        $payment = \SatispayGBusiness\Payment::create([
            'flow'             => 'MATCH_USER',
            'amount_unit'      => (int) round($order->total * 100),
            'currency'         => 'EUR',
            'redirect_url'     => route('checkout.satispay.success') . '?order_id=' . $order->id,
            'callback_url'     => route('checkout.satispay.success') . '?order_id=' . $order->id,
            'expiration_date'  => now()->addMinutes(30)->toIso8601String(),
            'metadata'         => ['order_id' => $order->id],
        ]);

        session(['satispay_payment_' . $payment->id => $order->id]);

        return redirect($payment->redirect_url);
    }

    public function satispaySuccess(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $orderId   = $request->query('order_id')
            ?? session('satispay_payment_' . $paymentId);

        if (!$orderId) return redirect()->route('home');

        $order = Order::findOrFail($orderId);

        if (!$order->isPaid() && $paymentId) {
            $config = config('services.satispay');
            \SatispayGBusiness\Api::setSandbox((bool) $config['sandbox']);
            \SatispayGBusiness\Api::setPublicKey($config['public_key']);
            \SatispayGBusiness\Api::setPrivateKey($config['private_key']);
            \SatispayGBusiness\Api::setKeyId($config['key_id']);

            $payment = \SatispayGBusiness\Payment::get($paymentId);

            if (($payment->status ?? '') === 'ACCEPTED') {
                $order->markAsPaid($paymentId);
                $order->update(['status' => 'processing']);
            } else {
                return redirect()->route('checkout.index')
                    ->with('error', 'Pagamento Satispay non completato. Riprova.');
            }
        }

        if ($order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        session()->forget('satispay_payment_' . $paymentId);
        session(['last_order_id' => $order->id]);

        return redirect()->route('checkout.success');
    }

    // =========================================================================
    // Pagina successo
    // =========================================================================

    public function success()
    {
        $orderId = session('last_order_id');
        if (!$orderId) return redirect()->route('home');

        $order = Order::with('items')->findOrFail($orderId);

        if ($order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }

    // =========================================================================
    // Helpers privati
    // =========================================================================

    private function resolveCart(): ?Cart
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())->first();
        }

        $sessionId = session('cart_session_id');
        if (!$sessionId) return null;

        return Cart::where('session_id', $sessionId)->first();
    }

    private function resolveSessionDiscount(float $subtotal, float $shippingCost): array
    {
        $stored = session('checkout_discount');

        if (!$stored || !isset($stored['code'])) {
            return ['code' => null, 'amount' => 0, 'label' => null];
        }

        // Rivalidazione al momento del submit
        $discount = Discount::active()->byCode($stored['code'])->first();

        if (!$discount || !$discount->canApplyToOrder($subtotal, auth()->id())) {
            session()->forget('checkout_discount');
            return ['code' => null, 'amount' => 0, 'label' => null];
        }

        $amount = $discount->type === 'shipping'
            ? $shippingCost
            : $discount->calculateDiscount($subtotal);

        return [
            'code'   => $stored['code'],
            'amount' => (float) $amount,
            'label'  => $stored['label'] ?? $this->discountLabel($discount),
        ];
    }

    private function discountLabel(Discount $discount): string
    {
        if ($discount->type === 'percentage') {
            return '-' . rtrim(rtrim(number_format($discount->value, 2), '0'), '.') . '%';
        }
        if ($discount->type === 'shipping') {
            return 'Spedizione gratuita';
        }
        return '-€ ' . number_format($discount->value, 2, ',', '.');
    }
}
