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
use App\Mail\ResetPasswordMail;
use App\Mail\VerifyEmailMail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
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

        ResetPassword::toMailUsing(function (object $notifiable, string $token) {
            return new ResetPasswordMail($notifiable, $token);
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return new VerifyEmailMail($notifiable, $url);
        });

        View::composer('*', function ($view) {
            $view->with('wishlistIds', once(fn () => auth()->check()
                ? auth()->user()->wishlists()->pluck('product_id')->all()
                : []
            ));
        });
    }
}
