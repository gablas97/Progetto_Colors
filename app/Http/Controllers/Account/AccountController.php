<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Mail\AccountDeactivatedMail;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function index()
    {
        $user              = auth()->user();
        $recentOrders      = $user->orders()->latest()->limit(5)->get();
        $ordersCount       = $user->orders()->count();
        $wishlistCount     = $user->wishlists()->count();
        $defaultAddress    = $user->addresses()->where('is_default', true)->first()
                           ?? $user->addresses()->first();
        $hasDeliveredOrder = $user->orders()->where('status', 'delivered')->exists();

        return view('account.index', compact(
            'user', 'recentOrders', 'ordersCount', 'wishlistCount', 'defaultAddress', 'hasDeliveredOrder'
        ));
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

        $unreviewedProducts = collect();
        if ($order->status === 'delivered') {
            $reviewedProductIds = Review::where('user_id', auth()->id())->pluck('product_id');
            $unreviewedProducts = $order->items
                ->filter(fn ($item) => $item->product && !$item->product->is_service)
                ->filter(fn ($item) => !$reviewedProductIds->contains($item->product_id))
                ->map(fn ($item) => $item->product)
                ->unique('id')
                ->values();
        }

        return view('account.order-show', compact('order', 'unreviewedProducts'));
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

        $user = auth()->user();

        if (!$user->addresses()->exists()) {
            $data['is_default'] = true;
        }

        $user->addresses()->create($data);

        return back()->with('success', 'Indirizzo aggiunto.');
    }

    public function destroyAddress(Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        $address->delete();

        return back()->with('success', 'Indirizzo eliminato.');
    }

    public function setDefaultAddress(Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);

        $address->update(['is_default' => true]);

        return back()->with('success', 'Indirizzo predefinito aggiornato.');
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

    public function destroy(Request $request)
    {
        $request->validateWithBag('deleteAccount', [
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'La password non è corretta.'], 'deleteAccount');
        }

        $reactivationUrl = URL::temporarySignedRoute(
            'account.reactivate',
            now()->addDays(7),
            ['id' => $user->id]
        );

        $user->update(['is_active' => false]);

        Mail::to($user->email)->send(new AccountDeactivatedMail($user, $reactivationUrl));

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Il tuo account è stato eliminato. Controlla l\'email per annullare l\'operazione.');
    }

    public function reactivate(Request $request, int $id)
    {
        $user = User::where('id', $id)->where('is_active', false)->first();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Link non valido o account già attivo.');
        }

        $user->update(['is_active' => true]);

        return redirect()->route('login')->with('success', 'Account riattivato con successo. Puoi ora accedere.');
    }
}
