<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\LocationReviewService;
use App\Models\LocationReview;

class LocationReviewController
{
    private LocationReviewService $reviewService;

    public function __construct(LocationReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $locationId = isset($queryParams['location_id']) ? (int)$queryParams['location_id'] : null;
            $userId = isset($queryParams['user_id']) ? (int)$queryParams['user_id'] : null;
            $limit = isset($queryParams['limit']) ? max(1, min(100, (int)$queryParams['limit'])) : 50;
            $offset = isset($queryParams['offset']) ? max(0, (int)$queryParams['offset']) : 0;

            $result = $this->reviewService->getAll($locationId, $userId, $limit, $offset);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $result,
                'pagination' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'count' => count($result),
                ]
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener reseñas: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByLocation(Request $request, Response $response, array $args): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $limit = isset($queryParams['limit']) ? max(1, min(100, (int)$queryParams['limit'])) : 50;
            $offset = isset($queryParams['offset']) ? max(0, (int)$queryParams['offset']) : 0;
            
            $locationId = (int)$args['location_id'];
            $result = $this->reviewService->getAll($locationId, null, $limit, $offset);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $result,
                'pagination' => [
                    'limit' => $limit,
                    'offset' => $offset,
                    'count' => count($result),
                    'location_id' => $locationId
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
                'message' => 'Error al obtener reseñas de la ubicación: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $user = $request->getAttribute('user');
            $data = $request->getParsedBody() ?? [];

            $required = ['location_id', 'rating'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => "El campo {$field} es requerido"
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
            }

            $rating = (int)$data['rating'];
            if ($rating < 1 || $rating > 5) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'El rating debe estar entre 1 y 5'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $data['user_id'] = $user->id;
            $review = $this->reviewService->create([
                'location_id' => (int)$data['location_id'],
                'user_id' => $user->id,
                'rating' => $rating,
                'comment' => $data['comment'] ?? null,
            ]);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Reseña creada exitosamente',
                'data' => $review->load(['user', 'location'])
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Ubicación no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al crear reseña: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $user = $request->getAttribute('user');
            $data = $request->getParsedBody() ?? [];
            $review = $this->reviewService->findById((int)$args['id']);

            if ($review->user_id !== $user->id) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'No tienes permiso para actualizar esta reseña'
                ]));
                return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
            }

            $updateData = [];
            if (isset($data['rating'])) {
                $rating = (int)$data['rating'];
                if ($rating < 1 || $rating > 5) {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'message' => 'El rating debe estar entre 1 y 5'
                    ]));
                    return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
                }
                $updateData['rating'] = $rating;
            }
            if (array_key_exists('comment', $data)) {
                $updateData['comment'] = $data['comment'];
            }

            if (empty($updateData)) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'No se enviaron datos para actualizar'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $updated = $this->reviewService->update($review->id, $updateData);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Reseña actualizada exitosamente',
                'data' => $updated
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Reseña no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar reseña: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $user = $request->getAttribute('user');
            $review = $this->reviewService->findById((int)$args['id']);

            if ($review->user_id !== $user->id) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar esta reseña'
                ]));
                return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
            }

            $this->reviewService->delete($review->id);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Reseña eliminada exitosamente'
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Reseña no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar reseña: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}

