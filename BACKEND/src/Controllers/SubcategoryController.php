<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\SubcategoryService;
use App\Services\ArticleService;
use App\Services\UploadService;
use Psr\Http\Message\UploadedFileInterface;

class SubcategoryController
{
    private SubcategoryService $subcategoryService;
    private ArticleService $articleService;
    private UploadService $uploadService;

    public function __construct(SubcategoryService $subcategoryService, ArticleService $articleService, UploadService $uploadService)
    {
        $this->subcategoryService = $subcategoryService;
        $this->articleService = $articleService;
        $this->uploadService = $uploadService;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $subcategories = $this->subcategoryService->getAll();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $subcategories
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener subcategorías: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $subcategory = $this->subcategoryService->getById((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $subcategory
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Subcategoría no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener subcategoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];
            $uploadedFiles = $request->getUploadedFiles();
            
            // Procesar imagen si se envía
            if (isset($uploadedFiles['image']) && $uploadedFiles['image'] instanceof UploadedFileInterface) {
                $file = $uploadedFiles['image'];
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $data['image_url'] = $this->uploadService->saveSubcategoryImage($file);
                }
            }
            
            $subcategory = $this->subcategoryService->create($data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Subcategoría creada exitosamente',
                'data' => $subcategory
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al crear subcategoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];
            $uploadedFiles = $request->getUploadedFiles();
            
            // Procesar imagen si se envía
            if (isset($uploadedFiles['image']) && $uploadedFiles['image'] instanceof UploadedFileInterface) {
                $file = $uploadedFiles['image'];
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $data['image_url'] = $this->uploadService->saveSubcategoryImage($file);
                }
            }
            
            $subcategory = $this->subcategoryService->update((int)$args['id'], $data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Subcategoría actualizada exitosamente',
                'data' => $subcategory
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Subcategoría no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar subcategoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $this->subcategoryService->delete((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Subcategoría eliminada exitosamente'
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Subcategoría no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar subcategoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByCategory(Request $request, Response $response, array $args): Response
    {
        try {
            $subcategories = $this->subcategoryService->getByCategory((int)$args['category_id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $subcategories
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener subcategorías de la categoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getArticlesBySubcategory(Request $request, Response $response, array $args): Response
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

            $articles = $this->articleService->getBySubcategoryIds([(int)$args['id']], $limit, $offset, $type);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $articles
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Subcategoría no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener artículos de la subcategoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
