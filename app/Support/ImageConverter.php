<?php

namespace App\Support;

/**
 * ImageConverter Helper - Konversi image file ke Base64 dengan error handling
 * 
 * Usage:
 *   $base64 = ImageConverter::toBase64('/path/to/image.jpg');
 *   $base64 = ImageConverter::toBase64('uploads/signatures/abc123.png');
 */
final class ImageConverter
{
    /**
     * Ekstensi file yang diizinkan
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * MIME type yang diizinkan
     */
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Ukuran maksimal file (default: 5 MB)
     */
    private const MAX_FILE_SIZE = 5 * 1024 * 1024;

    /**
     * Konversi image file ke Base64 string
     * 
     * @param string $filePath Path relatif atau absolut ke file
     * @param bool $strict Jika true, validasi MIME type ketat
     * @return string|null Base64 encoded string atau null jika error
     * @throws \RuntimeException Jika file tidak valid
     */
    public static function toBase64(string $filePath, bool $strict = true): ?string
    {
        // Normalize path
        $filePath = self::normalizePath($filePath);

        // Validasi file exists
        if (!is_file($filePath)) {
            throw new \RuntimeException("File not found: {$filePath}");
        }

        // Validasi file readable
        if (!is_readable($filePath)) {
            throw new \RuntimeException("File not readable: {$filePath}");
        }

        // Validasi file size
        $fileSize = filesize($filePath);
        if ($fileSize === false || $fileSize > self::MAX_FILE_SIZE) {
            throw new \RuntimeException("File too large (max " . (self::MAX_FILE_SIZE / 1024 / 1024) . " MB)");
        }

        // Validasi extension
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            throw new \RuntimeException("File extension not allowed: {$ext}");
        }

        // Validasi MIME type (strict mode)
        if ($strict) {
            $mimeType = self::detectMimeType($filePath);
            if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
                throw new \RuntimeException("MIME type not allowed: {$mimeType}");
            }
        }

        // Read file content
        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            throw new \RuntimeException("Failed to read file content: {$filePath}");
        }

        // Encode to Base64
        $base64 = base64_encode($fileContent);
        $mimeType = self::detectMimeType($filePath);

        // Return data URI format
        return "data:{$mimeType};base64,{$base64}";
    }

    /**
     * Convert Base64 string to file
     * 
     * @param string $base64Data Base64 encoded string (with atau tanpa data URI prefix)
     * @param string $outputDir Direktori output
     * @param string $filename Nama file output (auto-generate jika kosong)
     * @return string Path relatif file yang disimpan
     * @throws \RuntimeException Jika gagal menyimpan file
     */
    public static function fromBase64(string $base64Data, string $outputDir = 'uploads/signatures', string $filename = ''): string
    {
        // Strip data URI prefix jika ada
        if (strpos($base64Data, 'data:') === 0) {
            // Extract MIME type
            preg_match('/data:([^;]+);base64,(.+)/', $base64Data, $matches);
            if (empty($matches[2])) {
                throw new \RuntimeException("Invalid Base64 data URI format");
            }
            $base64Data = $matches[2];
            $mimeType = $matches[1] ?? 'image/png';
        }

        // Decode Base64
        $binaryData = base64_decode($base64Data, true);
        if ($binaryData === false) {
            throw new \RuntimeException("Failed to decode Base64 data");
        }

        // Determine extension
        $ext = self::getMimeExtension($mimeType ?? 'image/png');

        // Generate filename jika tidak disediakan
        if (empty($filename)) {
            $filename = self::generateFilename($ext);
        }

        // Ensure output directory exists
        $outputPath = self::normalizePath($outputDir);
        if (!is_dir($outputPath)) {
            if (!@mkdir($outputPath, 0755, true)) {
                throw new \RuntimeException("Failed to create output directory: {$outputPath}");
            }
        }

        // Save file
        $filePath = $outputPath . '/' . $filename;
        if (file_put_contents($filePath, $binaryData) === false) {
            throw new \RuntimeException("Failed to save file: {$filePath}");
        }

        // Return relative path
        return '/' . trim($outputDir, '/') . '/' . $filename;
    }

    /**
     * Detect MIME type dari file
     * 
     * @param string $filePath
     * @return string MIME type
     */
    private static function detectMimeType(string $filePath): string
    {
        // Try finfo first
        if (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($filePath);
            if ($mimeType !== false) {
                return strtolower($mimeType);
            }
        }

        // Fallback ke finfo_file
        if (function_exists('finfo_file')) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($filePath);
            if ($mimeType !== false) {
                return strtolower($mimeType);
            }
        }

        // Fallback ke extension mapping
        return self::getExtensionMimeType(pathinfo($filePath, PATHINFO_EXTENSION));
    }

    /**
     * Get MIME type dari extension
     * 
     * @param string $extension
     * @return string MIME type
     */
    private static function getExtensionMimeType(string $extension): string
    {
        $mimeMap = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];

        return $mimeMap[strtolower($extension)] ?? 'image/jpeg';
    }

    /**
     * Get extension dari MIME type
     * 
     * @param string $mimeType
     * @return string Extension
     */
    private static function getMimeExtension(string $mimeType): string
    {
        $extensionMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        return $extensionMap[strtolower($mimeType)] ?? 'jpg';
    }

    /**
     * Normalize file path (handle both relative dan absolute)
     * 
     * @param string $path
     * @return string Absolute path
     */
    private static function normalizePath(string $path): string
    {
        // Jika path sudah absolute, return as is
        if (is_absolute_path($path)) {
            return $path;
        }

        // Jika path relatif, prepend public directory
        $publicDir = dirname(__DIR__, 2) . '/public';
        return rtrim($publicDir, '/\\') . '/' . ltrim($path, '/\\');
    }

    /**
     * Generate unique filename
     * 
     * @param string $extension
     * @return string Filename
     */
    private static function generateFilename(string $extension): string
    {
        $timestamp = date('YmdHis');
        $random = bin2hex(random_bytes(8));
        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Check if string is absolute path
     * 
     * @param string $path
     * @return bool
     */
    private static function isAbsolutePath(string $path): bool
    {
        // Windows: C:\ or D:\, etc
        if (preg_match('/^[a-zA-Z]:/', $path)) {
            return true;
        }

        // Unix: /path
        if (strpos($path, '/') === 0) {
            return true;
        }

        return false;
    }

    /**
     * Check if file is image
     * 
     * @param string $filePath
     * @return bool
     */
    public static function isImage(string $filePath): bool
    {
        try {
            $mimeType = self::detectMimeType($filePath);
            return in_array($mimeType, self::ALLOWED_MIME_TYPES, true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get file info
     * 
     * @param string $filePath
     * @return array ['size' => bytes, 'mime' => mime_type, 'ext' => extension]
     */
    public static function getFileInfo(string $filePath): array
    {
        $filePath = self::normalizePath($filePath);

        return [
            'path' => $filePath,
            'exists' => is_file($filePath),
            'size' => is_file($filePath) ? filesize($filePath) : 0,
            'mime' => is_file($filePath) ? self::detectMimeType($filePath) : null,
            'ext' => strtolower(pathinfo($filePath, PATHINFO_EXTENSION)),
        ];
    }
}

// Helper function untuk is_absolute_path jika tidak tersedia
if (!function_exists('is_absolute_path')) {
    function is_absolute_path(string $path): bool
    {
        // Windows: C:\ or D:\, etc
        if (preg_match('/^[a-zA-Z]:/', $path)) {
            return true;
        }
        // Unix: /path
        if (strpos($path, '/') === 0) {
            return true;
        }
        return false;
    }
}
