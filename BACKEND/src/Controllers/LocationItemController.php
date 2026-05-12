<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\LocationItemService;
use App\Services\UploadService;
use App\Models\LocationItem;
use Psr\Http\Message\UploadedFileInterface;

class LocationItemController
{
    private LocationItemService $locationItemService;
    private UploadService $uploadService;

    public function __construct(LocationItemService $locationItemService, UploadService $uploadService)
    {
        $this->locationItemService = $locationItemService;
        $this->uploadService = $uploadService;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $locationId = isset($queryParams['location_id']) ? (int)$queryParams['location_id'] : null;
            
            $items = $this->locationItemService->getAll($locationId);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $items
            ]));
            
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener items: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $item = $this->locationItemService->getById((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $item
            ]));
            
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Item no encontrado'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener item: ' . $e->getMessage()
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
                    $data['image_url'] = $this->uploadService->saveLocationItemImage($file);
                }
            }
            
            // Validar campos requeridos
            if (empty($data['location_id']) || empty($data['name'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'location_id y name son requeridos'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            // Validar tipo si se proporciona
            if (isset($data['type'])) {
                $allowedTypes = ['room', 'dish', 'service', 'offer', 'other'];
                if (!in_array($data['type'], $allowedTypes, true)) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => 'Tipo inválido. Valores permitidos: ' . implode(', ', $allowedTypes)
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }
            
            $item = $this->locationItemService->create($data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Item de locación creado exitosamente',
                'data' => $item
            ]));
            
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Locación no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al crear item: ' . $e->getMessage()
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
                    // Eliminar imagen anterior si existe
                    $item = LocationItem::findOrFail((int)$args['id']);
                    if (!empty($item->image_url)) {
                        $projectRoot = dirname(__DIR__, 2);
                        $filePath = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . ltrim($item->image_url, '/\\');
                        $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $filePath);
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                        }
                    }
                    $data['image_url'] = $this->uploadService->saveLocationItemImage($file);
                }
            }
            
            // Validar tipo si se proporciona
            if (isset($data['type'])) {
                $allowedTypes = ['room', 'dish', 'service', 'offer', 'other'];
                if (!in_array($data['type'], $allowedTypes, true)) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => 'Tipo inválido. Valores permitidos: ' . implode(', ', $allowedTypes)
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }
            
            $item = $this->locationItemService->update((int)$args['id'], $data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Item de locación actualizado exitosamente',
                'data' => $item
            ]));
            
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Item o locación no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar item: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $this->locationItemService->delete((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Item de locación eliminado exitosamente'
            ]));
            
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Item no encontrado'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar item: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByLocation(Request $request, Response $response, array $args): Response
    {
        try {
            $items = $this->locationItemService->getByLocation((int)$args['location_id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $items
            ]));
            
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener items de la locación: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByType(Request $request, Response $response, array $args): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $locationId = isset($queryParams['location_id']) ? (int)$queryParams['location_id'] : null;
            
            $items = $this->locationItemService->getByType($args['type'], $locationId);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $items
            ]));
            
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener items por tipo: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByUserId(Request $request, Response $response, array $args): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $type = isset($queryParams['type']) ? (string)$queryParams['type'] : null;
            
            // Validar tipo si se proporciona
            if ($type !== null) {
                $allowedTypes = ['room', 'dish', 'service', 'offer', 'other'];
                if (!in_array($type, $allowedTypes, true)) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => 'Tipo inválido. Valores permitidos: ' . implode(', ', $allowedTypes)
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }
            
            $items = $this->locationItemService->getByUserId((int)$args['user_id'], $type);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $items
            ]));
            
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener items del usuario: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}

