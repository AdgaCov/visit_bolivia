<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image_cities extends Model
{
    protected $table = 'images_cities';
    
    protected $fillable = [
        'city_id',
        'image_url'
    ];
    
    protected $casts = [
        'uploaded_at' => 'datetime'
    ];
    
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}


