<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyCard extends Model
{
    protected $fillable = [
        'card_number',
        'user_id',
        'points',
        'total_spent',
        'tier',
        'issued_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
        'issued_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($card) {
            if (empty($card->card_number)) {
                $card->card_number = 'CLR-' . strtoupper(uniqid());
            }
            if (empty($card->issued_at)) {
                $card->issued_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    public function addPoints(int $points, ?int $orderId = null, ?string $description = null): void
    {
        $this->increment('points', $points);
        $this->transactions()->create([
            'type' => 'earn',
            'points' => $points,
            'balance_after' => $this->points,
            'description' => $description ?? "Punti guadagnati",
            'order_id' => $orderId,
        ]);
        $this->updateTier();
    }

    public function redeemPoints(int $points, ?string $description = null): bool
    {
        if ($this->points < $points) return false;

        $this->decrement('points', $points);
        $this->transactions()->create([
            'type' => 'redeem',
            'points' => -$points,
            'balance_after' => $this->points,
            'description' => $description ?? "Punti riscattati",
        ]);
        return true;
    }

    public function updateTier(): void
    {
        $tier = match (true) {
            $this->total_spent >= 5000 => 'platinum',
            $this->total_spent >= 2000 => 'gold',
            $this->total_spent >= 500 => 'silver',
            default => 'base',
        };

        if ($this->tier !== $tier) {
            $this->update(['tier' => $tier]);
        }
    }

    public function addSpent(float $amount): void
    {
        $this->increment('total_spent', $amount);
        $this->updateTier();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTierLabelAttribute(): string
    {
        return match ($this->tier) {
            'base' => 'Base',
            'silver' => 'Silver',
            'gold' => 'Gold',
            'platinum' => 'Platinum',
            default => $this->tier,
        };
    }
}
