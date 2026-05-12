<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accommodation extends Model
{
    protected $table = 'accommodations';
    
    protected $fillable = [
        'place_id',
        'title',
        'description',
        'location',
        'room_type',
        'price_per_night',
        'capacity',
        'latitude',
        'longitude',
        'badge',
        'link',
        'facebook',
        'instagram',
        'whatsapp'
    ];
    
    protected $casts = [
        'price_per_night' => 'decimal:2',
        'capacity' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
    
    public function images(): HasMany
    {
        return $this->hasMany(AccommodationImage::class, 'accommodation_id');
    }
    
    public function mainImage(): HasMany
    {
        return $this->hasMany(AccommodationImage::class, 'accommodation_id')->where('is_main', true);
    }
    
    // Scopes para consultas comunes
    public function scopeNearby($query, $latitude, $longitude, $radius = 50)
    {
        return $query->whereRaw(
            "ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?",
            [$longitude, $latitude, $radius * 1000]
        );
    }
    
    public function scopeByRoomType($query, $roomType)
    {
        return $query->where('room_type', $roomType);
    }
    
    public function scopeByBadge($query, $badge)
    {
        return $query->where('badge', $badge);
    }
    
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price_per_night', [$minPrice, $maxPrice]);
    }
    
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('location', 'LIKE', "%{$search}%");
    }
    
    // Métodos de utilidad
    public function getFormattedPriceAttribute(): string
    {
        return 'Bs. ' . number_format($this->price_per_night, 2);
    }
}
