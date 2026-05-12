<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{
    protected $table = 'routes';
    
    protected $fillable = [
        'origin_type',
        'origin_id',
        'destination_type',
        'destination_id',
        'name',
        'description'
    ];
    
    public function steps(): HasMany
    {
        return $this->hasMany(RouteStep::class, 'route_id')->orderBy('step_order');
    }
}
