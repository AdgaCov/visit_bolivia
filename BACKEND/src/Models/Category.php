<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $table = 'categories';
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'description',
        'image_url'
    ];
    
    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class, 'category_id');
    }
    
    public function places(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, 'place_subcategories', 'subcategory_id', 'place_id')
                    ->join('subcategories', 'place_subcategories.subcategory_id', '=', 'subcategories.id')
                    ->where('subcategories.category_id', $this->id);
    }
}
