<?php

namespace App\Services;

use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubcategoryService
{
    public function getAll(): Collection
    {
        return Subcategory::with('category')->get();
    }

    public function getById(int $id): Subcategory
    {
        return Subcategory::with(['category', 'places'])->findOrFail($id);
    }

    public function getByCategory(int $categoryId): Collection
    {
        return Subcategory::where('category_id', $categoryId)
                          ->with(['category', 'places'])
                          ->get();
    }

    public function create(array $data): Subcategory
    {
        return Subcategory::create($data);
    }

    public function update(int $id, array $data): Subcategory
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->update($data);
        return $subcategory;
    }

    public function delete(int $id): bool
    {
        $subcategory = Subcategory::findOrFail($id);
        return $subcategory->delete();
    }

    public function searchByName(string $name): Collection
    {
        return Subcategory::where('name', 'LIKE', "%{$name}%")
                          ->with(['category', 'places'])
                          ->get();
    }
}
