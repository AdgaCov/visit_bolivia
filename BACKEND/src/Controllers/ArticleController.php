<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\ArticleService;
use App\Services\UploadService;
use Psr\Http\Message\UploadedFileInterface;

class ArticleController
{
    private ArticleService $articleService;
    private UploadService $uploadService;

    public function __construct(ArticleService $articleService, UploadService $uploadService)
    {
        $this->articleService = $articleService;
        $this->uploadService = $uploadService;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $limit = isset($queryParams['limit']) ? max(1, (int)$queryParams['limit']) : 200;
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

            $articles = $this->articleService->getAll($limit, $offset, $type);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $articles,
                'pagination' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'count' => $articles->count()
                ]
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener artículos: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            if ($data === null) { $data = []; }

            // Soporte de imagen opcional via multipart/form-data (campo 'image')
            $uploadedFiles = $request->getUploadedFiles();
            if (isset($uploadedFiles['image']) && $uploadedFiles['image'] instanceof UploadedFileInterface) {
                $file = $uploadedFiles['image'];
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $data['media_url'] = $this->uploadService->saveArticleImage($file);
                }
            }

            $required = ['title', 'content'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "El campo {$field} es requerido"
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            $article = $this->articleService->create($data);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Artículo creado exitosamente',
                'data' => $article
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al crear artículo: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody();
            if ($data === null) { $data = []; }

            // Soporte de imagen opcional via multipart/form-data (campo 'image')
            $uploadedFiles = $request->getUploadedFiles();
            
            // Si no hay datos parseados y es multipart/form-data, intentar leer de $_POST
            // (funciona cuando se usa POST con method override, o servidor configurado para parsear PUT)
            if (empty($data)) {
                $contentType = $request->getHeaderLine('Content-Type');
                if (strpos($contentType, 'multipart/form-data') !== false && !empty($_POST)) {
                    $data = $_POST;
                }
            }

            if (isset($uploadedFiles['image']) && $uploadedFiles['image'] instanceof UploadedFileInterface) {
                $file = $uploadedFiles['image'];
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $data['media_url'] = $this->uploadService->saveArticleImage($file);
                }
            }
            
            // Filtrar solo campos válidos del modelo y remover vacíos
            $allowedFields = ['parent_id','id_category', 'title', 'subtitle', 'description', 'content', 
                            'type', 'author', 'media_url', 'template_id', 'page_section', 'link'];
            $data = array_intersect_key($data, array_flip($allowedFields));

            if (array_key_exists('parent_id', $data)) {
                $rawParentId = $data['parent_id'];
                if ($rawParentId === '' || $rawParentId === 'null') {
                    $data['parent_id'] = null; // quitar padre
                } elseif ($rawParentId !== null) {
                    $data['parent_id'] = (int)$rawParentId;
                }

                if ($data['parent_id'] !== null && $data['parent_id'] === (int)$args['id']) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => 'Un artículo no puede ser padre de sí mismo'
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }
            // Remover valores vacíos/null (permitir 0 y false)
            foreach ($data as $key => $value) {
                if ($key === 'parent_id') {
                    continue; // permitir null para remover padre
                }
                if ($value === null || $value === '') {
                    unset($data[$key]);
                }
            }
            
            // Si no hay datos para actualizar, devolver error informativo
            if (empty($data)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'No se enviaron datos válidos para actualizar. Asegúrate de enviar form-data con POST y header X-HTTP-Method-Override: PUT, o JSON directamente con PUT.',
                    'debug' => [
                        'parsed_body_empty' => empty($request->getParsedBody()),
                        'has_uploaded_files' => !empty($uploadedFiles),
                        'method' => $request->getMethod()
                    ]
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $article = $this->articleService->update((int)$args['id'], $data);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Artículo actualizado exitosamente',
                'data' => $article
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Artículo no encontrado'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar artículo: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $this->articleService->delete((int)$args['id']);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Artículo eliminado exitosamente'
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Artículo no encontrado'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar artículo: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByParentId(Request $request, Response $response, array $args): Response
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

            // Si parent_id es "null" o "0" en la URL, buscar artículos sin padre
            $parentId = isset($args['parent_id']) && $args['parent_id'] !== 'null' && $args['parent_id'] !== '0' 
                ? (int)$args['parent_id'] 
                : null;

            $articles = $this->articleService->getByParentId($parentId, $limit, $offset, $type);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $articles,
                'pagination' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'count' => $articles->count(),
                    'parent_id' => $parentId
                ]
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener artículos por parent_id: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}


