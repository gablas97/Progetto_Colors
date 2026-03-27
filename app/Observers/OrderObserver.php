<?php

namespace App\Observers;

use App\Mail\OrderShippedMail;
use App\Models\Order;
use App\Models\User;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status') && $order->status === 'shipped') {
            $email = $order->user?->email ?? $order->guest_email;
            if ($email) {
                Mail::to($email)->send(new OrderShippedMail($order));
            }
        }
    }

    public function created(Order $order): void
    {
        $admins = User::admins()->active()->get();

        foreach ($admins as $admin) {
            Notification::make()
                ->title('Nuovo ordine ricevuto')
                ->body("#{$order->order_number} · " . number_format((float) $order->total, 2, ',', '.') . ' €')
                ->icon('heroicon-o-shopping-bag')
                ->iconColor('success')
                ->actions([
                    Action::make('view')
                        ->label('Apri ordine')
                        ->url(OrderResource::getUrl('edit', ['record' => $order->id]))
                        ->button(),
                ])
                ->tap(fn ($n) => $admin->notifyNow($n->toDatabase()));
        }
    }
}
