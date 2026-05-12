<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Article;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryService
{
    public function getAll(): Collection
    {
        return Category::with(['subcategories:id,category_id,name,description,image_url'])->get();
    }

    public function getById(int $id): Category
    {
        return Category::with(['subcategories:id,category_id,name,description,image_url'])->findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id): bool
    {
        $category = Category::findOrFail($id);
        return $category->delete();
    }

    public function getWithPlaces(): Collection
    {
        return Category::with(['subcategories.places'])->get();
    }

    public function getWithArticles(int $limitPerSubcategory = 10, int $offset = 0): Collection
    {
        return Category::with(['subcategories' => function ($q) use ($limitPerSubcategory, $offset) {
            $q->with(['articles' => function ($qa) use ($limitPerSubcategory, $offset) {
                $qa->with(['images' => function ($qi) {
                        $qi->orderByDesc('is_main');
                    }])
                   ->orderByDesc('created_at')
                   ->skip($offset)
                   ->take($limitPerSubcategory);
            }]);
        }])->get();
    }

    public function getArticlesByCategory(int $categoryId, int $limit = 20, int $offset = 0, ?string $type = null): Collection
    {
        $query = Article::with(['images' => function ($q) {
                $q->orderByDesc('is_main');
            }, 'subcategories:id,category_id,name,description,image_url'])
            ->whereHas('subcategories', function ($q) use ($categoryId) {
                $q->where('subcategories.category_id', $categoryId);
            })
            ->whereNull('page_section') // Solo artículos donde page_section es NULL
            ->orderByDesc('created_at');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->skip($offset)->take($limit)->get();
    }

    public function getArticlesByPageSection(string $pageSection, int $limit = 20, int $offset = 0, ?string $type = null): Collection
    {
        $query = Article::with(['images' => function ($q) {
                $q->orderByDesc('is_main');
            }, 'subcategories:id,category_id,name,description,image_url'])
            ->where('page_section', $pageSection)
            ->orderByDesc('created_at');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->skip($offset)->take($limit)->get();
    }

    public function getArticlesByCategoryId(int $categoryId, int $limit = 20, int $offset = 0, ?string $type = null): Collection
    {
        $query = Article::with(['images' => function ($q) {
                $q->orderByDesc('is_main');
            }, 'category:id,name,description,image_url', 'subcategories:id,category_id,name,description,image_url'])
            ->where('id_category', $categoryId)
            ->orderBy('created_at');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->skip($offset)->take($limit)->get();
    }

    public function searchByName(string $name): Collection
    {
        return Category::where('name', 'LIKE', "%{$name}%")
                      ->with('subcategories')
                      ->get();
    }

    /**
     * Obtener locations asociadas a una categoría a través de sus subcategorías.
     */
    public function getLocationsByCategory(int $categoryId, ?string $type = null): Collection
    {
        $query = Location::whereHas('subcategories', function ($q) use ($categoryId) {
                $q->where('subcategories.category_id', $categoryId);
            })
            ->with(['department', 'user', 'subcategories', 'images', 'items']);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }
}
