<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $recentOrders = $user->orders()->latest()->limit(5)->get();
        $wishlistCount = $user->wishlists()->count();

        return view('account.index', compact('user', 'recentOrders', 'wishlistCount'));
    }

    public function orders()
    {
        $orders = auth()->user()
            ->orders()
            ->latest()
            ->paginate(10);

        return view('account.orders', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items.product', 'items.productVariant');

        return view('account.order-show', compact('order'));
    }

    public function addresses()
    {
        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->get();

        return view('account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $data = $request->validate([
            'type'        => ['required', 'in:shipping,billing,both'],
            'first_name'  => ['required', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'address'     => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:100'],
            'province'    => ['required', 'string', 'max:2'],
            'postal_code' => ['required', 'string', 'max:10'],
            'country'     => ['nullable', 'string', 'max:100'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'is_default'  => ['nullable', 'boolean'],
            'company'     => ['nullable', 'string', 'max:255'],
            'vat_number'  => ['nullable', 'string', 'max:30'],
            'tax_code'    => ['nullable', 'string', 'max:20'],
        ]);

        auth()->user()->addresses()->create($data);

        return back()->with('success', 'Indirizzo aggiunto.');
    }

    public function destroyAddress(Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        $address->delete();

        return back()->with('success', 'Indirizzo eliminato.');
    }

    public function wishlist()
    {
        $wishlist = auth()->user()
            ->wishlists()
            ->with(['product.variants' => fn ($q) => $q->active()])
            ->get();

        return view('account.wishlist', compact('wishlist'));
    }

    public function toggleWishlist(Product $product)
    {
        $user     = auth()->user();
        $existing = $user->wishlists()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            $added = false;
        } else {
            $user->wishlists()->create(['product_id' => $product->id]);
            $added = true;
        }

        if (request()->expectsJson()) {
            return response()->json(['added' => $added]);
        }

        return back()->with('success', $added ? 'Aggiunto alla wishlist.' : 'Rimosso dalla wishlist.');
    }

    public function editProfile()
    {
        return view('account.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'first_name'           => ['required', 'string', 'max:255'],
            'last_name'            => ['required', 'string', 'max:255'],
            'phone'                => ['nullable', 'string', 'max:20'],
            'newsletter_subscribed'=> ['nullable', 'boolean'],
            'current_password'     => ['nullable', 'required_with:new_password', 'string'],
            'new_password'         => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if (isset($data['current_password'])) {
            if (! Hash::check($data['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'La password attuale non è corretta.']);
            }
            $user->password = Hash::make($data['new_password']);
        }

        $user->first_name            = $data['first_name'];
        $user->last_name             = $data['last_name'];
        $user->phone                 = $data['phone'] ?? null;
        $user->newsletter_subscribed = isset($data['newsletter_subscribed']);
        $user->save();

        return back()->with('success', 'Profilo aggiornato con successo.');
    }
}
