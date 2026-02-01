<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'first_name',
        'last_name',
        'company',
        'vat_number',
        'tax_code',
        'address',
        'address_2',
        'city',
        'province',
        'postal_code',
        'country',
        'phone',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        // Quando si imposta un indirizzo come default, rimuove il flag dagli altri
        static::saving(function ($address) {
            if ($address->is_default) {
                static::where('user_id', $address->user_id)
                      ->where('type', $address->type)
                      ->where('id', '!=', $address->id)
                      ->update(['is_default' => false]);
            }
        });
    }

    // Relazioni
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helpers
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFormattedAddressAttribute(): string
    {
        $address = $this->address;
        
        if ($this->address_2) {
            $address .= ', ' . $this->address_2;
        }
        
        return sprintf(
            "%s\n%s %s, %s\n%s (%s)",
            $address,
            $this->postal_code,
            $this->city,
            $this->province,
            $this->country
        );
    }
}