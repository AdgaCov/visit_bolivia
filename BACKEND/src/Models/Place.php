<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Place extends Model
{
    protected $table = 'places';
    
    protected $fillable = [
        'users_id',
        'name',
        'description',
        'latitude',
        'longitude',
        'address',
        'opening_hours',
        'contact_info'
    ];
    
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    
    public function images_places(): HasMany
    {
        return $this->hasMany(Image_places::class, 'place_id');
    }
    
    public function restaurant(): HasMany
    {
        return $this->hasMany(Restaurant::class, 'place_id');
    }
    
    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class, 'place_id');
    }
    
    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'place_city', 'place_id', 'city_id');
    }
    
    public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(Subcategory::class, 'place_subcategories', 'place_id', 'subcategory_id');
    }
    
    public function routes(): HasMany
    {
        return $this->hasMany(Route::class, 'destination_place_id');
    }
}
