<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $table = 'cities';
    
    protected $fillable = [
        'department_id',
        'name',
        'latitude',
        'longitude',
        'description'
    ];
    
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    public function places(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, 'place_city', 'city_id', 'place_id');
    }
    
    public function routes(): HasMany
    {
        return $this->hasMany(Route::class, 'origin_city_id');
    }

    public function images_cities(): HasMany
    {
        return $this->hasMany(Image_cities::class, 'city_id');
    }
}
