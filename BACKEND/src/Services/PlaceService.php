<?php

namespace App\Services;

use App\Models\Place;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PlaceService
{
    public function getAll(): Collection
    {
        return Place::with(['user', 'images_places', 'cities', 'subcategories', 'restaurant', 'accommodations'])->get();
    }

    public function getById(int $id): Place
    {
        return Place::with(['user', 'images_places', 'cities', 'subcategories', 'restaurant', 'accommodations'])->findOrFail($id);
    }

    public function getByUser(int $userId): Collection
    {
        return Place::where('users_id', $userId)
                    ->with(['images_places', 'cities', 'subcategories', 'restaurant', 'accommodations'])
                    ->get();
    }

    public function getByCity(int $cityId): Collection
    {
        return Place::whereHas('cities', function($query) use ($cityId) {
            $query->where('cities.id', $cityId);
        })->with(['user', 'images_places', 'cities', 'subcategories', 'restaurant', 'accommodations'])->get();
    }

    public function create(array $data): Place
    {
        $place = Place::create($data);
        
        // Attach cities if provided
        if (isset($data['cities'])) {
            $place->cities()->attach($data['cities']);
        }
        
        // Attach subcategories if provided
        if (isset($data['subcategories'])) {
            $place->subcategories()->attach($data['subcategories']);
        }
        
        return $place->load(['user', 'images_places', 'cities', 'subcategories', 'restaurant', 'accommodations']);
    }

    public function update(int $id, array $data): Place
    {
        $place = Place::findOrFail($id);
        $place->update($data);
        
        // Update cities if provided
        if (isset($data['cities'])) {
            $place->cities()->sync($data['cities']);
        }
        
        // Update subcategories if provided
        if (isset($data['subcategories'])) {
            $place->subcategories()->sync($data['subcategories']);
        }
        
        return $place->load(['user', 'images_places', 'cities', 'subcategories', 'restaurant', 'accommodations']);
    }

    public function delete(int $id): bool
    {
        $place = Place::findOrFail($id);
        return $place->delete();
    }

    public function getNearby(float $latitude, float $longitude, float $radius = 50): Collection
    {
        return Place::selectRaw('*, 
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
            sin(radians(latitude)))) AS distance', [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->with(['user', 'images_places', 'cities', 'subcategories'])
            ->get();
    }

    public function searchByName(string $name): Collection
    {
        return Place::where('name', 'LIKE', "%{$name}%")
                   ->with(['user', 'images_places', 'cities', 'subcategories', 'restaurant', 'accommodations'])
                   ->get();
    }

    public function getBySubcategory(int $subcategoryId): Collection
    {
        return Place::whereHas('subcategories', function($query) use ($subcategoryId) {
            $query->where('subcategories.id', $subcategoryId);
        })->with(['user', 'images_places', 'cities', 'subcategories', 'restaurant', 'accommodations'])->get();
    }
}
