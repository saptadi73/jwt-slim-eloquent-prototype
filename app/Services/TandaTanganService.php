<?php

namespace App\Services;

use App\Models\TandaTangan;
use Psr\Http\Message\UploadedFileInterface;
use Illuminate\Support\Str;

class TandaTanganService
{
    public function getAll($page = 1, $limit = 10)
    {
        return TandaTangan::paginate($limit, ['*'], 'page', $page);
    }

    public function getById($id)
    {
        return TandaTangan::findOrFail($id);
    }

    public function getByPegawaiId($pegawaiId, $page = 1, $limit = 10)
    {
        return TandaTangan::where('pegawai_id', $pegawaiId)
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function store($data, ?UploadedFileInterface $file = null)
    {
        $urlTandaTangan = null;

        if ($file !== null) {
            $urlTandaTangan = $this->handleSignatureUpload($file);
        }

        return TandaTangan::create([
            'pegawai_id' => $data['pegawai_id'],
            'url_tanda_tangan' => $urlTandaTangan,
            'deskripsi' => $data['deskripsi'] ?? null,
        ]);
    }

    public function update($id, $data, ?UploadedFileInterface $file = null)
    {
        $tandaTangan = TandaTangan::findOrFail($id);

        $urlTandaTangan = $tandaTangan->url_tanda_tangan;

        if ($file !== null) {
            // Delete old signature if exists
            if ($tandaTangan->url_tanda_tangan) {
                $this->deleteSignature($tandaTangan->url_tanda_tangan);
            }
            $urlTandaTangan = $this->handleSignatureUpload($file);
        }

        $tandaTangan->update([
            'pegawai_id' => $data['pegawai_id'] ?? $tandaTangan->pegawai_id,
            'url_tanda_tangan' => $urlTandaTangan,
            'deskripsi' => $data['deskripsi'] ?? $tandaTangan->deskripsi,
        ]);

        return $tandaTangan;
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
