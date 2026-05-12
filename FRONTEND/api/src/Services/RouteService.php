<?php

namespace App\Services;

use App\Models\Route;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RouteService
{
    public function getAll(): Collection
    {
        return Route::with(['steps'])->get();
    }

    public function getById(int $id): Route
    {
        return Route::with(['steps'])->findOrFail($id);
    }

    public function getByOriginCity(int $cityId): Collection
    {
        return Route::where('origin_type', 'city')
                   ->where('origin_id', $cityId)
                   ->with(['steps'])
                   ->get();
    }

    public function getByDestinationPlace(int $placeId): Collection
    {
        return Route::where('destination_type', 'place')
                   ->where('destination_id', $placeId)
                   ->with(['steps'])
                   ->get();
    }

    public function create(array $data): Route
    {
        $route = Route::create($data);
        
        // Create route steps if provided
        if (isset($data['steps']) && is_array($data['steps'])) {
            foreach ($data['steps'] as $step) {
                $step['route_id'] = $route->id;
                $route->steps()->create($step);
            }
        }
        
        return $route->load(['steps']);
    }

    public function update(int $id, array $data): Route
    {
        $route = Route::findOrFail($id);
        $route->update($data);
        
        // Update route steps if provided
        if (isset($data['steps']) && is_array($data['steps'])) {
            $route->steps()->delete();
            foreach ($data['steps'] as $step) {
                $step['route_id'] = $route->id;
                $route->steps()->create($step);
            }
        }
        
        return $route->load(['steps']);
    }

    public function delete(int $id): bool
    {
        $route = Route::findOrFail($id);
        return $route->delete();
    }

    public function searchByName(string $name): Collection
    {
        return Route::where('name', 'LIKE', "%{$name}%")
                   ->with(['steps'])
                   ->get();
    }

    public function getRoutesBetween(int $originCityId, int $destinationPlaceId): Collection
    {
        return Route::where('origin_type', 'city')
                   ->where('origin_id', $originCityId)
                   ->where('destination_type', 'place')
                   ->where('destination_id', $destinationPlaceId)
                   ->with(['steps'])
                   ->get();
    }
}
