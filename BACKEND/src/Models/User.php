<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Model
{
    protected $table = 'users';
    
    public $timestamps = true;
    const UPDATED_AT = null; // La tabla no tiene updated_at
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role_id',
        'status',
        'plan_id',
        'business',
        'sector'
    ];
    
    protected $hidden = [
        'password'
    ];
    
    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime'
    ];
    
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'user_id');
    }
    
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
    
    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === 'admin';
    }
    
    public function isEditor(): bool
    {
        return $this->role && $this->role->name === 'editor';
    }
    
    public function isUser(): bool
    {
        return $this->role && $this->role->name === 'user';
    }
    
    // Accessor para obtener el nombre del rol directamente
    public function getRoleNameAttribute(): ?string
    {
        return $this->role ? $this->role->name : null;
    }
}
