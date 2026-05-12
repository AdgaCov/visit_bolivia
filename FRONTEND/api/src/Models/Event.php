<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Event extends Model
{
    protected $table = 'events';
    
    protected $fillable = [
        'place_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'event_type',
        'price',
        'is_recurring',
        'latitude',
        'longitude'
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'decimal:2',
        'is_recurring' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];
    
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
    
    public function images(): HasMany
    {
        return $this->hasMany(EventImage::class, 'event_id');
    }
    
    public function mainImage(): HasMany
    {
        return $this->hasMany(EventImage::class, 'event_id')->where('is_main', 1);
    }
    
    // Scopes para consultas comunes
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', Carbon::now());
    }
    
    public function scopeByType($query, $type)
    {
        return $query->where('event_type', $type);
    }
    
    public function scopeByPlace($query, $placeId)
    {
        return $query->where('place_id', $placeId);
    }
    
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', 1);
    }
    
    public function scopeNearby($query, $latitude, $longitude, $radius = 50)
    {
        return $query->whereRaw(
            "ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?",
            [$longitude, $latitude, $radius * 1000]
        );
    }
    
    // Métodos de utilidad
    public function isUpcoming(): bool
    {
        return $this->start_date >= Carbon::now();
    }
    
    public function isOngoing(): bool
    {
        $now = Carbon::now();
        return $this->start_date <= $now && 
               ($this->end_date === null || $this->end_date >= $now);
    }
    
    public function isPast(): bool
    {
        $endDate = $this->end_date ?? $this->start_date;
        return $endDate < Carbon::now();
    }
    
    public function getDurationAttribute(): ?int
    {
        if ($this->end_date && $this->start_date) {
            return $this->start_date->diffInMinutes($this->end_date);
        }
        return null;
    }
    
    public function getFormattedPriceAttribute(): string
    {
        if ($this->price === null) {
            return 'Gratis';
        }
        return 'Bs. ' . number_format($this->price, 2);
    }
}
