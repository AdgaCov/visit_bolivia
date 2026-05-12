<?php

namespace App\Services;

use App\Models\Image_places;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ImageService
{
    public function getAll(): Collection
    {
        return Image_places::with('place')->get();
    }

    public function getById(int $id): Image_places
    {
        return Image_places::with('place')->findOrFail($id);
    }

    public function getByPlace(int $placeId): Collection
    {
        return Image_places::where('place_id', $placeId)
                   ->with('place')
                   ->get();
    }

    public function create(array $data): Image_places
    {
        return Image_places::create($data);
    }

    public function update(int $id, array $data): Image_places
    {
        $image = Image_places::findOrFail($id);
        $image->update($data);
        return $image;
    }

    public function delete(int $id): bool
    {
        $image = Image_places::findOrFail($id);
        return $image->delete();
    }

    public function uploadImage(int $placeId, string $imageUrl): Image
    {
        return $this->create([
            'place_id' => $placeId,
            'image_url' => $imageUrl
        ]);
    }
}
