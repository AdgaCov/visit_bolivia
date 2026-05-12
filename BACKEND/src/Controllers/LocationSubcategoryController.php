<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\LocationSubcategoryService;

class LocationSubcategoryController
{
    private LocationSubcategoryService $locationSubcategoryService;

    public function __construct(LocationSubcategoryService $locationSubcategoryService)
    {
        $this->locationSubcategoryService = $locationSubcategoryService;
    }

    /**
     * Listar todas las asociaciones location-subcategory
     * GET /api/location-subcategories
     * Query params opcionales: location_id, subcategory_id
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $locationId = isset($queryParams['location_id']) ? (int)$queryParams['location_id'] : null;
            $subcategoryId = isset($queryParams['subcategory_id']) ? (int)$queryParams['subcategory_id'] : null;

            $associations = $this->locationSubcategoryService->getAll($locationId, $subcategoryId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $associations,
                'count' => $associations->count()
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener asociaciones: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Obtener subcategorías de una ubicación específica
     * GET /api/location-subcategories/by-location/{location_id}
     */
    public function getSubcategoriesByLocation(Request $request, Response $response, array $args): Response
    {
        try {
            $locationId = (int)$args['location_id'];
            $subcategories = $this->locationSubcategoryService->getSubcategoriesByLocation($locationId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'location_id' => $locationId,
                'data' => $subcategories,
                'count' => $subcategories->count()
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ubicación no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener subcategorías: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Obtener ubicaciones de una subcategoría específica
     * GET /api/location-subcategories/by-subcategory/{subcategory_id}
     */
    public function getLocationsBySubcategory(Request $request, Response $response, array $args): Response
    {
        try {
            $subcategoryId = (int)$args['subcategory_id'];
            $locations = $this->locationSubcategoryService->getLocationsBySubcategory($subcategoryId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'subcategory_id' => $subcategoryId,
                'data' => $locations,
                'count' => $locations->count()
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
                'message' => 'Error al obtener ubicaciones: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Agregar una subcategoría a una ubicación
     * POST /api/location-subcategories
     * Body: { "location_id": 1, "subcategory_id": 2 }
     */
    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];

            if (empty($data['location_id']) || empty($data['subcategory_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'location_id y subcategory_id son requeridos'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $result = $this->locationSubcategoryService->attach(
                (int)$data['location_id'],
                (int)$data['subcategory_id']
            );

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'location_id' => $result['location_id'],
                    'subcategory_id' => $result['subcategory_id']
                ]
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ubicación o subcategoría no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al agregar asociación: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Sincronizar subcategorías de una ubicación (reemplazar todas)
     * PUT /api/location-subcategories/{location_id}
     * Body: { "subcategory_ids": [1, 2, 3] }
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $locationId = (int)$args['location_id'];
            $data = $request->getParsedBody() ?? [];

            if (empty($data['subcategory_ids']) || !is_array($data['subcategory_ids'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'subcategory_ids debe ser un array de IDs'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $result = $this->locationSubcategoryService->sync($locationId, $data['subcategory_ids']);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'location_id' => $result['location_id'],
                    'subcategory_ids' => $result['subcategory_ids'],
                    'subcategories' => $result['subcategories']
                ]
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ubicación no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al sincronizar subcategorías: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * Eliminar una asociación específica
     * DELETE /api/location-subcategories/{location_id}/{subcategory_id}
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $locationId = (int)$args['location_id'];
            $subcategoryId = (int)$args['subcategory_id'];

            $result = $this->locationSubcategoryService->detach($locationId, $subcategoryId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'location_id' => $result['location_id'],
                    'subcategory_id' => $result['subcategory_id']
                ]
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ubicación o subcategoría no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar asociación: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}

