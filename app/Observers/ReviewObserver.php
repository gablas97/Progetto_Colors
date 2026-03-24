<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\User;
use App\Filament\Resources\Reviews\ReviewResource;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

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
}
