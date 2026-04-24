<?php

namespace App\Observers;

use App\Mail\NewOrderAdminMail;
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
        if ($order->wasChanged('payment_status') && $order->payment_status === 'paid') {
            // Mail all'admin (in coda per rispettare rate limit Mailtrap)
            Mail::to('colorstarantosrl@gmail.com')->queue(new NewOrderAdminMail($order));

            // Notifica nella dashboard admin
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

            // Mail di conferma al cliente (in coda, con delay per rispettare rate limit Mailtrap free)
            $email = $order->user?->email ?? $order->guest_email;
            if ($email) {
                Mail::to($email)->later(now()->addSeconds(5), new \App\Mail\OrderConfirmedMail($order));
            }
        }

        if ($order->wasChanged('status') && $order->status === 'shipped') {
            $email = $order->user?->email ?? $order->guest_email;
            if ($email) {
                Mail::to($email)->queue(new OrderShippedMail($order));
            }
        }
    }

    public function created(Order $order): void
    {
        // Nessuna notifica alla creazione: l'ordine è ancora pending.
        // Mail e notifiche partono in updated() quando payment_status diventa 'paid'.
    }
}
