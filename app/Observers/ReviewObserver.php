<?php

namespace App\Observers;

use App\Mail\ReviewRewardMail;
use App\Models\Discount;
use App\Models\Review;
use App\Models\User;
use App\Filament\Resources\Reviews\ReviewResource;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ReviewObserver
{
    public function created(Review $review): void
    {
        $admins = User::admins()->active()->get();

        $stars = str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating);
        $title = $review->title ? "\"{$review->title}\"" : 'Nuova recensione';

        foreach ($admins as $admin) {
            Notification::make()
                ->title('Recensione da approvare')
                ->body("{$title} · {$stars}")
                ->icon('heroicon-o-star')
                ->iconColor('warning')
                ->actions([
                    Action::make('view')
                        ->label('Vedi recensioni')
                        ->url(ReviewResource::getUrl('index'))
                        ->button(),
                ])
                ->tap(fn ($n) => $admin->notifyNow($n->toDatabase()));
        }
    }

    public function updated(Review $review): void
    {
        if (!$review->wasChanged('is_approved') || !$review->is_approved) {
            return;
        }

        // Sconto solo alla prima recensione approvata per utente
        $alreadyRewarded = Review::where('user_id', $review->user_id)
            ->where('reward_sent', true)
            ->exists();

        if ($alreadyRewarded) {
            return;
        }

        $user = $review->user;
        $code = 'REC-' . strtoupper(Str::random(8));

        $discount = Discount::create([
            'name'                   => 'Recensione ' . $user->first_name,
            'code'                   => $code,
            'type'                   => 'percentage',
            'value'                  => 15,
            'is_single_use_per_user' => true,
            'usage_limit'            => 1,
            'expires_at'             => now()->addMonths(3),
            'is_active'              => true,
        ]);

        Mail::to($user->email)->send(new ReviewRewardMail($user, $discount));

        $review->updateQuietly(['reward_sent' => true]);
    }
}
