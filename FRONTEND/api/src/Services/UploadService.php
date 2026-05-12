<?php

namespace App\Services;

use Psr\Http\Message\UploadedFileInterface;

class UploadService
{
    private string $baseUploadPath;
    private int $maxFileSize;
    private bool $preferWebp;
    private int $defaultJpegQuality;
    private int $defaultWebpQuality;
    private int $defaultPngCompression;
    private int $maxDimension;

    public function __construct()
    {
        $projectRoot = dirname(__DIR__, 2); // .../api_conoce_bolivia
        $configured = $_ENV['UPLOAD_PATH'] ?? 'public/uploads/';
        $configured = rtrim($configured, "/\\");

        // Si la ruta no es absoluta, construirla desde la raíz del proyecto
        if (!$this->isAbsolutePath($configured)) {
            $this->baseUploadPath = $projectRoot . DIRECTORY_SEPARATOR . $configured . DIRECTORY_SEPARATOR;
        } else {
            $this->baseUploadPath = $configured . DIRECTORY_SEPARATOR;
        }

        $this->maxFileSize = (int)($_ENV['MAX_FILE_SIZE'] ?? 15 * 1024 * 1024);
        $this->preferWebp = filter_var($_ENV['IMAGE_PREFER_WEBP'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $this->defaultJpegQuality = (int)($_ENV['IMAGE_JPEG_QUALITY'] ?? 80);
        $this->defaultWebpQuality = (int)($_ENV['IMAGE_WEBP_QUALITY'] ?? 80);
        $this->defaultPngCompression = (int)($_ENV['IMAGE_PNG_COMPRESSION'] ?? 6);
        $this->maxDimension = (int)($_ENV['MAX_IMAGE_DIMENSION'] ?? 1280);
    }

    public function saveArticleImage(UploadedFileInterface $uploadedFile, ?int $jpegQuality = null, ?int $webpQuality = null, ?int $pngCompression = null): string
    {
        return $this->saveImageToSubdir('articles', $uploadedFile, $jpegQuality, $webpQuality, $pngCompression);
    }

    public function saveLocationImage(UploadedFileInterface $uploadedFile, ?int $jpegQuality = null, ?int $webpQuality = null, ?int $pngCompression = null): string
    {
        return $this->saveImageToSubdir('locations', $uploadedFile, $jpegQuality, $webpQuality, $pngCompression);
    }

    public function saveCategoryImage(UploadedFileInterface $uploadedFile, ?int $jpegQuality = null, ?int $webpQuality = null, ?int $pngCompression = null): string
    {
        return $this->saveImageToSubdir('categories', $uploadedFile, $jpegQuality, $webpQuality, $pngCompression);
    }

    public function saveLocationItemImage(UploadedFileInterface $uploadedFile, ?int $jpegQuality = null, ?int $webpQuality = null, ?int $pngCompression = null): string
    {
        return $this->saveImageToSubdir('location-items', $uploadedFile, $jpegQuality, $webpQuality, $pngCompression);
    }

    public function saveSubcategoryImage(UploadedFileInterface $uploadedFile, ?int $jpegQuality = null, ?int $webpQuality = null, ?int $pngCompression = null): string
    {
        return $this->saveImageToSubdir('categories', $uploadedFile, $jpegQuality, $webpQuality, $pngCompression);
    }

    private function saveImageToSubdir(string $subdir, UploadedFileInterface $uploadedFile, ?int $jpegQuality, ?int $webpQuality, ?int $pngCompression): string
    {
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Error al subir la imagen');
        }

        $size = $uploadedFile->getSize();
        if ($size !== null && $size > $this->maxFileSize) {
            throw new \RuntimeException('La imagen excede el tamaño máximo permitido de 15 MB');
        }

        $mimeType = $uploadedFile->getClientMediaType() ?? '';
        $originalName = $uploadedFile->getClientFilename() ?? 'image';
        $sourceExtension = $this->guessExtension($mimeType, $originalName);
        
        // Validar MIME types permitidos
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($mimeType, $allowedMimes, true)) {
            throw new \RuntimeException('Tipo de archivo no permitido. Solo se aceptan imágenes JPEG, PNG o WebP');
        }

        $targetDir = $this->baseUploadPath . $subdir . DIRECTORY_SEPARATOR;
        $this->ensureDirectory($targetDir);

        // Decidir formato de salida (preferir WEBP si está disponible)
        $targetExtension = $this->decideTargetExtension($mimeType, $sourceExtension);
        $filename = $this->generateFilename($targetExtension);
        $targetPath = $targetDir . $filename;

        $stream = $uploadedFile->getStream();
        $tmpPath = $this->createTempFile($sourceExtension);
        file_put_contents($tmpPath, (string)$stream);

        $saved = $this->compressPreservingDimensions(
            $tmpPath,
            $targetPath,
            $mimeType,
            $targetExtension,
            $jpegQuality ?? $this->defaultJpegQuality,
            $webpQuality ?? $this->defaultWebpQuality,
            $pngCompression ?? $this->defaultPngCompression
        );

        if (!$saved) {
            // Intentar fallback a otro formato (ej. de webp -> jpeg)
            $altExt = $targetExtension === 'webp' ? 'jpg' : (function_exists('imagewebp') ? 'webp' : 'jpg');
            if ($altExt !== $targetExtension) {
                $altFilename = $this->generateFilename($altExt);
                $altPath = $targetDir . $altFilename;
                $saved = $this->compressPreservingDimensions(
                    $tmpPath,
                    $altPath,
                    $mimeType,
                    $altExt,
                    $jpegQuality ?? $this->defaultJpegQuality,
                    $webpQuality ?? $this->defaultWebpQuality,
                    $pngCompression ?? $this->defaultPngCompression
                );
                if ($saved) {
                    $targetPath = $altPath;
                }
            }

            if (!$saved) {
                // Último fallback: mover sin recomprimir
                try {
                    $uploadedFile->moveTo($targetPath);
                    $saved = true;
                } catch (\Exception $e) {
                    @unlink($tmpPath);
                    throw new \RuntimeException('No se pudo guardar la imagen: ' . $e->getMessage());
                }
            }
        }

        // Limpiar archivo temporal
        if (file_exists($tmpPath)) {
            @unlink($tmpPath);
        }

        $relative = $this->toPublicRelativePath($targetPath);
        return $relative;
    }

