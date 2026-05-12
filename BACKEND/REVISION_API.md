# Revisión de API Conoce Bolivia

## 📋 Resumen Ejecutivo

API desarrollada en PHP con Slim Framework 4, Eloquent ORM y autenticación JWT. Estructura bien organizada con separación de responsabilidades. Se identificaron varios puntos de mejora en seguridad, configuración y buenas prácticas.

---

## ✅ Aspectos Positivos

1. **Arquitectura MVC bien estructurada**
   - Separación clara entre Controllers, Services y Models
   - Uso de inyección de dependencias (PHP-DI)

2. **Seguridad básica**
   - Uso de Eloquent ORM (previene SQL injection)
   - Autenticación JWT implementada
   - Hash de contraseñas con `password_hash()`

3. **Código organizado**
   - Estructura de carpetas clara
   - Uso de namespaces apropiados
   - Middleware para autenticación

---

## ⚠️ Problemas Críticos de Seguridad

### 1. **CORS Demasiado Permisivo**
**Archivo:** `public/index.php` (líneas 4-6)

```php
header('Access-Control-Allow-Origin: *');
```

**Problema:** Permite acceso desde cualquier origen, lo cual es inseguro en producción.

**Recomendación:**
```php
// En producción, especificar dominios permitidos
$allowedOrigins = [
    'https://tudominio.com',
    'https://www.tudominio.com'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
}
```

### 2. **Exposición de Información de Errores en Producción**
**Archivo:** `public/index.php` (línea 70)

```php
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
```

**Problema:** El primer parámetro `true` habilita el display de errores, exponiendo información sensible en producción.

**Recomendación:**
```php
$displayErrorDetails = $_ENV['APP_ENV'] === 'development';
$errorMiddleware = $app->addErrorMiddleware(
    $displayErrorDetails,  // displayErrorDetails
    true,                  // logErrors
    true                   // logErrorDetails
);
```

### 3. **Secretos JWT por Defecto**
**Archivo:** `public/index.php` (líneas 48-49)

```php
$_ENV['JWT_SECRET'] = $_ENV['JWT_SECRET'] ?? 'your_jwt_secret_key_here_change_this_in_production';
```

**Problema:** Si no existe `.env`, usa un secreto por defecto conocido públicamente.

**Recomendación:**
```php
$jwtSecret = $_ENV['JWT_SECRET'] ?? null;
if (!$jwtSecret || $jwtSecret === 'your_jwt_secret_key_here_change_this_in_production') {
    throw new \RuntimeException('JWT_SECRET debe estar configurado en .env');
}
```

### 4. **Credenciales de Base de Datos por Defecto**
**Archivo:** `public/index.php` (líneas 44-47)

```php
$_ENV['DB_HOST'] = $_ENV['DB_HOST'] ?? 'localhost';
$_ENV['DB_NAME'] = $_ENV['DB_NAME'] ?? 'conoce_bolivia';
$_ENV['DB_USER'] = $_ENV['DB_USER'] ?? 'root';
$_ENV['DB_PASS'] = $_ENV['DB_PASS'] ?? 'root';
```

**Problema:** Credenciales por defecto inseguras expuestas en el código.

**Recomendación:** Forzar el uso de `.env` en producción:
```php
if ($_ENV['APP_ENV'] === 'production') {
    $required = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'JWT_SECRET'];
    foreach ($required as $key) {
        if (empty($_ENV[$key])) {
            throw new \RuntimeException("Variable de entorno $key es requerida");
        }
    }
}
```

---

## 🔒 Problemas de Seguridad Moderados

### 5. **Falta Validación de Roles en Middleware**
**Archivo:** `src/Middleware/JwtMiddleware.php`

**Problema:** El middleware solo valida el token, pero no verifica roles/permisos. Todos los usuarios autenticados tienen acceso a todas las rutas protegidas.

**Recomendación:** Crear middleware adicional para roles:
```php
class RoleMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler, array $roles): Response
    {
        $user = $request->getAttribute('user');
        if (!$user || !in_array($user->role, $roles, true)) {
            return new Response(403, [], json_encode([
                'success' => false,
                'message' => 'Acceso denegado'
            ]));
        }
        return $handler->handle($request);
    }
}
```

### 6. **Validación de Entrada Insuficiente**
**Archivo:** `src/Controllers/AuthController.php`, `src/Controllers/LocationController.php`

**Problema:** Validación básica de campos requeridos, pero falta validación de formato (email, longitud, tipos, etc.).

