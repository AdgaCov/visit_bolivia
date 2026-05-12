<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Subcategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Collection;

class LocationSubcategoryService
{
    /**
     * Obtener todas las asociaciones location-subcategory
     */
    public function getAll(?int $locationId = null, ?int $subcategoryId = null): Collection
    {
        $query = DB::table('location_subcategories')
            ->join('locations', 'location_subcategories.location_id', '=', 'locations.id')
            ->join('subcategories', 'location_subcategories.subcategory_id', '=', 'subcategories.id')
            ->select(
                'location_subcategories.location_id',
                'location_subcategories.subcategory_id',
                'locations.id as location_id',
                'locations.name as location_name',
                'subcategories.id as subcategory_id',
                'subcategories.name as subcategory_name'
            );

        if ($locationId !== null) {
            $query->where('location_subcategories.location_id', $locationId);
        }

        if ($subcategoryId !== null) {
            $query->where('location_subcategories.subcategory_id', $subcategoryId);
        }

        $results = $query->get();

        // Convertir a Collection con estructura más clara
        return $results->map(function ($item) {
            return [
                'location_id' => $item->location_id,
                'location_name' => $item->location_name,
                'subcategory_id' => $item->subcategory_id,
                'subcategory_name' => $item->subcategory_name,
            ];
        });
    }

    /**
     * Obtener subcategorías de una ubicación específica
     */
    public function getSubcategoriesByLocation(int $locationId): Collection
    {
        $location = Location::with('subcategories')->findOrFail($locationId);
        return $location->subcategories;
    }

    /**
     * Obtener ubicaciones de una subcategoría específica
     */
    public function getLocationsBySubcategory(int $subcategoryId): Collection
    {
        $subcategory = Subcategory::with('locations')->findOrFail($subcategoryId);
        return $subcategory->locations;
    }

    /**
     * Agregar una subcategoría a una ubicación (attach)
     */
    public function attach(int $locationId, int $subcategoryId): array
    {
        $location = Location::findOrFail($locationId);
        $subcategory = Subcategory::findOrFail($subcategoryId);

        // Verificar si ya existe la asociación
        if ($location->subcategories()->where('subcategory_id', $subcategoryId)->exists()) {
            throw new \Exception('La subcategoría ya está asociada a esta ubicación');
        }

        $location->subcategories()->attach($subcategoryId);

        return [
            'location_id' => $locationId,
            'subcategory_id' => $subcategoryId,
            'message' => 'Subcategoría agregada exitosamente'
        ];
    }

    /**
     * Eliminar una subcategoría de una ubicación (detach)
     */
    public function detach(int $locationId, int $subcategoryId): array
    {
        $location = Location::findOrFail($locationId);
        $subcategory = Subcategory::findOrFail($subcategoryId);

        // Verificar si existe la asociación
        if (!$location->subcategories()->where('subcategory_id', $subcategoryId)->exists()) {
            throw new \Exception('La subcategoría no está asociada a esta ubicación');
        }

        $location->subcategories()->detach($subcategoryId);

        return [
            'location_id' => $locationId,
            'subcategory_id' => $subcategoryId,
            'message' => 'Subcategoría eliminada exitosamente'
        ];
    }

    /**
     * Sincronizar subcategorías de una ubicación (sync)
     * Reemplaza todas las subcategorías actuales con las nuevas proporcionadas
     */
    public function sync(int $locationId, array $subcategoryIds): array
    {
        $location = Location::findOrFail($locationId);

        // Validar que todas las subcategorías existan
        $existingSubcategories = Subcategory::whereIn('id', $subcategoryIds)->pluck('id')->toArray();
        $invalidIds = array_diff($subcategoryIds, $existingSubcategories);
        
        if (!empty($invalidIds)) {
            throw new \Exception('Las siguientes subcategorías no existen: ' . implode(', ', $invalidIds));
        }

        $location->subcategories()->sync($subcategoryIds);

        // Recargar la relación
        $location->load('subcategories');

        return [
            'location_id' => $locationId,
            'subcategory_ids' => $subcategoryIds,
            'subcategories' => $location->subcategories,
            'message' => 'Subcategorías sincronizadas exitosamente'
        ];
    }

    /**
     * Verificar si una ubicación tiene una subcategoría específica
     */
    public function exists(int $locationId, int $subcategoryId): bool
    {
        $location = Location::findOrFail($locationId);
        return $location->subcategories()->where('subcategory_id', $subcategoryId)->exists();
    }
}

