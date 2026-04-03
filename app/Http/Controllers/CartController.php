<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Recupera (o crea) il carrello per l'utente corrente o per l'ospite.
     */
    private function resolveCart(): Cart
    {
        if (auth()->check()) {
            return auth()->user()->getOrCreateCart();
        }

        $sessionId = session()->get('cart_session_id') ?? tap(Str::uuid()->toString(), fn ($id) => session(['cart_session_id' => $id]));

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    private function syncCartCount(Cart $cart): void
    {
        session(['cart_count' => $cart->items()->sum('quantity')]);
    }

    // -------------------------------------------------------------------------

    public function preview()
    {
        $cart = $this->resolveCart();
        $cart->load(['items.product', 'items.productVariant']);

        $items = $cart->items->map(function ($item) {
            $product  = $item->product;
            $price    = $item->productVariant?->price ?? $product->price;
            return [
                'name'     => $product->name,
                'variant'  => $item->productVariant?->name,
                'quantity' => $item->quantity,
                'price'    => number_format($price, 2, ',', '.'),
                'subtotal' => number_format($price * $item->quantity, 2, ',', '.'),
                'image'    => $product->main_image ? \Storage::url($product->main_image) : null,
                'url'      => route('products.show', $product->slug),
            ];
        });

        $total = number_format($cart->items->sum(function ($i) {
            return ($i->productVariant?->price ?? $i->product->price) * $i->quantity;
        }), 2, ',', '.');

        return response()->json(['items' => $items, 'total' => $total]);
    }

    public function index()
    {
        $cart = $this->resolveCart();
        $cart->load(['items.product', 'items.productVariant']);

        return view('cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id'         => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity'           => ['integer', 'min:1', 'max:99'],
        ]);

        $product  = Product::active()->findOrFail($request->product_id);
        $quantity = $request->integer('quantity', 1);
        $variant  = $request->filled('product_variant_id')
            ? ProductVariant::findOrFail($request->product_variant_id)
            : null;

        if ($product->variants()->active()->exists() && !$variant) {
            return back()->withErrors(['variant' => 'Seleziona una variante prima di aggiungere al carrello.']);
        }

        $cart = $this->resolveCart();
        $cart->addItem($product, $variant, $quantity);
        $this->syncCartCount($cart);

        if ($request->expectsJson()) {
            return response()->json(['cart_count' => session('cart_count')]);
        }

        return back()->with('success', '«' . $product->name . '» aggiunto al carrello.');
    }

    public function update(Request $request, CartItem $item)
    {
        $this->authorizeItem($item);

        $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);

        $cart = $this->resolveCart();
        $cart->updateItemQuantity($item, $request->integer('quantity'));
        $this->syncCartCount($cart);

        return back();
    }

    public function remove(CartItem $item)
    {
        $this->authorizeItem($item);

        $cart = $this->resolveCart();
        $cart->removeItem($item);
        $this->syncCartCount($cart);

        return back()->with('success', 'Prodotto rimosso dal carrello.');
    }

    public function clear()
    {
        $cart = $this->resolveCart();
        $cart->items()->delete();
        $this->syncCartCount($cart);

        return redirect()->route('cart.index')->with('success', 'Carrello svuotato.');
    }

    private function authorizeItem(CartItem $item): void
    {
        $cart = $this->resolveCart();
        abort_unless($item->cart_id === $cart->id, 403);
    }
}
