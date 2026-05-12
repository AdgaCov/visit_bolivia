<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\LocationPolicyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationPolicyController
{
    private LocationPolicyService $locationPolicyService;

    public function __construct(LocationPolicyService $locationPolicyService)
    {
        $this->locationPolicyService = $locationPolicyService;
    }

    public function index(Request $request, Response $response): Response
    {
        try {
            $policies = $this->locationPolicyService->getAll();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $policies
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener políticas: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $policy = $this->locationPolicyService->getById((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $policy
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Política no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener política: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByLocation(Request $request, Response $response, array $args): Response
    {
        try {
            $policy = $this->locationPolicyService->getByLocation((int)$args['location_id']);
            
            if (!$policy) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'No se encontró política para esta location',
                    'data' => null
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $policy
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener política por location: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByUserId(Request $request, Response $response, array $args): Response
    {
        try {
            $policies = $this->locationPolicyService->getByUserId((int)$args['user_id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $policies
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener políticas del usuario: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];
            
            // Validar location_id requerido
            if (empty($data['location_id'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'El campo location_id es requerido'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Convertir valores booleanos de string a boolean si es necesario
            $booleanFields = [
                'reservation_recommended',
                'accepts_cash',
                'accepts_credit_card',
                'accepts_debit_card',
                'accepts_bank_transfer',
                'accepts_digital_wallet'
            ];

            foreach ($booleanFields as $field) {
                if (isset($data[$field])) {
                    $data[$field] = filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }

            $policy = $this->locationPolicyService->create($data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Política creada exitosamente',
                'data' => $policy->load('location:id,name,type')
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al crear política: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];

            // Convertir valores booleanos de string a boolean si es necesario
            $booleanFields = [
                'reservation_recommended',
                'accepts_cash',
                'accepts_credit_card',
                'accepts_debit_card',
                'accepts_bank_transfer',
                'accepts_digital_wallet'
            ];

            foreach ($booleanFields as $field) {
                if (isset($data[$field])) {
                    $data[$field] = filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }

            $policy = $this->locationPolicyService->update((int)$args['id'], $data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Política actualizada exitosamente',
                'data' => $policy
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Política no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar política: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function updateOrCreate(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];
            $locationId = (int)$args['location_id'];

            // Convertir valores booleanos de string a boolean si es necesario
            $booleanFields = [
                'reservation_recommended',
                'accepts_cash',
                'accepts_credit_card',
                'accepts_debit_card',
                'accepts_bank_transfer',
                'accepts_digital_wallet'
            ];

            foreach ($booleanFields as $field) {
                if (isset($data[$field])) {
                    $data[$field] = filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }

            $policy = $this->locationPolicyService->updateOrCreateByLocation($locationId, $data);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Política actualizada o creada exitosamente',
                'data' => $policy->load('location:id,name,type')
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al actualizar o crear política: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $this->locationPolicyService->delete((int)$args['id']);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Política eliminada exitosamente'
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Política no encontrada'
            ]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al eliminar política: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}

