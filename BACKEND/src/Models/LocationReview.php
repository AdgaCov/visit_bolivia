<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationReview extends Model
{
    protected $table = 'location_reviews';
    
    protected $fillable = [
        'location_id',
        'user_id',
        'rating',
        'comment',
        'created_at'
    ];
    
    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime'
    ];
    
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
