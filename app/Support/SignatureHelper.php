<?php

namespace App\Support;

/**
 * SignatureHelper - Helper untuk menangani signature dengan error handling dan logging
 * 
 * Ini adalah wrapper di atas ImageConverter untuk context workorder-specific
 */
final class SignatureHelper
{
    /**
     * Format response dengan signature Base64
     * 
     * @param array $data Model data atau array
     * @param string|array $signatureFields Field names yang berisi signature path
     * @param string $baseField Field name untuk menyimpan Base64 (default: {field}_base64)
     * @return array Data dengan Base64 signature ditambahkan
     */
    public static function appendBase64Signatures(array $data, $signatureFields = [], string $baseField = ''): array
    {
        if (is_string($signatureFields)) {
            $signatureFields = [$signatureFields];
        }

        if (empty($signatureFields)) {
            return $data;
        }

        foreach ($signatureFields as $field) {
            $base64Field = !empty($baseField) ? "{$baseField}_base64" : "{$field}_base64";
            $data[$base64Field] = self::getSignatureBase64($data[$field] ?? null);
        }

        return $data;
    }

    /**
     * Get signature sebagai Base64 dengan error handling
     * 
     * @param string|null $path Path ke file signature
     * @return string|null Base64 string atau null
     */
    public static function getSignatureBase64(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        try {
            return ImageConverter::toBase64($path, false);
        } catch (\Throwable $e) {
            // Silent fail - signature tetap null jika error
            \error_log("SignatureHelper error: " . $e->getMessage() . " for path: " . $path);
            return null;
        }
    }

    /**
     * Validate signature fields dalam request data
     * 
     * @param array $data Request data
     * @param array $signatureFields Field names yang harus divalidasi
     * @return array Validation errors (kosong jika valid)
     */
    public static function validateSignatureFields(array $data, array $signatureFields = []): array
    {
        $errors = [];

        foreach ($signatureFields as $field) {
            if (empty($data[$field] ?? null)) {
                $errors[$field] = "Signature field '{$field}' is required";
                continue;
            }

            $path = $data[$field];
            if (!is_string($path)) {
                $errors[$field] = "Signature field '{$field}' must be a string path";
                continue;
            }

            // Validate path doesn't contain path traversal
            if (strpos($path, '..') !== false || strpos($path, '\\\\') !== false) {
                $errors[$field] = "Invalid path: path traversal detected";
                continue;
            }
        }

        return $errors;
    }

    /**
     * Get signature info dengan error handling
     * 
     * @param string|null $path Path ke file signature
     * @return array|null File info atau null jika error
     */
    public static function getSignatureInfo(?string $path): ?array
    {
        if (empty($path)) {
            return null;
        }

        try {
            return ImageConverter::getFileInfo($path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Validate multiple workorder signatures
     * 
     * @param object $workorder Model dengan signature fields
     * @return array Validation result ['valid' => bool, 'signatures' => [...], 'errors' => [...]]
     */
    public static function validateWorkorderSignatures($workorder): array
    {
        $result = [
            'valid' => true,
            'signatures' => [],
            'errors' => []
        ];

        // Tentukan signature fields berdasarkan tipe workorder
        $signatureFields = [];
        
        if ($workorder instanceof \App\Models\WorkOrderAcService) {
            $signatureFields = ['tanda_tangan_pelanggan'];
        } elseif ($workorder instanceof \App\Models\WorkOrderPenjualan) {
            $signatureFields = ['tanda_tangan_pelanggan'];
        } elseif ($workorder instanceof \App\Models\WorkOrderPenyewaan) {
            $signatureFields = ['tanda_tangan_teknisi', 'tanda_tangan_pelanggan'];
        }

        // Validate each signature field
        foreach ($signatureFields as $field) {
            $path = $workorder->{$field} ?? null;
            
            if (empty($path)) {
                $result['errors'][$field] = "Missing signature field: {$field}";
                $result['valid'] = false;
                continue;
            }

            try {
                $base64 = ImageConverter::toBase64($path, false);
                $result['signatures'][$field] = [
                    'path' => $path,
                    'base64_preview' => substr($base64, 0, 50) . '...',
                    'valid' => true
                ];
            } catch (\Throwable $e) {
                $result['signatures'][$field] = [
                    'path' => $path,
                    'error' => $e->getMessage(),
                    'valid' => false
                ];
                $result['valid'] = false;
            }
        }

        return $result;
    }

    /**
     * Convert signature dari Base64 data ke file
     * 
     * @param string $base64Data Base64 string (with atau tanpa data URI)
     * @param string $workorderId Workorder ID untuk unique filename
     * @param string $signatureType Tipe signature (e.g., 'pelanggan', 'teknisi')
     * @return string Path file yang disimpan
     * @throws \RuntimeException
     */
    public static function saveSignatureFromBase64(
        string $base64Data,
        string $workorderId,
        string $signatureType = 'signature'
    ): string
    {
        $filename = "wo_{$workorderId}_{$signatureType}_" . date('YmdHis') . '.png';
        return ImageConverter::fromBase64($base64Data, 'uploads/signatures/workorders', $filename);
    }
}
