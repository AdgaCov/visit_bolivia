<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationItem extends Model
{
    protected $table = 'location_items';
    
    protected $fillable = [
        'location_id',
        'name',
        'description',
        'type',
        'price',
        'review',
        'image_url'
    ];
    
    public $timestamps = false;
    
    protected $casts = [
        'price' => 'decimal:2'
    ];
    
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
