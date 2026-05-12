<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\CityService;

class CityController
{
    private CityService $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $cities = $this->cityService->getAll();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $cities
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ciudades: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $city = $this->cityService->getById((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $city
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ciudad no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ciudad: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $city = $this->cityService->create($data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Ciudad creada exitosamente',
                'data' => $city
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al crear ciudad: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody();
            $city = $this->cityService->update((int)$args['id'], $data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Ciudad actualizada exitosamente',
                'data' => $city
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ciudad no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar ciudad: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $this->cityService->delete((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Ciudad eliminada exitosamente'
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ciudad no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar ciudad: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByDepartment(Request $request, Response $response, array $args): Response
    {
        try {
            $cities = $this->cityService->getByDepartment((int)$args['department_id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $cities
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ciudades del departamento: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getNearby(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            
            if (empty($queryParams['latitude']) || empty($queryParams['longitude'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Latitude y longitude son requeridos'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $radius = $queryParams['radius'] ?? 50;
            $cities = $this->cityService->getNearby(
                (float)$queryParams['latitude'],
                (float)$queryParams['longitude'],
                (float)$radius
            );
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $cities
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ciudades cercanas: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function search(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            
            if (empty($queryParams['name'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Parámetro name es requerido'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $cities = $this->cityService->searchByName($queryParams['name']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $cities
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al buscar ciudades: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
