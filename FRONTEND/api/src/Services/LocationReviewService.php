<?php

namespace App\Services;

use App\Models\LocationReview;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;

class LocationReviewService
{
    public function create(array $data): LocationReview
    {
        return LocationReview::create($data);
    }

    public function getAll(?int $locationId = null, ?int $userId = null, int $limit = 50, int $offset = 0): array
    {
        // Si se filtra por location_id específico, retornar solo las reviews de esa location
        if ($locationId !== null) {
            $reviews = LocationReview::where('location_id', $locationId)
                ->with(['user'])
                ->orderByDesc('created_at')
                ->get();
            
            $location = Location::with(['department', 'user', 'subcategories', 'images', 'items'])
                ->findOrFail($locationId);
            
            return [
                [
                    'location' => $location,
                    'reviews' => $reviews,
                    'average_rating' => $reviews->avg('rating') ?? 0,
                    'review_count' => $reviews->count()
                ]
            ];
        }

        // Si se filtra por user_id, retornar agrupado por location
        if ($userId !== null) {
            $reviews = LocationReview::where('user_id', $userId)
                ->with(['user', 'location'])
                ->orderByDesc('created_at')
                ->get();
            
            $grouped = $reviews->groupBy('location_id')->map(function ($locationReviews, $locId) {
                $location = $locationReviews->first()->location;
                return [
                    'location' => $location,
                    'reviews' => $locationReviews->map(function ($review) {
                        return [
                            'id' => $review->id,
                            'user_id' => $review->user_id,
                            'rating' => $review->rating,
                            'comment' => $review->comment,
                            'created_at' => $review->created_at,
                            'user' => $review->user
                        ];
                    }),
                    'average_rating' => $locationReviews->avg('rating') ?? 0,
                    'review_count' => $locationReviews->count()
                ];
            })->values();
            
            return $grouped->skip($offset)->take($limit)->toArray();
        }

        // Sin filtros: agrupar todas las reviews por location
        $reviews = LocationReview::with(['user', 'location.department', 'location.user', 'location.subcategories', 'location.images', 'location.items'])
            ->orderByDesc('created_at')
            ->get();
        
        $grouped = $reviews->groupBy('location_id')->map(function ($locationReviews, $locId) {
            $location = $locationReviews->first()->location;
            return [
                'location' => $location,
                'reviews' => $locationReviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'user_id' => $review->user_id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'created_at' => $review->created_at,
                        'user' => $review->user
                    ];
                }),
                'average_rating' => $locationReviews->avg('rating') ?? 0,
                'review_count' => $locationReviews->count()
            ];
        })->values();
        
        return $grouped->skip($offset)->take($limit)->toArray();
    }

    public function getByLocation(int $locationId): Collection
    {
        return LocationReview::where('location_id', $locationId)
                            ->with(['user'])
                            ->orderBy('created_at', 'desc')
                            ->get();
    }

    public function getByUser(int $userId): Collection
    {
        return LocationReview::where('user_id', $userId)
                            ->with(['location'])
                            ->orderBy('created_at', 'desc')
                            ->get();
    }

    public function getFeaturedPlaces(int $limit = 3): Collection
    {
        // Versión simplificada: obtener locations que tienen reseñas, ordenadas por promedio de rating
        return Location::whereHas('reviews')
            ->with(['department', 'user', 'subcategories', 'images', 'reviews', 'items'])
            ->get()
            ->map(function ($location) {
                $location->average_rating = $location->reviews->avg('rating');
                $location->review_count = $location->reviews->count();
                return $location;
            })
            ->sortByDesc('average_rating')
            ->sortByDesc('review_count')
            ->take($limit)
            ->values();
    }

    public function getAverageRating(int $locationId): float
    {
        return LocationReview::where('location_id', $locationId)
                            ->avg('rating') ?? 0;
    }

    public function getReviewCount(int $locationId): int
    {
        return LocationReview::where('location_id', $locationId)->count();
    }

    public function findById(int $id): LocationReview
    {
        return LocationReview::with(['user', 'location'])->findOrFail($id);
    }

    public function update(int $id, array $data): LocationReview
    {
        $review = LocationReview::findOrFail($id);
        $review->update($data);
        return $review->fresh(['user', 'location']);
    }

    public function delete(int $id): bool
    {
        $review = LocationReview::findOrFail($id);
        return $review->delete();
    }
}
