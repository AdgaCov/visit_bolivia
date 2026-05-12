<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\AccommodationService;

class AccommodationController
{
    private AccommodationService $accommodationService;

    public function __construct(AccommodationService $accommodationService)
    {
        $this->accommodationService = $accommodationService;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $accommodations = $this->accommodationService->getAll();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $accommodations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener alojamientos: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $accommodation = $this->accommodationService->getById((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $accommodation
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Alojamiento no encontrado'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener alojamiento: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validar datos requeridos
            $requiredFields = ['place_id', 'title', 'price_per_night'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "El campo {$field} es requerido"
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            $accommodation = $this->accommodationService->create($data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Alojamiento creado exitosamente',
                'data' => $accommodation
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al crear alojamiento: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody();
            $accommodation = $this->accommodationService->update((int)$args['id'], $data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Alojamiento actualizado exitosamente',
                'data' => $accommodation
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Alojamiento no encontrado'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar alojamiento: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $this->accommodationService->delete((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Alojamiento eliminado exitosamente'
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Alojamiento no encontrado'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar alojamiento: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByPlace(Request $request, Response $response, array $args): Response
    {
        try {
            $accommodations = $this->accommodationService->getByPlace((int)$args['place_id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $accommodations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener alojamientos del lugar: ' . $e->getMessage()
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
            $accommodations = $this->accommodationService->getNearby(
                (float)$queryParams['latitude'],
                (float)$queryParams['longitude'],
                (float)$radius
            );
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $accommodations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener alojamientos cercanos: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByPriceRange(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            
            if (empty($queryParams['min_price']) || empty($queryParams['max_price'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'min_price y max_price son requeridos'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $accommodations = $this->accommodationService->getByPriceRange(
                (float)$queryParams['min_price'],
                (float)$queryParams['max_price']
            );
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $accommodations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener alojamientos por rango de precio: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByCapacity(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            
            if (empty($queryParams['capacity'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'capacity es requerido'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $accommodations = $this->accommodationService->getByCapacity((int)$queryParams['capacity']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $accommodations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener alojamientos por capacidad: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByRoomType(Request $request, Response $response, array $args): Response
    {
        try {
            $accommodations = $this->accommodationService->getByRoomType($args['room_type']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $accommodations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener alojamientos por tipo de habitación: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByBadge(Request $request, Response $response, array $args): Response
    {
        try {
            $accommodations = $this->accommodationService->getByBadge($args['badge']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $accommodations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener alojamientos por badge: ' . $e->getMessage()
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

            $accommodations = $this->accommodationService->search($queryParams['q']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $accommodations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al buscar alojamientos: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getStats(Request $request, Response $response): Response
    {
        try {
            $stats = $this->accommodationService->getAccommodationStats();
            
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

            $image = $this->accommodationService->addImage((int)$args['accommodation_id'], $data);
            
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
            $image = $this->accommodationService->updateImage((int)$args['image_id'], $data);
            
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
            $this->accommodationService->deleteImage((int)$args['image_id']);
            
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
            $this->accommodationService->setMainImage((int)$args['accommodation_id'], (int)$args['image_id']);
            
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
