<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Collection;

class ArticleService
{
    public function getAll(int $limit = 20, int $offset = 0, ?string $type = null): Collection
    {
        $query = Article::with(['images' => function ($q) {
                $q->orderByDesc('is_main');
            }, 'category', 'subcategories']);

        if ($type !== null) {
            $query->where('type', $type);
        }

        return $query->orderByDesc('created_at')
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function getBySubcategoryIds(array $subcategoryIds, int $limit = 10, int $offset = 0, ?string $type = null): Collection
    {
        $query = Article::with(['images' => function ($q) {
                $q->orderByDesc('is_main');
            }, 'subcategories'])
            ->whereHas('subcategories', function ($q) use ($subcategoryIds) {
                $q->whereIn('subcategories.id', $subcategoryIds);
            });

        if ($type !== null) {
            $query->where('type', $type);
        }

        return $query->orderByDesc('created_at')
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    public function getByLocationId(int $locationId, int $limit = 10, int $offset = 0, ?string $type = null): Collection
    {
        // Estrategia: usar subcategorías compartidas entre la location y los artículos
        // 1) obtener subcategorías de la location a través de la tabla pivot
        $subcategoryIds = DB::table('location_subcategories')
            ->where('location_id', $locationId)
            ->pluck('subcategory_id')
            ->toArray();

        if (empty($subcategoryIds)) {
            return Article::whereRaw('1 = 0')->get(); // Retorna Collection vacía del tipo correcto
        }

        // 2) buscar artículos que compartan esas subcategorías
        return $this->getBySubcategoryIds($subcategoryIds, $limit, $offset, $type);
    }

    public function create(array $data): Article
    {
        return Article::create($data);
    }

    public function update(int $id, array $data): Article
    {
        $article = Article::findOrFail($id);
        $article->fill($data);
        $article->save();
        return $article;
    }

    public function delete(int $id): void
    {
        $article = Article::findOrFail($id);
        $article->delete();
    }

    public function getByParentId(?int $parentId, int $limit = 20, int $offset = 0, ?string $type = null): Collection
    {
        $query = Article::with([
                'images' => function ($q) {
                    $q->orderByDesc('is_main');
                }, 
                'category', 
                'subcategories',
                'parent',
                'children'
            ]);

        if ($parentId === null) {
            // Artículos sin padre (artículos principales)
            $query->whereNull('parent_id');
        } else {
            // Artículos hijos de un padre específico
            $query->where('parent_id', $parentId);
        }

        if ($type !== null) {
            $query->where('type', $type);
        }

        return $query->orderByDesc('created_at')
            ->skip($offset)
            ->take($limit)
            ->get();
    }
}




