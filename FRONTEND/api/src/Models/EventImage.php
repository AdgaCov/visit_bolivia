<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventImage extends Model
{
    protected $table = 'event_images';
    
    protected $fillable = [
        'event_id',
        'image_url',
        'alt_text',
        'is_main'
    ];
    
    protected $casts = [
        'is_main' => 'boolean',
        'uploaded_at' => 'datetime'
    ];
    
    protected $dates = [
        'uploaded_at',
        'created_at',
        'updated_at'
    ];
    
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    
    // Scope para obtener solo la imagen principal
    public function scopeMain($query)
    {
        return $query->where('is_main', 1);
    }
    
    // Scope para obtener imágenes secundarias
    public function scopeSecondary($query)
    {
        return $query->where('is_main', 0);
    }
    
    // Método para establecer como imagen principal
    public function setAsMain(): bool
    {
        // Primero quitar el flag de imagen principal de otras imágenes del mismo evento
        static::where('event_id', $this->event_id)
              ->where('id', '!=', $this->id)
              ->update(['is_main' => 0]);
        
        // Establecer esta imagen como principal
        return $this->update(['is_main' => 1]);
    }
    
    // Método para obtener la URL completa de la imagen
    public function getFullImageUrlAttribute(): string
    {
        if (filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            return $this->image_url;
        }
        
        // Si es una ruta relativa, construir la URL completa
        $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost';
        return rtrim($baseUrl, '/') . '/' . ltrim($this->image_url, '/');
    }
}