**Recomendación:** Usar `respect/validation` (ya está en composer.json):
```php
use Respect\Validation\Validator as v;

public function register(Request $request, Response $response): Response
{
    $data = $request->getParsedBody();
    
    $validator = v::key('name', v::stringType()->length(2, 100))
        ->key('email', v::email())
        ->key('password', v::stringType()->length(8, 100));
    
    try {
        $validator->assert($data);
    } catch (\Respect\Validation\Exceptions\ValidationException $e) {
        return $response->withStatus(400)->withJson([
            'success' => false,
            'message' => 'Datos inválidos',
            'errors' => $e->getMessages()
        ]);
    }
    // ... resto del código
}
```

### 7. **Falta Rate Limiting**
**Problema:** No hay protección contra ataques de fuerza bruta en login/registro.

**Recomendación:** Implementar rate limiting:
```php
// Usar middleware de rate limiting
use Tuupola\Middleware\RateLimitMiddleware;

$app->post('/api/auth/login', [AuthController::class, 'login'])
    ->add(new RateLimitMiddleware([
        'interval' => 60,
        'max' => 5
    ]));
```

### 8. **Upload de Archivos - Validación Mejorable**
**Archivo:** `src/Services/UploadService.php`

**Aspectos Positivos:**
- ✅ Validación de MIME types
- ✅ Validación de tamaño
- ✅ Redimensionamiento de imágenes
- ✅ Compresión

**Mejoras Recomendadas:**
- Agregar validación de contenido real del archivo (no solo extensión)
- Limitar tipos de archivo más estrictamente
- Agregar sanitización de nombres de archivo

```php
// Validar contenido real del archivo
private function validateImageContent(string $filePath): bool
{
    $imageInfo = @getimagesize($filePath);
    if ($imageInfo === false) {
        return false;
    }
    $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP];
    return in_array($imageInfo[2], $allowedTypes, true);
}
```

---

## 🛠️ Mejoras de Código y Buenas Prácticas

### 9. **Manejo de Errores Inconsistente**
**Problema:** Algunos métodos capturan excepciones genéricas, otros no. Los mensajes de error exponen detalles internos.

**Recomendación:** Crear excepciones personalizadas y manejo centralizado:
```php
class ApiException extends \Exception
{
    private int $statusCode;
    
    public function __construct(string $message, int $statusCode = 500)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }
    
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}

// En el error handler
$errorMiddleware->setDefaultErrorHandler(function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    $response = $app->getResponseFactory()->createResponse();
    
    $statusCode = $exception instanceof ApiException 
        ? $exception->getStatusCode() 
        : 500;
    
    $response->getBody()->write(json_encode([
        'success' => false,
        'message' => $displayErrorDetails 
            ? $exception->getMessage() 
            : 'Error interno del servidor'
    ]));
    
    return $response->withStatus($statusCode)
        ->withHeader('Content-Type', 'application/json');
});
```

### 10. **Falta de Logging Estructurado**
**Problema:** No se ve implementación de logging estructurado.

**Recomendación:** Usar Monolog (agregar a composer.json):
```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('api');
$logger->pushHandler(new StreamHandler('logs/app.log', Logger::DEBUG));

// En los servicios
try {
    // ... código
} catch (\Exception $e) {
    $logger->error('Error al crear ubicación', [
        'exception' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'data' => $data
    ]);
    throw $e;
}
```

### 11. **Falta Validación de Propiedad de Recursos**
**Archivo:** `src/Controllers/LocationController.php`

**Problema:** No se verifica si el usuario autenticado es el propietario del recurso antes de actualizar/eliminar.

**Recomendación:**
```php
public function update(Request $request, Response $response, array $args): Response
{
    $user = $request->getAttribute('user');
    $location = $this->locationService->getById((int)$args['id']);
    
    // Verificar propiedad o permisos
    if ($location->user_id !== $user->id && !$user->isAdmin()) {
        return $response->withStatus(403)->withJson([
            'success' => false,
            'message' => 'No tienes permiso para modificar este recurso'
        ]);
    }
    
    // ... resto del código
}
```

### 12. **Falta de Paginación Estándar**
**Problema:** Algunos endpoints tienen paginación, otros no. La implementación no es consistente.

**Recomendación:** Crear helper para paginación:
```php
trait PaginationTrait
{
    protected function paginate($query, Request $request): array
    {
        $page = max(1, (int)($request->getQueryParams()['page'] ?? 1));
        $perPage = min(100, max(1, (int)($request->getQueryParams()['per_page'] ?? 20)));
        
        $items = $query->skip(($page - 1) * $perPage)->take($perPage)->get();
        $total = $query->count();
        
        return [
            'data' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage)
            ]
        ];
    }
}
```

