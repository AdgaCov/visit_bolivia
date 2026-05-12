<?php

namespace App\Services;

use App\Models\LocationFavorite;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationFavoriteService
{
    public function getAll(): Collection
    {
        return LocationFavorite::with([
                'user', 
                'location' => function($query) {
                    $query->withCount('favorites')
                        ->with(['department', 'images', 'items']);
                }
            ])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getByUser(int $userId): Collection
    {
        return LocationFavorite::where('user_id', $userId)
            ->with([
                'location' => function($query) {
                    $query->withCount('favorites')
                        ->with(['department', 'images', 'items']);
                }
            ])
            ->orderByDesc('created_at')
            ->get();
    }

    public function isFavorite(int $userId, int $locationId): bool
    {
        return LocationFavorite::where('user_id', $userId)
            ->where('location_id', $locationId)
            ->exists();
    }

    public function addFavorite(int $userId, int $locationId): LocationFavorite
    {
        Location::findOrFail($locationId);

        $favorite = LocationFavorite::firstOrCreate([
            'user_id' => $userId,
            'location_id' => $locationId,
        ]);

        return $favorite->load('location');
    }

    public function removeFavorite(int $userId, int $locationId): bool
    {
        $favorite = LocationFavorite::where('user_id', $userId)
            ->where('location_id', $locationId)
            ->firstOrFail();

        return $favorite->delete();
    }

    public function toggleFavorite(int $userId, int $locationId): array
    {
        if ($this->isFavorite($userId, $locationId)) {
            $this->removeFavorite($userId, $locationId);
            return ['favorited' => false];
        }

        $favorite = $this->addFavorite($userId, $locationId);
        return ['favorited' => true, 'favorite' => $favorite];
    }

    public function countByLocation(int $locationId): int
    {
        return LocationFavorite::where('location_id', $locationId)->count();
    }
}

