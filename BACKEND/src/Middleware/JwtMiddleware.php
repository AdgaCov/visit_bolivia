<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Services\AuthService;

class JwtMiddleware
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Intentar leer el header Authorization de múltiples formas
        // Porque algunos servidores no pasan headers personalizados correctamente con FormData
        $authHeader = $request->getHeaderLine('Authorization');
        
        // Si no se encuentra en los headers normales, intentar desde $_SERVER
        // Esto es necesario para algunos servidores Apache/PHP que no pasan headers correctamente con multipart/form-data
        if (empty($authHeader)) {
            // Apache puede almacenar el header como HTTP_AUTHORIZATION
            if (isset($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
                $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
            }
            // O como REDIRECT_HTTP_AUTHORIZATION si hay redirecciones
            elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            }
            // Intentar leer directamente desde getallheaders() que puede funcionar mejor
            elseif (function_exists('getallheaders')) {
                $headers = getallheaders();
                if (isset($headers['Authorization'])) {
                    $authHeader = $headers['Authorization'];
                } elseif (isset($headers['authorization'])) { // Algunos servidores envían en minúsculas
                    $authHeader = $headers['authorization'];
                }
            }
        }
        
        if (empty($authHeader)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Token de autorización requerido'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Extraer el token del header "Bearer TOKEN"
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Formato de token inválido'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $token = $matches[1];
        $user = $this->authService->getUserFromToken($token);

        if (!$user) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Token inválido o expirado'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Agregar el usuario al request para que esté disponible en los controladores
        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }
}
