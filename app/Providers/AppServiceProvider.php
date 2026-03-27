<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Observers\CategoryObserver;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Observers\ReviewObserver;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Category::observe(CategoryObserver::class);
        Order::observe(OrderObserver::class);
        Product::observe(ProductObserver::class);
        Review::observe(ReviewObserver::class);

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifica il tuo indirizzo email - Colors S.r.l.')
                ->greeting('Ciao ' . $notifiable->first_name . '!')
                ->line('Clicca il pulsante qui sotto per verificare il tuo indirizzo email.')
                ->action('Verifica email', $url)
                ->line('Il link scadrà tra 60 minuti.')
                ->line('Se non hai creato un account su Colors S.r.l., ignora questa email.')
                ->salutation('Il team di Colors S.r.l.');
        });

        View::composer('*', function ($view) {
            $wishlistIds = auth()->check()
                ? auth()->user()->wishlists()->pluck('product_id')->all()
                : [];
            $view->with('wishlistIds', $wishlistIds);
        });
    }
}
