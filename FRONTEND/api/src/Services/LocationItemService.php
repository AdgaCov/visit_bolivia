<?php

namespace App\Services;

use App\Models\LocationItem;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationItemService
{
    public function getAll(?int $locationId = null): Collection
    {
        $query = LocationItem::with('location:id,name,type');
        
        if ($locationId) {
            $query->where('location_id', $locationId);
        }
        
        return $query->orderBy('id', 'desc')->get();
    }

    public function getById(int $id): LocationItem
    {
        return LocationItem::with('location:id,name,type')->findOrFail($id);
    }

    public function getByLocation(int $locationId): Collection
    {
        return LocationItem::where('location_id', $locationId)
            ->with('location:id,name,type')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function getByType(string $type, ?int $locationId = null): Collection
    {
        $query = LocationItem::where('type', $type)
            ->with('location:id,name,type');
        
        if ($locationId) {
            $query->where('location_id', $locationId);
        }
        
        return $query->orderBy('id', 'desc')->get();
    }

    public function getByUserId(int $userId, ?string $type = null): Collection
    {
        $query = LocationItem::whereHas('location', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with('location:id,name,type,user_id');
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->orderBy('id', 'desc')->get();
    }

    public function create(array $data): LocationItem
    {
        // Validar que la location existe
        Location::findOrFail($data['location_id']);
        
        return LocationItem::create($data);
    }

    public function update(int $id, array $data): LocationItem
    {
        $item = LocationItem::findOrFail($id);
        
        // Si se actualiza location_id, validar que existe
        if (isset($data['location_id'])) {
            Location::findOrFail($data['location_id']);
        }
        
        $item->update($data);
        return $item->fresh(['location:id,name,type']);
    }

    public function delete(int $id): bool
    {
        $item = LocationItem::findOrFail($id);
        
        // Eliminar archivo físico si existe
        if (!empty($item->image_url)) {
            $projectRoot = dirname(__DIR__, 2);
            $filePath = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . ltrim($item->image_url, '/\\');
            
            // Normalizar separadores de ruta
            $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filePath);
            
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        return $item->delete();
    }
}

