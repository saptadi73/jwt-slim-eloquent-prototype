<?php

namespace App\Services;

use App\Models\TandaTangan;
use Psr\Http\Message\UploadedFileInterface;

class TandaTanganService
{
    public function getAll($page = 1, $limit = 10)
    {
        return TandaTangan::with('pegawai:id,nama,departemen_id,position_id')
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function getById($id)
    {
        return TandaTangan::with('pegawai:id,nama,departemen_id,position_id')
            ->findOrFail($id);
    }

    public function store(UploadedFileInterface $file)
    {
        $urlTandaTangan = $this->handleSignatureUpload($file);

        return TandaTangan::create([
            'url_tanda_tangan' => $urlTandaTangan,
        ]);
    }

    public function update($id, ?UploadedFileInterface $file = null)
    {
        $tandaTangan = TandaTangan::findOrFail($id);

        if ($file !== null) {
            // Delete old signature if exists
            if ($tandaTangan->url_tanda_tangan) {
                $this->deleteSignature($tandaTangan->url_tanda_tangan);
            }
            
            $urlTandaTangan = $this->handleSignatureUpload($file);
            $tandaTangan->update([
                'url_tanda_tangan' => $urlTandaTangan,
            ]);
        }

        return $tandaTangan->fresh();
    }

    public function delete($id)
    {
        $tandaTangan = TandaTangan::findOrFail($id);

        if ($tandaTangan->url_tanda_tangan) {
            $this->deleteSignature($tandaTangan->url_tanda_tangan);
        }

        $tandaTangan->delete();
        return $tandaTangan;
    }

    public function handleSignatureUpload(UploadedFileInterface $file): string
    {
        // Use absolute path like Upload utility
        $publicRoot = rtrim($_ENV['PUBLIC_PATH'] ?? (dirname(__DIR__, 2) . '/public'), '/\\');
        $uploadDir = $publicRoot . '/uploads/signatures';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $filename = 'sig_' . bin2hex(random_bytes(8)) . '_' . date('Ymd_His') . '.' . $ext;
        $filepath = $uploadDir . '/' . $filename;

        try {
            // Use moveTo directly (same as Upload utility)
            $file->moveTo($filepath);
            
            // Verify file was written
            if (!is_file($filepath)) {
                throw new \Exception('Failed to move uploaded file');
            }
            
            error_log('Signature uploaded successfully: ' . $filepath . ' (size: ' . filesize($filepath) . ')');
        } catch (\Exception $e) {
            error_log('Signature upload error: ' . $e->getMessage());
            throw new \Exception('Failed to upload signature: ' . $e->getMessage());
        }

        return '/uploads/signatures/' . $filename;
    }

    public function deleteSignature(string $url)
    {
        $publicRoot = rtrim($_ENV['PUBLIC_PATH'] ?? (dirname(__DIR__, 2) . '/public'), '/\\');
        $filepath = $publicRoot . '/' . ltrim($url, '/\\');
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
