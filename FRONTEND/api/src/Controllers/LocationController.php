<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\LocationService;
use App\Services\LocationReviewService;
use App\Services\ArticleService;
use App\Services\UploadService;
use Psr\Http\Message\UploadedFileInterface;
use Carbon\Carbon;

class LocationController
{
    private LocationService $locationService;
    private LocationReviewService $locationReviewService;
    private ArticleService $articleService;
    private UploadService $uploadService;

    public function __construct(LocationService $locationService, LocationReviewService $locationReviewService, ArticleService $articleService, UploadService $uploadService)
    {
        $this->locationService = $locationService;
        $this->locationReviewService = $locationReviewService;
        $this->articleService = $articleService;
        $this->uploadService = $uploadService;
    }

    // ==========================
    // GENERAL LOCATION METHODS
    // ==========================

    public function index(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $type = $queryParams['type'] ?? null;
            
            $locations = $this->locationService->getAll($type);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $locations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $location = $this->locationService->getById((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $location
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
                'message' => 'Error al obtener ubicación: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validar datos requeridos
            $requiredFields = ['name', 'type', 'latitude', 'longitude'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "El campo {$field} es requerido"
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            // Obtener usuario autenticado
            $user = $request->getAttribute('user');
            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ]));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

            // Cargar plan del usuario si no está cargado
            if (!$user->relationLoaded('plan')) {
                $user->load('plan');
            }

            // Validar límite de locations según el plan
            if ($user->plan) {
                $currentLocationsCount = $user->locations()->count();
                $maxLocations = $user->plan->max_locations;
                
                if ($currentLocationsCount >= $maxLocations) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "Has alcanzado el límite de locations permitidas por tu plan ({$maxLocations}). Actualmente tienes {$currentLocationsCount} location(s).",
                        'current_count' => $currentLocationsCount,
                        'max_allowed' => $maxLocations,
                        'plan_name' => $user->plan->name
                    ]));
                    return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
                }
            } else {
                // Si no tiene plan, usar límite por defecto (1 location)
                $currentLocationsCount = $user->locations()->count();
                if ($currentLocationsCount >= 1) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "No tienes un plan activo. El límite por defecto es 1 location. Actualmente tienes {$currentLocationsCount} location(s).",
                        'current_count' => $currentLocationsCount,
                        'max_allowed' => 1
                    ]));
                    return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
                }
            }

            // Asignar user_id automáticamente si no viene en los datos
            if (empty($data['user_id'])) {
                $data['user_id'] = $user->id;
            } else {
                // Verificar que el user_id coincida con el usuario autenticado (a menos que sea admin)
                if ($data['user_id'] != $user->id && !$user->isAdmin()) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => 'No tienes permiso para crear locations para otro usuario'
                    ]));
                    return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
                }
            }

            $location = $this->locationService->create($data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Ubicación creada exitosamente',
                'data' => $location,
                'plan_info' => [
                    'current_locations' => $currentLocationsCount + 1,
                    'max_locations' => $user->plan ? $user->plan->max_locations : 1,
                    'remaining' => $user->plan ? ($user->plan->max_locations - ($currentLocationsCount + 1)) : 0
                ]
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al crear ubicación: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody();
            $location = $this->locationService->update((int)$args['id'], $data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Ubicación actualizada exitosamente',
                'data' => $location
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
                'message' => 'Error al actualizar ubicación: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $this->locationService->delete((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Ubicación eliminada exitosamente'
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
                'message' => 'Error al eliminar ubicación: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // ==========================
    // SEARCH AND FILTER METHODS
    // ==========================

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

            $type = $queryParams['type'] ?? null;
            $locations = $this->locationService->searchByName($queryParams['name'], $type);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $locations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al buscar ubicaciones: ' . $e->getMessage()
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
            $type = $queryParams['type'] ?? null;
            
            $locations = $this->locationService->getNearby(
                (float)$queryParams['latitude'],
                (float)$queryParams['longitude'],
                (float)$radius,
                $type
            );
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $locations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones cercanas: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByType(Request $request, Response $response, array $args): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $onlyWithReviews = filter_var($queryParams['only_with_reviews'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $minRating = isset($queryParams['min_rating']) ? max(1, min(5, (int)$queryParams['min_rating'])) : null;
            $locations = $this->locationService->getByType($args['type'], $onlyWithReviews, $minRating);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $locations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones por tipo: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByTypeWithReviews(Request $request, Response $response, array $args): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $minRating = isset($queryParams['min_rating']) ? max(1, min(5, (int)$queryParams['min_rating'])) : null;
            $locations = $this->locationService->getByType($args['type'], true, $minRating);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $locations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones por tipo con reseñas: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByDepartment(Request $request, Response $response, array $args): Response
    {
        try {
            $type = $request->getQueryParams()['type'] ?? null;
            $locations = $this->locationService->getByDepartment((int)$args['department_id'], $type);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $locations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones del departamento: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByUser(Request $request, Response $response, array $args): Response
    {
        try {
            $type = $request->getQueryParams()['type'] ?? null;
            $locations = $this->locationService->getByUser((int)$args['user_id'], $type);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $locations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones del usuario: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getBySubcategory(Request $request, Response $response, array $args): Response
    {
        try {
            $type = $request->getQueryParams()['type'] ?? null;
            $locations = $this->locationService->getBySubcategory((int)$args['subcategory_id'], $type);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $locations
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones por subcategoría: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getWithAnySubcategory(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $type = $queryParams['type'] ?? null;
            $departmentId = isset($queryParams['department']) ? (int)$queryParams['department'] : null;
            $limit = (int)($queryParams['limit'] ?? 50);
            $offset = (int)($queryParams['offset'] ?? 0);

            $result = $this->locationService->getWithAnySubcategory($type, $departmentId, $limit, $offset);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $result['data'],
                'pagination' => [
                    'total' => $result['total'],
                    'limit' => $result['limit'],
                    'offset' => $result['offset']
                ]
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones con subcategorías: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getArticlesByLocation(Request $request, Response $response, array $args): Response
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

            $articles = $this->articleService->getByLocationId((int)$args['id'], $limit, $offset, $type);

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
                'message' => 'Error al obtener artículos por location: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // ==========================
    // EVENT-SPECIFIC METHODS
    // ==========================

    public function getUpcomingEvents(Request $request, Response $response): Response
    {
        try {
            $events = $this->locationService->getUpcomingEvents();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $events
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener eventos próximos: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getRecurringEvents(Request $request, Response $response): Response
    {
        try {
            $events = $this->locationService->getRecurringEvents();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $events
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener eventos recurrentes: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getEventsByDateRange(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            
            if (empty($queryParams['start_date']) || empty($queryParams['end_date'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'start_date y end_date son requeridos'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $startDate = Carbon::parse($queryParams['start_date']);
            $endDate = Carbon::parse($queryParams['end_date']);
            
            $events = $this->locationService->getEventsByDateRange($startDate, $endDate);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $events
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener eventos por rango de fechas: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getTodayEvents(Request $request, Response $response): Response
    {
        try {
            $events = $this->locationService->getTodayEvents();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $events
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener eventos de hoy: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getThisWeekEvents(Request $request, Response $response): Response
    {
        try {
            $events = $this->locationService->getThisWeekEvents();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $events
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener eventos de esta semana: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getThisMonthEvents(Request $request, Response $response): Response
    {
        try {
            $events = $this->locationService->getThisMonthEvents();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $events
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener eventos de este mes: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // ==========================
    // RESTAURANT-SPECIFIC METHODS
    // ==========================

    public function getRestaurantsByBadge(Request $request, Response $response, array $args): Response
    {
        try {
            $restaurants = $this->locationService->getRestaurantsByBadge($args['badge']);
            
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

    // ==========================
    // ACCOMMODATION-SPECIFIC METHODS
    // ==========================

    public function getAccommodationsByRoomType(Request $request, Response $response, array $args): Response
    {
        try {
            $accommodations = $this->locationService->getAccommodationsByRoomType($args['room_type']);
            
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

    public function getAccommodationsByPriceRange(Request $request, Response $response): Response
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

            $accommodations = $this->locationService->getAccommodationsByPriceRange(
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

    public function getAccommodationsByCapacity(Request $request, Response $response): Response
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

            $accommodations = $this->locationService->getAccommodationsByCapacity((int)$queryParams['capacity']);
            
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

    // ==========================
    // FEATURED PLACES METHODS
    // ==========================

    public function getFeaturedPlaces(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $limit = (int)($queryParams['limit'] ?? 6);
            
            $featuredPlaces = $this->locationReviewService->getFeaturedPlaces($limit);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $featuredPlaces
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener lugares destacados: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getWithReviews(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $type = $queryParams['type'] ?? null;
            $minRating = isset($queryParams['min_rating']) ? max(1, min(5, (int)$queryParams['min_rating'])) : null;
            $limit = isset($queryParams['limit']) ? max(1, (int)$queryParams['limit']) : 50;
            $offset = isset($queryParams['offset']) ? max(0, (int)$queryParams['offset']) : 0;

            $result = $this->locationService->getWithReviews($type, $minRating, $limit, $offset);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $result['data'],
                'pagination' => [
                    'total' => $result['total'],
                    'limit' => $result['limit'],
                    'offset' => $result['offset']
                ]
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones con reseñas: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // ==========================
    // STATISTICS METHODS
    // ==========================

    public function getStats(Request $request, Response $response): Response
    {
        try {
            $type = $request->getQueryParams()['type'] ?? null;
            $stats = $this->locationService->getStats($type);
            
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

    public function getPopularFavorites(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $limit = isset($queryParams['limit']) ? max(1, min(100, (int)$queryParams['limit'])) : 10;
            $type = $queryParams['type'] ?? null;

            $locations = $this->locationService->getPopularByFavorites($limit, $type);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $locations,
                'meta' => [
                    'limit' => $limit,
                    'type' => $type,
                ]
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener ubicaciones populares: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    // ==========================
    // IMAGE MANAGEMENT METHODS
    // ==========================

    public function addImage(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];

            // Aceptar multipart/form-data: si llega archivo 'image', subir y setear image_url
            $uploadedFiles = $request->getUploadedFiles();
            if (isset($uploadedFiles['image']) && $uploadedFiles['image'] instanceof UploadedFileInterface) {
                $file = $uploadedFiles['image'];
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $data['image_url'] = $this->uploadService->saveLocationImage($file);
                }
            }

            if (empty($data['image_url'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Debes enviar image (archivo) o image_url (string)'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $image = $this->locationService->addImage((int)$args['id'], $data);
            
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
            $data = $request->getParsedBody() ?? [];

            // Aceptar multipart en update: si llega archivo, reemplazar image_url
            $uploadedFiles = $request->getUploadedFiles();
            if (isset($uploadedFiles['image']) && $uploadedFiles['image'] instanceof UploadedFileInterface) {
                $file = $uploadedFiles['image'];
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $data['image_url'] = $this->uploadService->saveLocationImage($file);
                }
            }
            $image = $this->locationService->updateImage((int)$args['image_id'], $data);
            
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
            $this->locationService->deleteImage((int)$args['image_id']);
            
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
            $this->locationService->setMainImage((int)$args['id'], (int)$args['image_id']);
            
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

