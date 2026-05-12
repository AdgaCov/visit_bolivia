<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\LocationFavoriteService;

class LocationFavoriteController
{
    private LocationFavoriteService $favoriteService;

    public function __construct(LocationFavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $user = $request->getAttribute('user');

            $favorites = $this->favoriteService->getByUser($user->id);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $favorites
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener favoritos: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getAll(Request $request, Response $response): Response
    {
        try {
            $favorites = $this->favoriteService->getAll();

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $favorites,
                'count' => $favorites->count()
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener todos los favoritos: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $user = $request->getAttribute('user');
            $data = $request->getParsedBody() ?? [];

            if (empty($data['location_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'location_id es requerido'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $favorite = $this->favoriteService->addFavorite($user->id, (int)$data['location_id']);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Ubicación agregada a favoritos',
                'data' => $favorite
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
                'message' => 'Error al agregar favorito: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $user = $request->getAttribute('user');
            $locationId = (int)$args['location_id'];

            $this->favoriteService->removeFavorite($user->id, $locationId);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Ubicación eliminada de favoritos'
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Favorito no encontrado'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar favorito: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function toggle(Request $request, Response $response): Response
    {
        try {
            $user = $request->getAttribute('user');
            $data = $request->getParsedBody() ?? [];

            if (empty($data['location_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'location_id es requerido'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $result = $this->favoriteService->toggleFavorite($user->id, (int)$data['location_id']);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => $result['favorited'] ? 'Ubicación agregada a favoritos' : 'Ubicación eliminada de favoritos',
                'data' => $result
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
                'message' => 'Error al actualizar favorito: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}

