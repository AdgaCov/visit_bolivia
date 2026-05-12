<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image_places extends Model
{
    protected $table = 'images_places';
    
    protected $fillable = [
        'place_id',
        'image_url'
    ];
    
    protected $casts = [
        'uploaded_at' => 'datetime'
    ];
    
    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
}
