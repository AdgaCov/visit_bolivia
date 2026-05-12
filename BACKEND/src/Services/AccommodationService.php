<?php

namespace App\Services;

use App\Models\Accommodation;
use App\Models\AccommodationImage;
use App\Models\Place;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AccommodationService
{
    public function getAll(): Collection
    {
        return Accommodation::with(['place', 'images' => function($query) {
            $query->orderBy('is_main', 'desc');
        }])->orderBy('title', 'asc')->get();
    }

    public function getById(int $id): Accommodation
    {
        return Accommodation::with(['place', 'images' => function($query) {
            $query->orderBy('is_main', 'desc');
        }])->findOrFail($id);
    }

    public function getByPlace(int $placeId): Collection
    {
        return Accommodation::where('place_id', $placeId)
                           ->with(['place', 'images' => function($query) {
                               $query->orderBy('is_main', 'desc');
                           }])
                           ->orderBy('title', 'asc')
                           ->get();
    }

    public function create(array $data): Accommodation
    {
        // Validar que el lugar existe
        if (!Place::find($data['place_id'])) {
            throw new \Exception('El lugar especificado no existe');
        }

        $accommodation = Accommodation::create($data);
        return $this->getById($accommodation->id);
    }

    public function update(int $id, array $data): Accommodation
    {
        $accommodation = Accommodation::findOrFail($id);
        $accommodation->update($data);
        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $accommodation = Accommodation::findOrFail($id);
        return $accommodation->delete();
    }

    public function getNearby(float $latitude, float $longitude, float $radius = 50): Collection
    {
        return Accommodation::nearby($latitude, $longitude, $radius)
                           ->with(['place', 'images' => function($query) {
                               $query->orderBy('is_main', 'desc');
                           }])
                           ->orderBy('title', 'asc')
                           ->get();
    }

    public function getByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return Accommodation::byPriceRange($minPrice, $maxPrice)
                           ->with(['place', 'images' => function($query) {
                               $query->orderBy('is_main', 'desc');
                           }])
                           ->orderBy('price_per_night', 'asc')
                           ->get();
    }

    public function getByCapacity(int $capacity): Collection
    {
        return Accommodation::where('capacity', '>=', $capacity)
                           ->with(['place', 'images' => function($query) {
                               $query->orderBy('is_main', 'desc');
                           }])
                           ->orderBy('capacity', 'asc')
                           ->get();
    }

    public function getByRoomType(string $roomType): Collection
    {
        return Accommodation::byRoomType($roomType)
                           ->with(['place', 'images' => function($query) {
                               $query->orderBy('is_main', 'desc');
                           }])
                           ->orderBy('title', 'asc')
                           ->get();
    }

    public function getByBadge(string $badge): Collection
    {
        return Accommodation::byBadge($badge)
                           ->with(['place', 'images' => function($query) {
                               $query->orderBy('is_main', 'desc');
                           }])
                           ->orderBy('title', 'asc')
                           ->get();
    }

    public function search(string $search): Collection
    {
        return Accommodation::search($search)
                           ->with(['place', 'images' => function($query) {
                               $query->orderBy('is_main', 'desc');
                           }])
                           ->orderBy('title', 'asc')
                           ->get();
    }

    // Métodos para manejo de imágenes
    public function addImage(int $accommodationId, array $imageData): AccommodationImage
    {
        $accommodation = Accommodation::findOrFail($accommodationId);
        
        // Si es la primera imagen o se marca como principal, establecer como principal
        if ($imageData['is_main'] ?? false || $accommodation->images()->count() === 0) {
            $imageData['is_main'] = true;
            // Quitar el flag de principal de otras imágenes
            $accommodation->images()->update(['is_main' => false]);
        }

        return $accommodation->images()->create($imageData);
    }

    public function updateImage(int $imageId, array $imageData): AccommodationImage
    {
        $image = AccommodationImage::findOrFail($imageId);
        
        // Si se está estableciendo como imagen principal
        if ($imageData['is_main'] ?? false) {
            $image->setAsMain();
        }

        $image->update($imageData);
        return $image;
    }

    public function deleteImage(int $imageId): bool
    {
        $image = AccommodationImage::findOrFail($imageId);
        return $image->delete();
    }

    public function setMainImage(int $accommodationId, int $imageId): bool
    {
        $accommodation = Accommodation::findOrFail($accommodationId);
        $image = $accommodation->images()->findOrFail($imageId);
        
        return $image->setAsMain();
    }

    // Métodos de estadísticas
    public function getAccommodationStats(): array
    {
        $totalAccommodations = Accommodation::count();
        $accommodationsWithImages = Accommodation::has('images')->count();
        $accommodationsByRoomType = Accommodation::selectRaw('room_type, COUNT(*) as count')
                                                ->whereNotNull('room_type')
                                                ->groupBy('room_type')
                                                ->get()
                                                ->pluck('count', 'room_type')
                                                ->toArray();
        $accommodationsByBadge = Accommodation::selectRaw('badge, COUNT(*) as count')
                                             ->whereNotNull('badge')
                                             ->groupBy('badge')
                                             ->get()
                                             ->pluck('count', 'badge')
                                             ->toArray();

        $avgPrice = Accommodation::avg('price_per_night');
        $minPrice = Accommodation::min('price_per_night');
        $maxPrice = Accommodation::max('price_per_night');

        return [
            'total_accommodations' => $totalAccommodations,
            'accommodations_with_images' => $accommodationsWithImages,
            'accommodations_by_room_type' => $accommodationsByRoomType,
            'accommodations_by_badge' => $accommodationsByBadge,
            'price_stats' => [
                'average_price' => round($avgPrice, 2),
                'min_price' => $minPrice,
                'max_price' => $maxPrice
            ]
        ];
    }
}
