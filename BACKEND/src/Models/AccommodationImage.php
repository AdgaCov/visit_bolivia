<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccommodationImage extends Model
{
    protected $table = 'accommodation_images';
    
    protected $fillable = [
        'accommodation_id',
        'image_url',
        'is_main'
    ];
    
    protected $casts = [
        'is_main' => 'boolean',
        'uploaded_at' => 'datetime'
    ];
    
    public function accommodation(): BelongsTo
    {
        return $this->belongsTo(Accommodation::class, 'accommodation_id');
    }
    
    // Método para establecer como imagen principal
    public function setAsMain(): bool
    {
        // Quitar el flag de principal de otras imágenes del mismo alojamiento
        $this->accommodation->images()->update(['is_main' => false]);
        
        // Establecer esta imagen como principal
        return $this->update(['is_main' => true]);
    }
    
    // Validar URL de imagen
    public function isValidImageUrl(): bool
    {
        return filter_var($this->image_url, FILTER_VALIDATE_URL) !== false;
    }
}
