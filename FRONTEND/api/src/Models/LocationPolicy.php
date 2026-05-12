<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationPolicy extends Model
{
    protected $table = 'location_policies';

    protected $fillable = [
        'location_id',
        'reservation_recommended',
        'reservation_notes',
        'reservation_link',
        'cancellation_deadline_hours',
        'cancellation_policy',
        'opening_hours',
        'days_closed',
        'accepts_cash',
        'accepts_credit_card',
        'accepts_debit_card',
        'accepts_bank_transfer',
        'accepts_digital_wallet',
        'payment_notes',
        'event_duration_hours',
        'minimum_age',
        'dress_code'
    ];

    protected $casts = [
        'reservation_recommended' => 'boolean',
        'accepts_cash' => 'boolean',
        'accepts_credit_card' => 'boolean',
        'accepts_debit_card' => 'boolean',
        'accepts_bank_transfer' => 'boolean',
        'accepts_digital_wallet' => 'boolean',
        'cancellation_deadline_hours' => 'integer',
        'event_duration_hours' => 'decimal:2',
        'minimum_age' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}

