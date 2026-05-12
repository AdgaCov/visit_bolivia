<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    protected $table = 'restaurants';
    
    protected $fillable = [
        'place_id',
        'name',
        'description',
        'location',
        'latitude',
        'longitude',
        'badge',
        'link',
        'facebook',
        'instagram',
        'whatsapp'
    ];
    
    protected $casts = [
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
        return $this->hasMany(RestaurantImage::class, 'restaurant_id');
    }
    
    public function mainImage(): HasMany
    {
        return $this->hasMany(RestaurantImage::class, 'restaurant_id')->where('is_main', true);
    }
    
    // Scopes para consultas comunes
    public function scopeNearby($query, $latitude, $longitude, $radius = 50)
    {
        return $query->whereRaw(
            "ST_Distance_Sphere(POINT(longitude, latitude), POINT(?, ?)) <= ?",
            [$longitude, $latitude, $radius * 1000]
        );
    }
    
    public function scopeByBadge($query, $badge)
    {
        return $query->where('badge', $badge);
    }
    
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('location', 'LIKE', "%{$search}%");
    }
}
