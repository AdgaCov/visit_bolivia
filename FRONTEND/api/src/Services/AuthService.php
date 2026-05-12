<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthService
{
    private string $jwtSecret;
    private int $jwtExpiration;

    public function __construct()
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'default_secret';
        $this->jwtExpiration = (int)($_ENV['JWT_EXPIRATION'] ?? 86400);
    }

    public function register(array $data): User
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['status'] = $data['status'] ?? 1;
        
        // Si se envía role como string, buscar el role_id
        if (isset($data['role']) && is_string($data['role'])) {
            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $data['role_id'] = $role->id;
            } else {
                // Por defecto, buscar el rol 'user'
                $defaultRole = Role::where('name', 'user')->first();
                $data['role_id'] = $defaultRole ? $defaultRole->id : 1;
            }
            unset($data['role']);
        } elseif (!isset($data['role_id'])) {
            // Si no se especifica role ni role_id, usar 'user' por defecto
            $defaultRole = Role::where('name', 'user')->first();
            $data['role_id'] = $defaultRole ? $defaultRole->id : 4;
        }

        return User::create($data);
    }

    public function login(string $email, string $password): ?array
    {
        try {
            $user = User::where('email', $email)->firstOrFail();
            
            if (!password_verify($password, $user->password)) {
                return null;
            }

            if (!$user->status) {
                throw new \Exception('Usuario inactivo');
            }

            $token = $this->generateToken($user);

            return [
                'user' => $user,
                'token' => $token
            ];
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    public function generateToken(User $user): string
    {
        // Cargar la relación role si no está cargada
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        $payload = [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role ? $user->role->name : null,
            'role_id' => $user->role_id,
            'iat' => time(),
            'exp' => time() + $this->jwtExpiration
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getUserFromToken(string $token): ?User
    {
        $payload = $this->validateToken($token);
        
        if (!$payload) {
            return null;
        }

        try {
            return User::findOrFail($payload['user_id']);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Autenticar o registrar usuario con Google
     * @param string $idToken El ID token de Google
     * @return array|null Array con 'user' y 'token', o null si falla
     */
    public function authenticateWithGoogle(string $idToken): ?array
    {
        try {
            // Validar el token con Google
            $googleUser = $this->verifyGoogleToken($idToken);
            
            if (!$googleUser) {
                return null;
            }

            $googleId = $googleUser['sub'];
            $email = $googleUser['email'];
            $name = $googleUser['name'] ?? $googleUser['given_name'] ?? 'Usuario Google';
            $picture = $googleUser['picture'] ?? null;

            // Buscar usuario por google_id o email
            $user = User::where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();

            if ($user) {
                // Usuario existe, actualizar google_id si no lo tiene
                if (empty($user->google_id)) {
                    $user->google_id = $googleId;
                    $user->save();
                }

                // Actualizar nombre si viene de Google
                if (!empty($name) && $user->name !== $name) {
                    $user->name = $name;
                    $user->save();
                }
            } else {
                // Crear nuevo usuario
                $defaultRole = Role::where('name', 'user')->first();
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'role_id' => $defaultRole ? $defaultRole->id : 4,
                    'status' => 1,
                    'password' => '' // Usuarios de Google no tienen password
                ]);
            }

            if (!$user->status) {
                throw new \Exception('Usuario inactivo');
            }

            $token = $this->generateToken($user);

            return [
                'user' => $user->fresh('role'),
                'token' => $token
            ];
        } catch (\Exception $e) {
            error_log('Error en authenticateWithGoogle: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar el ID token de Google
     * @param string $idToken El ID token de Google
     * @return array|null Datos del usuario de Google o null si es inválido
     */
    private function verifyGoogleToken(string $idToken): ?array
    {
        try {
            // Verificar el token con Google
            $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$response) {
                return null;
            }

            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            // Verificar que el token es válido
            if (isset($data['error'])) {
                return null;
            }

            // Verificar que tiene los campos necesarios
            if (empty($data['sub']) || empty($data['email'])) {
                return null;
            }

            // Opcional: Verificar el audience (client_id) si está configurado
            $googleClientId = $_ENV['GOOGLE_CLIENT_ID'] ?? null;
            if ($googleClientId && isset($data['aud']) && $data['aud'] !== $googleClientId) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            error_log('Error verificando token de Google: ' . $e->getMessage());
            return null;
        }
    }
}
