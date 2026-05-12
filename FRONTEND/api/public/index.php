<?php

// Configuración de CORS para desarrollo
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// IMPORTANTE: Capturar el header Authorization antes de que Slim lo procese
// Apache a veces no pasa headers personalizados correctamente con FormData
// Esto asegura que el header esté disponible incluso si Slim no lo detecta
if (isset($_SERVER['HTTP_AUTHORIZATION']) && !isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = $_SERVER['HTTP_AUTHORIZATION'];
}
// También desde getallheaders() si está disponible
if (function_exists('getallheaders')) {
    $headers = getallheaders();
    if (isset($headers['Authorization']) || isset($headers['authorization'])) {
        $authHeader = $headers['Authorization'] ?? $headers['authorization'];
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $_SERVER['HTTP_AUTHORIZATION'] = $authHeader;
        }
        if (!isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = $authHeader;
        }
    }
}

// Incluir el autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';
use Slim\Middleware\MethodOverrideMiddleware;

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
try {
    $dotenv->load();
} catch (Exception $e) {
    // Si no existe .env, usar variables por defecto
    $_ENV['DB_HOST'] = $_ENV['DB_HOST'] ?? 'localhost';
    $_ENV['DB_NAME'] = $_ENV['DB_NAME'] ?? 'conoce_bolivia';
    $_ENV['DB_USER'] = $_ENV['DB_USER'] ?? 'root';
    $_ENV['DB_PASS'] = $_ENV['DB_PASS'] ?? 'root';
    $_ENV['JWT_SECRET'] = $_ENV['JWT_SECRET'] ?? 'your_jwt_secret_key_here_change_this_in_production';
    $_ENV['JWT_EXPIRATION'] = $_ENV['JWT_EXPIRATION'] ?? '86400';
}

// Configurar la base de datos
require_once __DIR__ . '/../config/database.php';

// Crear el container de dependencias
$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/container.php');
$container = $containerBuilder->build();

// Crear la aplicación Slim con el container
$app = Slim\Factory\AppFactory::createFromContainer($container);

// Soporte para method override (permitir POST con _METHOD=PUT o header X-HTTP-Method-Override)
$app->add(new MethodOverrideMiddleware());

// Middleware para parsing de JSON
$app->addBodyParsingMiddleware();

// Middleware de manejo de errores
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Cargar las rutas
require_once __DIR__ . '/../routes/api.php';

// Ejecutar la aplicación
$app->run();