### 13. **Falta Archivo .env.example**
**Problema:** No hay archivo `.env.example` para documentar las variables de entorno requeridas.

**Recomendación:** Crear `.env.example`:
```env
APP_ENV=development
APP_DEBUG=true

DB_HOST=localhost
DB_NAME=conoce_bolivia
DB_USER=root
DB_PASS=root

JWT_SECRET=your_jwt_secret_key_here_change_this_in_production
JWT_EXPIRATION=86400

UPLOAD_PATH=public/uploads/
MAX_FILE_SIZE=15728640
IMAGE_PREFER_WEBP=true
IMAGE_JPEG_QUALITY=80
IMAGE_WEBP_QUALITY=80
IMAGE_PNG_COMPRESSION=6
MAX_IMAGE_DIMENSION=1280
```

### 14. **Falta Validación de Email Único en Registro**
**Archivo:** `src/Controllers/AuthController.php` (líneas 36-43)

**Problema:** La verificación de email duplicado se hace con query directa en el controlador, debería estar en el servicio.

**Recomendación:** Mover lógica al servicio:
```php
// En AuthService
public function emailExists(string $email): bool
{
    return User::where('email', $email)->exists();
}

// En AuthController
if ($this->authService->emailExists($data['email'])) {
    // ... error
}
```

### 15. **CORS Headers en Todas las Respuestas**
**Problema:** Los headers CORS solo se establecen en `index.php`, pero deberían estar en todas las respuestas.

**Recomendación:** Crear middleware para CORS:
```php
class CorsMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);
        
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowedOrigins = explode(',', $_ENV['CORS_ALLOWED_ORIGINS'] ?? '*');
        
        if ($origin && (in_array($origin, $allowedOrigins) || in_array('*', $allowedOrigins))) {
            $response = $response
                ->withHeader('Access-Control-Allow-Origin', $origin)
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
                ->withHeader('Access-Control-Allow-Credentials', 'true');
        }
        
        return $response;
    }
}
```

---

## 📝 Recomendaciones Adicionales

### 16. **Documentación API**
- Agregar Swagger/OpenAPI para documentación automática
- Usar `zircote/swagger-php` para anotaciones

### 17. **Testing**
- Agregar tests unitarios con PHPUnit
- Tests de integración para endpoints críticos

### 18. **Cache**
- Implementar cache para consultas frecuentes (Redis/Memcached)
- Cache de respuestas de endpoints públicos

### 19. **Validación de Datos Geográficos**
**Archivo:** `src/Controllers/LocationController.php`

**Problema:** No se valida que latitud y longitud estén en rangos válidos.

**Recomendación:**
```php
$latitude = (float)$data['latitude'];
$longitude = (float)$data['longitude'];

if ($latitude < -90 || $latitude > 90) {
    throw new ApiException('Latitud inválida', 400);
}

if ($longitude < -180 || $longitude > 180) {
    throw new ApiException('Longitud inválida', 400);
}
```

### 20. **Optimización de Consultas**
**Archivo:** `src/Services/LocationService.php`

**Problema:** Algunas consultas cargan relaciones innecesarias.

**Recomendación:** Usar eager loading selectivo:
```php
$query = Location::with([
    'department:id,name',
    'user:id,name,email',
    'images:id,location_id,image_url,is_main'
]);
```

---

## 🎯 Prioridades de Implementación

### 🔴 Alta Prioridad (Seguridad)
1. Configurar CORS apropiadamente
2. Deshabilitar display de errores en producción
3. Forzar variables de entorno en producción
4. Implementar rate limiting en auth

### 🟡 Media Prioridad (Funcionalidad)
5. Validación de entrada con Respect/Validation
6. Middleware de roles
7. Validación de propiedad de recursos
8. Logging estructurado

### 🟢 Baja Prioridad (Mejoras)
9. Paginación estándar
10. Documentación API
11. Testing
12. Cache

---

## 📊 Resumen

**Fortalezas:**
- ✅ Arquitectura sólida
- ✅ Uso de ORM (seguridad)
- ✅ Separación de responsabilidades

**Debilidades:**
- ⚠️ Configuración de seguridad para producción
- ⚠️ Validación de entrada insuficiente
- ⚠️ Falta de control de acceso por roles

**Calificación General:** 7/10

La API está bien estructurada pero necesita mejoras de seguridad antes de desplegar en producción.

