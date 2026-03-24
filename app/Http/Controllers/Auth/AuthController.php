<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials + ['role' => 'customer', 'is_active' => true], $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Merge del carrello ospite con quello dell'utente
            if ($sessionId = session('cart_session_id')) {
                $guestCart = \App\Models\Cart::where('session_id', $sessionId)->first();
                $guestCart?->convertToUser(Auth::id());
                session()->forget('cart_session_id');
            }

            return redirect()->intended(route('account.index'));
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Le credenziali inserite non sono corrette.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'password'   => ['required', 'confirmed', Password::min(8)],
            'newsletter' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'first_name'              => $data['first_name'],
            'last_name'               => $data['last_name'],
            'email'                   => $data['email'],
            'phone'                   => $data['phone'] ?? null,
            'password'                => Hash::make($data['password']),
            'role'                    => 'customer',
            'is_active'               => true,
            'newsletter_subscribed'   => isset($data['newsletter']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
