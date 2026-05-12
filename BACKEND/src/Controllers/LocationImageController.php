<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\UploadService;
use App\Models\LocationImage;
use Psr\Http\Message\UploadedFileInterface;

class LocationImageController
{
    private UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function index(Request $request, Response $response): Response
    {
        $images = \App\Models\LocationImage::with('location:id,name')->get();
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => $images
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody() ?? [];
            $uploadedFiles = $request->getUploadedFiles();
            if (isset($uploadedFiles['image']) && $uploadedFiles['image'] instanceof UploadedFileInterface) {
                $file = $uploadedFiles['image'];
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $data['image_url'] = $this->uploadService->saveLocationImage($file);
                }
            }
            // Validar location_id y image_url
            if (empty($data['location_id']) || empty($data['image_url'])) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'location_id/image (archivo) o image_url (string) son obligatorios.'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $isFirstImage = LocationImage::where('location_id', (int)$data['location_id'])->count() === 0;
            if ($isFirstImage) {
                $data['is_main'] = 1;
            } elseif (!isset($data['is_main'])) {
                $data['is_main'] = 0;
            }

            $image = LocationImage::create(array_intersect_key($data, array_flip(['location_id','image_url','alt_text','is_main'])));

            if (!empty($data['is_main'])) {
                $image->setAsMain();
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Imagen de location creada',
                'data' => $image
            ]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $image = LocationImage::findOrFail((int)$args['id']);
            $data = $request->getParsedBody() ?? [];
            $uploadedFiles = $request->getUploadedFiles();
            if (isset($uploadedFiles['image']) && $uploadedFiles['image'] instanceof UploadedFileInterface) {
                $file = $uploadedFiles['image'];
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $data['image_url'] = $this->uploadService->saveLocationImage($file);
                }
            }
            $image->update(array_intersect_key($data, array_flip(['location_id','image_url','alt_text','is_main'])));

            if (!empty($data['is_main'])) {
                $image->setAsMain();
            }

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Imagen de location actualizada',
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
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getByUserId(Request $request, Response $response, array $args): Response
    {
        try {
            $queryParams = $request->getQueryParams();
            $locationId = isset($queryParams['location_id']) ? (int)$queryParams['location_id'] : null;
            $isMain = isset($queryParams['is_main']) ? filter_var($queryParams['is_main'], FILTER_VALIDATE_BOOLEAN) : null;
            
            $query = LocationImage::whereHas('location', function($q) use ($args) {
                $q->where('user_id', (int)$args['user_id']);
            })->with('location:id,name,type,user_id');
            
            // Filtrar por location_id específica si se proporciona
            if ($locationId !== null) {
                $query->where('location_id', $locationId);
            }
            
            // Filtrar por is_main si se proporciona
            if ($isMain !== null) {
                $query->where('is_main', $isMain);
            }
            
            $images = $query->orderBy('is_main', 'desc')
                           ->orderBy('id', 'desc')
                           ->get();
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $images
            ]));
            
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Error al obtener imágenes del usuario: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function setMain(Request $request, Response $response, array $args): Response
    {
        try {
            $image = LocationImage::findOrFail((int)$args['id']);
            $image->setAsMain();

            $response->getBody()->write(json_encode([
                'success' => true,
                'message' => 'Imagen principal establecida exitosamente',
                'data' => $image->fresh()
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
                'message' => 'Error al marcar imagen principal: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $image = LocationImage::findOrFail((int)$args['id']);
            $imageUrl = $image->image_url;
            
            // Eliminar el archivo físico si existe y es una ruta local
            if (!empty($imageUrl) && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                // Es una ruta relativa desde public/, construir la ruta completa
                $projectRoot = dirname(__DIR__, 2);
                $publicPath = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
                
                // Normalizar separadores y construir la ruta completa
                $filePath = $publicPath . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($imageUrl, '/'));
                
                // Si el archivo existe, eliminarlo
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            
            // Eliminar el registro de la base de datos
            $image->delete();
            
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
}
