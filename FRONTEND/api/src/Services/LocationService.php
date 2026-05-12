<?php

namespace App\Services;

use App\Models\Location;
use App\Models\LocationImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class LocationService
{
    public function getAll(?string $type = null): Collection
    {
        $query = Location::with(['department', 'user', 'subcategories', 'images', 'items']);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->get();
    }

    public function getPopularByFavorites(int $limit = 10, ?string $type = null): Collection
    {
        $limit = max(1, min(100, $limit));

        $query = Location::with(['department', 'user', 'subcategories', 'images', 'items'])
            ->withCount('favorites');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->orderByDesc('favorites_count')
            ->limit($limit)
            ->get();
    }

    public function getById(int $id): Location
    {
        return Location::with(['department', 'user', 'subcategories', 'images', 'items'])->findOrFail($id);
    }

    public function getByType(string $type, bool $onlyWithReviews = false, ?int $minRating = null): Collection
    {
        $query = Location::where('type', $type)
                         ->with(['department', 'user', 'subcategories', 'images', 'items']);

        if ($onlyWithReviews) {
            $query->whereHas('reviews');
        }

        if ($minRating !== null) {
            $query->whereHas('reviews', function ($q) use ($minRating) {
                $q->where('rating', '>=', $minRating);
            });
        }

        return $query->get();
    }

    public function getByDepartment(int $departmentId, ?string $type = null): Collection
    {
        $query = Location::where('department_id', $departmentId)
                        ->with(['department', 'user', 'subcategories', 'images', 'items']);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->get();
    }

    public function getByUser(int $userId, ?string $type = null): Collection
    {
        $query = Location::where('user_id', $userId)
                        ->with(['department', 'user', 'subcategories', 'images', 'items']);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->get();
    }

    public function getBySubcategory(int $subcategoryId, ?string $type = null): Collection
    {
        $query = Location::whereHas('subcategories', function($q) use ($subcategoryId) {
            $q->where('subcategory_id', $subcategoryId);
        })->with(['department', 'user', 'subcategories', 'images', 'items']);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->get();
    }

    /**
     * Obtener ubicaciones únicas que tienen al menos una subcategoría asociada,
     * con sus relaciones e imágenes. Soporta filtros y paginación.
     */
    public function getWithAnySubcategory(?string $type = null, ?int $departmentId = null, int $limit = 50, int $offset = 0): array
    {
        $baseQuery = Location::whereHas('subcategories')
            ->with(['department', 'user', 'subcategories', 'images', 'items'])
            ->when($type, function ($q) use ($type) { $q->where('type', $type); })
            ->when($departmentId, function ($q) use ($departmentId) { $q->where('department_id', $departmentId); })
            ->orderByDesc('created_at');

        $data = (clone $baseQuery)->limit($limit)->offset($offset)->get();
        $total = (clone $baseQuery)->toBase()->getCountForPagination();

        return [
            'data' => $data,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
        ];
    }

    public function create(array $data): Location
    {
        return Location::create($data);
    }

    public function update(int $id, array $data): Location
    {
        $location = Location::findOrFail($id);
        $location->update($data);
        return $location;
    }

    public function delete(int $id): bool
    {
        $location = Location::findOrFail($id);
        return $location->delete();
    }

    public function getNearby(float $latitude, float $longitude, float $radius = 50, ?string $type = null): Collection
    {
        $query = Location::selectRaw('*, 
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
            sin(radians(latitude)))) AS distance', [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->with(['department', 'user', 'subcategories', 'images', 'items']);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->get();
    }

    public function searchByName(string $name, ?string $type = null): Collection
    {
        $query = Location::where('name', 'LIKE', "%{$name}%")
                        ->with(['department', 'user', 'subcategories', 'images', 'items']);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->get();
    }

    // ==========================
    // EVENT-SPECIFIC METHODS
    // ==========================

    public function getUpcomingEvents(): Collection
    {
        return Location::where('type', 'event')
                      ->where('start_date', '>', now())
                      ->orderBy('start_date')
                      ->with(['department', 'user', 'subcategories', 'images', 'items'])
                      ->get();
    }

    public function getRecurringEvents(): Collection
    {
        return Location::where('type', 'event')
                      ->where('is_recurring', 1)
                      ->with(['department', 'user', 'subcategories', 'images', 'items'])
                      ->get();
    }

    public function getEventsByDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return Location::where('type', 'event')
                      ->whereBetween('start_date', [$startDate, $endDate])
                      ->orderBy('start_date')
                      ->with(['department', 'user', 'subcategories', 'images', 'items'])
                      ->get();
    }

    public function getTodayEvents(): Collection
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();
        
        return Location::where('type', 'event')
                      ->whereBetween('start_date', [$today, $tomorrow])
                      ->orderBy('start_date')
                      ->with(['department', 'user', 'subcategories', 'images', 'items'])
                      ->get();
    }

    public function getThisWeekEvents(): Collection
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        return Location::where('type', 'event')
                      ->whereBetween('start_date', [$startOfWeek, $endOfWeek])
                      ->orderBy('start_date')
                      ->with(['department', 'user', 'subcategories', 'images', 'items'])
                      ->get();
    }

    public function getThisMonthEvents(): Collection
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        return Location::where('type', 'event')
                      ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                      ->orderBy('start_date')
                      ->with(['department', 'user', 'subcategories', 'images', 'items'])
                      ->get();
    }

    // ==========================
    // RESTAURANT-SPECIFIC METHODS
    // ==========================

    public function getRestaurantsByBadge(string $badge): Collection
    {
        return Location::where('type', 'restaurant')
                      ->where('badge', $badge)
                      ->with(['department', 'user', 'subcategories', 'images', 'items'])
                      ->get();
    }

    // ==========================
    // ACCOMMODATION-SPECIFIC METHODS
    // ==========================

    public function getAccommodationsByRoomType(string $roomType): Collection
    {
        return Location::where('type', 'accommodation')
                      ->where('room_type', $roomType)
                      ->with(['department', 'user', 'subcategories', 'images', 'items'])
                      ->get();
    }

    public function getAccommodationsByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return Location::where('type', 'accommodation')
                      ->whereBetween('price_per_night', [$minPrice, $maxPrice])
                      ->orderBy('price_per_night')
                      ->with(['department', 'user', 'subcategories', 'images', 'items'])
                      ->get();
    }

    public function getAccommodationsByCapacity(int $capacity): Collection
    {
        return Location::where('type', 'accommodation')
                      ->where('capacity', '>=', $capacity)
                      ->orderBy('capacity')
                      ->with(['department', 'user', 'subcategories', 'images', 'items'])
                      ->get();
    }

    // ==========================
    // STATISTICS METHODS
    // ==========================

    public function getStats(?string $type = null): array
    {
        $query = Location::query();
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $total = $query->count();
        
        $stats = [
            'total' => $total,
            'by_type' => []
        ];
        
        if (!$type) {
            $typeStats = Location::selectRaw('type, COUNT(*) as count')
                               ->groupBy('type')
                               ->get()
                               ->pluck('count', 'type')
                               ->toArray();
            
            $stats['by_type'] = $typeStats;
        }
        
        return $stats;
    }

    // ==========================
    // IMAGE MANAGEMENT METHODS
    // ==========================

    public function addImage(int $locationId, array $data): LocationImage
    {
        $data['location_id'] = $locationId;
        return LocationImage::create($data);
    }

    public function updateImage(int $imageId, array $data): LocationImage
    {
        $image = LocationImage::findOrFail($imageId);
        $image->update($data);
        return $image;
    }

    public function deleteImage(int $imageId): bool
    {
        $image = LocationImage::findOrFail($imageId);
        
        // Eliminar archivo físico si existe
        if (!empty($image->image_url)) {
            $projectRoot = dirname(__DIR__, 2);
            $filePath = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . ltrim($image->image_url, '/\\');
            
            // Normalizar separadores de ruta
            $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filePath);
            
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        return $image->delete();
    }

    public function setMainImage(int $locationId, int $imageId): bool
    {
        $image = LocationImage::findOrFail($imageId);
        
        // Verificar que la imagen pertenece a la ubicación
        if ($image->location_id !== $locationId) {
            throw new \Exception('La imagen no pertenece a esta ubicación');
        }
        
        return $image->setAsMain();
    }

    // ==========================
    // REVIEWS-BASED LISTING
    // ==========================

    /**
     * Listar ubicaciones que tienen al menos una reseña, con filtros y paginación.
     */
    public function getWithReviews(?string $type = null, ?int $minRating = null, int $limit = 50, int $offset = 0): array
    {
        $baseQuery = Location::query()
            ->whereHas('reviews')
            ->with(['department', 'user', 'subcategories', 'images', 'items'])
            ->when($type, function ($q) use ($type) { $q->where('type', $type); })
            ->when($minRating !== null, function ($q) use ($minRating) {
                $q->whereHas('reviews', function ($qr) use ($minRating) {
                    $qr->where('rating', '>=', $minRating);
                });
            })
            ->orderByDesc('created_at');

        $data = (clone $baseQuery)->limit($limit)->offset($offset)->get();
        $total = (clone $baseQuery)->toBase()->getCountForPagination();

        return [
            'data' => $data,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
        ];
    }
}