    private function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new \RuntimeException('No se pudo crear el directorio de subida');
            }
        }
    }

    private function generateFilename(string $extension): string
    {
        $random = bin2hex(random_bytes(6));
        return 'article_' . date('Ymd_His') . '_' . $random . '.' . $extension;
    }

    private function guessExtension(string $mimeType, string $fallbackName): string
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (isset($map[$mimeType])) {
            return $map[$mimeType];
        }
        $ext = pathinfo($fallbackName, PATHINFO_EXTENSION);
        return $ext !== '' ? strtolower($ext) : 'jpg';
    }

    private function createTempFile(string $extension): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'upload_');
        $new = $tmp . '.' . $extension;
        rename($tmp, $new);
        return $new;
    }
    private function compressPreservingDimensions(string $sourcePath, string $targetPath, string $mimeType, string $targetExtension, int $jpegQuality, int $webpQuality, int $pngCompression): bool
{
    if (!extension_loaded('gd')) {
        return false;
    }

    $info = @getimagesize($sourcePath);
    if (!$info) {
        return false;
    }

    [$width, $height, $type] = $info;

    // Validar dimensiones máximas razonables para evitar DoS
    if ($width > 10000 || $height > 10000) {
        return false;
    }

    // Cargar imagen según tipo
    switch ($type) {
        case IMAGETYPE_JPEG:
            $img = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $img = imagecreatefrompng($sourcePath);
            imagepalettetotruecolor($img);
            imagealphablending($img, false);
            imagesavealpha($img, true);
            break;
        case IMAGETYPE_WEBP:
            $img = function_exists('imagecreatefromwebp') ? imagecreatefromwebp($sourcePath) : false;
            break;
        default:
            return false;
    }

    if (!$img) {
        return false;
    }

    // Calcular nuevas dimensiones si es necesario (redimensionamiento proporcional)
    $newWidth = $width;
    $newHeight = $height;
    
    if ($width > $this->maxDimension || $height > $this->maxDimension) {
        if ($width >= $height) {
            // Imagen horizontal o cuadrada
            $newWidth = $this->maxDimension;
            $newHeight = intval($height * ($this->maxDimension / $width));
        } else {
            // Imagen vertical
            $newHeight = $this->maxDimension;
            $newWidth = intval($width * ($this->maxDimension / $height));
        }
    }

    // Crear canvas con las nuevas dimensiones
    $canvas = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preservar transparencia
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);
        imagealphablending($canvas, true);
    }
    
    // Redimensionar imagen
    imagecopyresampled($canvas, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    imagedestroy($img);

    // Guardar según formato
    $saved = false;
    switch ($targetExtension) {
        case 'webp':
            if (function_exists('imagewebp')) {
                $saved = imagewebp($canvas, $targetPath, $webpQuality);
            }
            break;

        case 'jpg':
        case 'jpeg':
            // Para JPEG, eliminar transparencia y usar fondo blanco
            if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
                $final = imagecreatetruecolor($newWidth, $newHeight);
                $white = imagecolorallocate($final, 255, 255, 255);
                imagefill($final, 0, 0, $white);
                imagecopy($final, $canvas, 0, 0, 0, 0, $newWidth, $newHeight);
                imagedestroy($canvas);
                $canvas = $final;
            }
            imageinterlace($canvas, true);
            $saved = imagejpeg($canvas, $targetPath, $jpegQuality);
            break;

        case 'png':
            imagesavealpha($canvas, true);
            $saved = imagepng($canvas, $targetPath, $pngCompression);
            break;
    }

    imagedestroy($canvas);
    return $saved;
}


    // private function compressPreservingDimensions(string $sourcePath, string $targetPath, string $mimeType, string $targetExtension, int $jpegQuality, int $webpQuality, int $pngCompression): bool
    // {
    //     if (!extension_loaded('gd')) {
    //         return false;
    //     }

    //     $imageInfo = @getimagesize($sourcePath);
    //     if ($imageInfo === false) {
    //         return false;
    //     }
    //     $type = $imageInfo[2];

    //     // Cargar la imagen en memoria
    //     switch ($type) {
    //         case IMAGETYPE_JPEG:
    //             $img = @imagecreatefromjpeg($sourcePath);
    //             break;
    //         case IMAGETYPE_PNG:
    //             $img = @imagecreatefrompng($sourcePath);
    //             // Asegurar canal alfa conservado
    //             if ($img) {
    //                 imagealphablending($img, false);
    //                 imagesavealpha($img, true);
    //             }
    //             break;
    //         case IMAGETYPE_WEBP:
    //             if (!function_exists('imagecreatefromwebp')) {
    //                 return false;
    //             }
    //             $img = @imagecreatefromwebp($sourcePath);
    //             break;
    //         default:
    //             return false;
    //     }

    //     if (!$img) {
    //         return false;
    //     }

    //     // Guardar según formato destino
    //     $saved = false;
    //     if ($targetExtension === 'webp' && function_exists('imagewebp')) {
    //         $saved = imagewebp($img, $targetPath, max(0, min(100, $webpQuality)));
    //     } elseif ($targetExtension === 'jpg' || $targetExtension === 'jpeg') {
    //         // Hacer JPEG progresivo
    //         imageinterlace($img, true);
    //         $saved = imagejpeg($img, $targetPath, max(0, min(100, $jpegQuality)));
    //     } elseif ($targetExtension === 'png') {
    //         $level = max(0, min(9, $pngCompression));
    //         imagesavealpha($img, true);
    //         $saved = imagepng($img, $targetPath, $level);
    //     }

    //     imagedestroy($img);
    //     return $saved;
    // }

    private function decideTargetExtension(string $mimeType, string $sourceExtension): string
    {
        if ($this->preferWebp && function_exists('imagewebp')) {
            return 'webp';
        }
        $ext = strtolower($sourceExtension);
        if (in_array($ext, ['jpg','jpeg','png','webp'], true)) {
            return $ext === 'jpeg' ? 'jpg' : $ext;
        }
        return 'jpg';
    }

    private function isAbsolutePath(string $path): bool
    {
        // Windows (C:\ or \\server\share) o Unix (/...)
        return (bool)preg_match('/^(?:[a-zA-Z]:\\\\|\\\\\\\\|\/)/', $path);
    }

    private function toPublicRelativePath(string $absolutePath): string
    {
        $projectRoot = dirname(__DIR__, 2);
        $publicRoot = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;

        // Normalizar separadores
        $normalized = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $absolutePath);
        $normalizedPublic = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $publicRoot);

        if (strpos($normalized, $normalizedPublic) === 0) {
            $rel = substr($normalized, strlen($normalizedPublic));
        } else {
            // Como fallback, quitar "public/" si está duplicado o devolver desde baseUploadPath
            $rel = str_replace($normalizedPublic, '', $normalized);
        }

        return str_replace(['\\', DIRECTORY_SEPARATOR], '/', $rel);
    }
}

