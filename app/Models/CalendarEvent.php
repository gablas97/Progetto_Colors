<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CalendarEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'priority',
        'starts_at',
        'ends_at',
        'all_day',
        'recurrence',
        'user_id',
        'related_type',
        'related_id',
        'color',
        'is_completed',
        'completed_at',
        'reminder_at',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'all_day' => 'boolean',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'reminder_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now())
            ->where('is_completed', false)
            ->orderBy('starts_at');
    }

    public function scopeOverdue($query)
    {
        return $query->where('starts_at', '<', now())
            ->where('is_completed', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('starts_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('starts_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function getColorAttribute($value): string
    {
        if ($value) return $value;

        return match ($this->type) {
            'pagamento' => '#ef4444',
            'scadenza' => '#f59e0b',
            'commercialista' => '#8b5cf6',
            'consegna' => '#3b82f6',
            'ordine_fornitore' => '#06b6d4',
            'promozione' => '#ec4899',
            'inventario' => '#10b981',
            default => '#6b7280',
        };
    }

    public function isOverdue(): bool
    {
        return !$this->is_completed && $this->starts_at->isPast();
    }
}
