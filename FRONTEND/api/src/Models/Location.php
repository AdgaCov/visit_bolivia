<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Location extends Model
{
    protected $table = 'locations';
    
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'latitude',
        'longitude',
        'address',
        'opening_hours',
        'contact_info',
        'type',
        'department_id',
        'badge',
        'room_type',
        'price_per_night',
        'start_date',
        'end_date',
        'event_type',
        'capacity',
        'is_recurring',
        'facebook',
        'instagram',
        'whatsapp',
        'link'
    ];
    
    protected $casts = [
        'latitude' => 'decimal:15',
        'longitude' => 'decimal:15',
        'price_per_night' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_recurring' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(Subcategory::class, 'location_subcategories', 'location_id', 'subcategory_id');
    }
    
    public function images(): HasMany
    {
        return $this->hasMany(LocationImage::class, 'location_id');
    }
    
    public function mainImage(): HasMany
    {
        return $this->hasMany(LocationImage::class, 'location_id')->where('is_main', 1);
    }
    
    public function reviews(): HasMany
    {
        return $this->hasMany(LocationReview::class, 'location_id');
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(LocationItem::class, 'location_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(LocationFavorite::class, 'location_id');
    }

    public function policy(): HasOne
    {
        return $this->hasOne(LocationPolicy::class, 'location_id');
    }
}

