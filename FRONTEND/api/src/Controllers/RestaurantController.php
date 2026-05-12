<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\RestaurantService;

class RestaurantController
{
    private RestaurantService $restaurantService;

    public function __construct(RestaurantService $restaurantService)
    {
        $this->restaurantService = $restaurantService;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $restaurants = $this->restaurantService->getAll();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $restaurants
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener restaurantes: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $restaurant = $this->restaurantService->getById((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $restaurant
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Restaurante no encontrado'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener restaurante: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validar datos requeridos
            $requiredFields = ['place_id', 'name'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "El campo {$field} es requerido"
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            $restaurant = $this->restaurantService->create($data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Restaurante creado exitosamente',
                'data' => $restaurant
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al crear restaurante: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody();
            $restaurant = $this->restaurantService->update((int)$args['id'], $data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Restaurante actualizado exitosamente',
                'data' => $restaurant
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Restaurante no encontrado'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar restaurante: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $this->restaurantService->delete((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Restaurante eliminado exitosamente'
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Restaurante no encontrado'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar restaurante: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByPlace(Request $request, Response $response, array $args): Response
    {
        try {
            $restaurants = $this->restaurantService->getByPlace((int)$args['place_id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $restaurants
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener restaurantes del lugar: ' . $e->getMessage()
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
            $restaurants = $this->restaurantService->getNearby(
                (float)$queryParams['latitude'],
                (float)$queryParams['longitude'],
                (float)$radius
            );
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $restaurants
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener restaurantes cercanos: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByBadge(Request $request, Response $response, array $args): Response
    {
        try {
            $restaurants = $this->restaurantService->getByBadge($args['badge']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $restaurants
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener restaurantes por badge: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function search(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            
            if (empty($queryParams['q'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Parámetro q es requerido'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $restaurants = $this->restaurantService->search($queryParams['q']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $restaurants
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al buscar restaurantes: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getStats(Request $request, Response $response): Response
    {
        try {
            $stats = $this->restaurantService->getRestaurantStats();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $stats
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // Métodos para manejo de imágenes
    public function addImage(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody();
            
            if (empty($data['image_url'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'image_url es requerido'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $image = $this->restaurantService->addImage((int)$args['restaurant_id'], $data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Imagen agregada exitosamente',
                'data' => $image
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al agregar imagen: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function updateImage(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody();
            $image = $this->restaurantService->updateImage((int)$args['image_id'], $data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Imagen actualizada exitosamente',
                'data' => $image
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Imagen no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar imagen: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function deleteImage(Request $request, Response $response, array $args): Response
    {
        try {
            $this->restaurantService->deleteImage((int)$args['image_id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Imagen eliminada exitosamente'
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Imagen no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar imagen: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function setMainImage(Request $request, Response $response, array $args): Response
    {
        try {
            $this->restaurantService->setMainImage((int)$args['restaurant_id'], (int)$args['image_id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Imagen principal establecida exitosamente'
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al establecer imagen principal: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
