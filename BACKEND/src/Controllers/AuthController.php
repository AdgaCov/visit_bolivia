<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\AuthService;
use App\Services\UserService;

class AuthController
{
    private AuthService $authService;
    private UserService $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function register(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validar datos requeridos
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Name, email y password son requeridos'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Verificar si el email ya existe
            $existingUser = \App\Models\User::where('email', $data['email'])->first();
            if ($existingUser) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'El email ya está registrado'
                ]));
                return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
            }

            $user = $this->authService->register($data);
            $token = $this->authService->generateToken($user);

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]));

            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al registrar usuario: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function login(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            if (empty($data['email']) || empty($data['password'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Email y password son requeridos'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $result = $this->authService->login($data['email'], $data['password']);

            if (!$result) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ]));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => $result
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al hacer login: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function me(Request $request, Response $response): Response
    {
        try {
            $user = $request->getAttribute('user');
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener usuario: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function googleAuth(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];
            
            if (empty($data['id_token'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'id_token es requerido'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $result = $this->authService->authenticateWithGoogle($data['id_token']);

            if (!$result) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'Token de Google inválido o expirado'
                ]));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Autenticación con Google exitosa',
                'data' => $result
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al autenticar con Google: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
