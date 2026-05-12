<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\RestaurantImage;
use App\Models\Place;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RestaurantService
{
    public function getAll(): Collection
    {
        return Restaurant::with(['place', 'images' => function($query) {
            $query->orderBy('is_main', 'desc');
        }])->orderBy('name', 'asc')->get();
    }

    public function getById(int $id): Restaurant
    {
        return Restaurant::with(['place', 'images' => function($query) {
            $query->orderBy('is_main', 'desc');
        }])->findOrFail($id);
    }

    public function getByPlace(int $placeId): Collection
    {
        return Restaurant::where('place_id', $placeId)
                        ->with(['place', 'images' => function($query) {
                            $query->orderBy('is_main', 'desc');
                        }])
                        ->orderBy('name', 'asc')
                        ->get();
    }

    public function create(array $data): Restaurant
    {
        // Validar que el lugar existe
        if (!Place::find($data['place_id'])) {
            throw new \Exception('El lugar especificado no existe');
        }

        $restaurant = Restaurant::create($data);
        return $this->getById($restaurant->id);
    }

    public function update(int $id, array $data): Restaurant
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->update($data);
        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $restaurant = Restaurant::findOrFail($id);
        return $restaurant->delete();
    }

    public function getNearby(float $latitude, float $longitude, float $radius = 50): Collection
    {
        return Restaurant::nearby($latitude, $longitude, $radius)
                        ->with(['place', 'images' => function($query) {
                            $query->orderBy('is_main', 'desc');
                        }])
                        ->orderBy('name', 'asc')
                        ->get();
    }

    public function getByBadge(string $badge): Collection
    {
        return Restaurant::byBadge($badge)
                        ->with(['place', 'images' => function($query) {
                            $query->orderBy('is_main', 'desc');
                        }])
                        ->orderBy('name', 'asc')
                        ->get();
    }

    public function search(string $search): Collection
    {
        return Restaurant::search($search)
                        ->with(['place', 'images' => function($query) {
                            $query->orderBy('is_main', 'desc');
                        }])
                        ->orderBy('name', 'asc')
                        ->get();
    }

    // Métodos para manejo de imágenes
    public function addImage(int $restaurantId, array $imageData): RestaurantImage
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        
        // Si es la primera imagen o se marca como principal, establecer como principal
        if ($imageData['is_main'] ?? false || $restaurant->images()->count() === 0) {
            $imageData['is_main'] = true;
            // Quitar el flag de principal de otras imágenes
            $restaurant->images()->update(['is_main' => false]);
        }

        return $restaurant->images()->create($imageData);
    }

    public function updateImage(int $imageId, array $imageData): RestaurantImage
    {
        $image = RestaurantImage::findOrFail($imageId);
        
        // Si se está estableciendo como imagen principal
        if ($imageData['is_main'] ?? false) {
            $image->setAsMain();
        }

        $image->update($imageData);
        return $image;
    }

    public function deleteImage(int $imageId): bool
    {
        $image = RestaurantImage::findOrFail($imageId);
        return $image->delete();
    }

    public function setMainImage(int $restaurantId, int $imageId): bool
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $image = $restaurant->images()->findOrFail($imageId);
        
        return $image->setAsMain();
    }

    // Métodos de estadísticas
    public function getRestaurantStats(): array
    {
        $totalRestaurants = Restaurant::count();
        $restaurantsWithImages = Restaurant::has('images')->count();
        $restaurantsByBadge = Restaurant::selectRaw('badge, COUNT(*) as count')
                                      ->whereNotNull('badge')
                                      ->groupBy('badge')
                                      ->get()
                                      ->pluck('count', 'badge')
                                      ->toArray();

        return [
            'total_restaurants' => $totalRestaurants,
            'restaurants_with_images' => $restaurantsWithImages,
            'restaurants_by_badge' => $restaurantsByBadge
        ];
    }
}
