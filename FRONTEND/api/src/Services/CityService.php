<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CityService
{
    public function getAll(): Collection
    {
        return City::with(['department', 'places.images_places', 'images_cities'])->get();
    }

    public function getById(int $id): City
    {
        return City::with(['department', 'places.images_places', 'images_cities'])->findOrFail($id);
    }

    public function getByDepartment(int $departmentId): Collection
    {
        return City::where('department_id', $departmentId)
                   ->with(['department', 'places.images_places', 'images_cities'])
                   ->get();
    }

    public function create(array $data): City
    {
        return City::create($data);
    }

    public function update(int $id, array $data): City
    {
        $city = City::findOrFail($id);
        $city->update($data);
        return $city;
    }

    public function delete(int $id): bool
    {
        $city = City::findOrFail($id);
        return $city->delete();
    }

    public function getNearby(float $latitude, float $longitude, float $radius = 50): Collection
    {
        return City::selectRaw('*, 
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
            sin(radians(latitude)))) AS distance', [$latitude, $longitude, $latitude])
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->get();
    }

    public function searchByName(string $name): Collection
    {
        return City::where('name', 'LIKE', "%{$name}%")
                   ->with(['department', 'places.images_places', 'images_cities'])
                   ->get();
    }
}
