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
        $uploadDir = 'public/uploads/signatures';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = 'sig_' . uniqid() . '_' . $file->getClientFilename();
        $filepath = $uploadDir . '/' . $filename;

        $stream = $file->getStream();
        file_put_contents($filepath, $stream);

        return '/uploads/signatures/' . $filename;
    }

    public function deleteSignature(string $url)
    {
        $filepath = 'public' . $url;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
