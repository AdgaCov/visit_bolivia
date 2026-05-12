<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\CategoryService;
use App\Services\UploadService;
use Psr\Http\Message\UploadedFileInterface;

class CategoryController
{
    private CategoryService $categoryService;
    private UploadService $uploadService;

    public function __construct(CategoryService $categoryService, UploadService $uploadService)
    {
        $this->categoryService = $categoryService;
        $this->uploadService = $uploadService;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $categories = $this->categoryService->getAll();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $categories
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener categorías: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $category = $this->categoryService->getById((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $category
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener categoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];
            $uploadedFiles = $request->getUploadedFiles();
            if (isset($uploadedFiles['image']) && $uploadedFiles['image'] instanceof UploadedFileInterface) {
                $file = $uploadedFiles['image'];
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $data['image_url'] = $this->uploadService->saveCategoryImage($file);
                }
            }
            $category = $this->categoryService->create($data);
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Categoría creada exitosamente',
                'data' => $category
            ]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al crear categoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];
            $uploadedFiles = $request->getUploadedFiles();
            if (isset($uploadedFiles['image']) && $uploadedFiles['image'] instanceof UploadedFileInterface) {
                $file = $uploadedFiles['image'];
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $data['image_url'] = $this->uploadService->saveCategoryImage($file);
                }
            }
            $category = $this->categoryService->update((int)$args['id'], $data);
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente',
                'data' => $category
            ]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar categoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $this->categoryService->delete((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar categoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getWithPlaces(Request $request, Response $response): Response
    {
        try {
            $categories = $this->categoryService->getWithPlaces();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $categories
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener categorías con lugares: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getWithArticles(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $limit = isset($queryParams['limit']) ? max(1, (int)$queryParams['limit']) : 10;
            $offset = isset($queryParams['offset']) ? max(0, (int)$queryParams['offset']) : 0;

            $categories = $this->categoryService->getWithArticles($limit, $offset);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $categories
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener categorías con artículos: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getArticlesByCategory(Request $request, Response $response, array $args): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $limit = isset($queryParams['limit']) ? max(1, (int)$queryParams['limit']) : 20;
            $offset = isset($queryParams['offset']) ? max(0, (int)$queryParams['offset']) : 0;
            $type = isset($queryParams['type']) ? (string)$queryParams['type'] : null;

            if ($type !== null) {
                $allowedTypes = ['custom','place','event','restaurant','accommodation'];
                if (!in_array($type, $allowedTypes, true)) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => 'Parámetro type inválido. Valores permitidos: '.implode(',', $allowedTypes)
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            $articles = $this->categoryService->getArticlesByCategory((int)$args['id'], $limit, $offset, $type);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $articles
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener artículos de la categoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getArticlesByPageSection(Request $request, Response $response, array $args): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $limit = isset($queryParams['limit']) ? max(1, (int)$queryParams['limit']) : 100;
            $offset = isset($queryParams['offset']) ? max(0, (int)$queryParams['offset']) : 0;
            $type = isset($queryParams['type']) ? (string)$queryParams['type'] : null;

            if ($type !== null) {
                $allowedTypes = ['custom','place','event','restaurant','accommodation'];
                if (!in_array($type, $allowedTypes, true)) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => 'Parámetro type inválido. Valores permitidos: '.implode(',', $allowedTypes)
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            $pageSection = $args['page_section'];
            $articles = $this->categoryService->getArticlesByPageSection($pageSection, $limit, $offset, $type);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $articles
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener artículos por sección de página: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getArticlesByCategoryId(Request $request, Response $response, array $args): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $limit = isset($queryParams['limit']) ? max(1, (int)$queryParams['limit']) : 20;
            $offset = isset($queryParams['offset']) ? max(0, (int)$queryParams['offset']) : 0;
            $type = isset($queryParams['type']) ? (string)$queryParams['type'] : null;

            if ($type !== null) {
                $allowedTypes = ['custom','place','event','restaurant','accommodation'];
                if (!in_array($type, $allowedTypes, true)) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => 'Parámetro type inválido. Valores permitidos: '.implode(',', $allowedTypes)
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            $categoryId = (int)$args['category_id'];
            $articles = $this->categoryService->getArticlesByCategoryId($categoryId, $limit, $offset, $type);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $articles
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener artículos por ID de categoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getLocationsByCategory(Request $request, Response $response, array $args): Response
    {
        try {
            $type = $request->getQueryParams()['type'] ?? null;
            $locations = $this->categoryService->getLocationsByCategory((int)$args['id'], $type);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $locations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones por categoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
