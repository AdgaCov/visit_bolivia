<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $table = 'plans';

    protected $fillable = [
        'name',
        'description',
        'price',
        'price_currency',
        'max_locations',
        'max_location_images',
        'max_location_items',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_locations' => 'integer',
        'max_location_images' => 'integer',
        'max_location_items' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'plan_id');
    }
}


