<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStep extends Model
{
    protected $table = 'route_steps';
    
    protected $fillable = [
        'route_id',
        'step_order',
        'lat',
        'lng',
        'nombre',
        'descripcion',
        'transport_mode'
    ];
    
    protected $casts = [
        'lat' => 'decimal:6',
        'lng' => 'decimal:6',
        'step_order' => 'integer'
    ];
    
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
