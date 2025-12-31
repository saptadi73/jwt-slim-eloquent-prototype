<?php

namespace App\Services;

use App\Models\Pegawai;
use Psr\Http\Message\UploadedFileInterface;

class PegawaiService
{
    public function getAll($page = 1, $limit = 10, $filters = [])
    {
        $query = Pegawai::with(['departemen', 'group', 'position', 'tandaTangan']);

        // Filter by department
        if (!empty($filters['department_id'])) {
            $query->where('departemen_id', $filters['department_id']);
        }

        // Filter by group
        if (!empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        // Filter by position
        if (!empty($filters['position_id'])) {
            $query->where('position_id', $filters['position_id']);
        }

        // Search by name
        if (!empty($filters['search'])) {
            $query->where('nama', 'ILIKE', '%' . $filters['search'] . '%');
        }

        // Filter by active status
        if (isset($filters['is_active'])) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    public function getById($id)
    {
        return Pegawai::with(['departemen', 'group', 'position', 'tandaTangan'])->findOrFail($id);
    }

    public function store($data, ?UploadedFileInterface $fotoFile = null, ?UploadedFileInterface $tandaTanganFile = null)
    {
        $urlFoto = null;
        $urlTandaTangan = null;

        if ($fotoFile !== null) {
            $urlFoto = $this->handlePhotoUpload($fotoFile);
        }

        // Legacy support: still accept tanda_tangan file upload
        if ($tandaTanganFile !== null) {
            $urlTandaTangan = $this->handleSignatureUpload($tandaTanganFile);
        }

        return Pegawai::create([
            'nama' => $data['nama'],
            'alamat' => $data['alamat'] ?? null,
            'hp' => $data['hp'] ?? null,
            'email' => $data['email'] ?? null,
            'departemen_id' => $data['departemen_id'] ?? null,
            'group_id' => $data['group_id'] ?? null,
            'position_id' => $data['position_id'] ?? null,
            'tanda_tangan_id' => $data['tanda_tangan_id'] ?? null,
            'url_foto' => $urlFoto,
            'tanda_tangan' => $urlTandaTangan, // Legacy column
            'hire_date' => $data['hire_date'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update($id, $data, ?UploadedFileInterface $fotoFile = null, ?UploadedFileInterface $tandaTanganFile = null)
    {
        $pegawai = Pegawai::findOrFail($id);

        $urlFoto = $pegawai->url_foto;
        $urlTandaTangan = $pegawai->tanda_tangan;

        if ($fotoFile !== null) {
            if ($pegawai->url_foto) {
                $this->deletePhoto($pegawai->url_foto);
            }
            $urlFoto = $this->handlePhotoUpload($fotoFile);
        }

        // Legacy support: still accept tanda_tangan file upload
        if ($tandaTanganFile !== null) {
            if ($pegawai->tanda_tangan) {
                $this->deleteSignature($pegawai->tanda_tangan);
            }
            $urlTandaTangan = $this->handleSignatureUpload($tandaTanganFile);
        }

        $pegawai->update([
            'nama' => $data['nama'] ?? $pegawai->nama,
            'alamat' => $data['alamat'] ?? $pegawai->alamat,
            'hp' => $data['hp'] ?? $pegawai->hp,
            'email' => $data['email'] ?? $pegawai->email,
            'departemen_id' => $data['departemen_id'] ?? $pegawai->departemen_id,
            'group_id' => $data['group_id'] ?? $pegawai->group_id,
            'position_id' => $data['position_id'] ?? $pegawai->position_id,
            'tanda_tangan_id' => $data['tanda_tangan_id'] ?? $pegawai->tanda_tangan_id,
            'url_foto' => $urlFoto,
            'tanda_tangan' => $urlTandaTangan, // Legacy column
            'hire_date' => $data['hire_date'] ?? $pegawai->hire_date,
            'is_active' => $data['is_active'] ?? $pegawai->is_active,
        ]);

        return $pegawai;
    }

    public function delete($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        if ($pegawai->url_foto) {
            $this->deletePhoto($pegawai->url_foto);
        }

        if ($pegawai->tanda_tangan) {
            $this->deleteSignature($pegawai->tanda_tangan);
        }

        $pegawai->delete();
        return $pegawai;
    }

    public function handlePhotoUpload(UploadedFileInterface $file): string
    {
        $uploadDir = 'public/uploads/pegawai';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = 'emp_' . uniqid() . '_' . $file->getClientFilename();
        $filepath = $uploadDir . '/' . $filename;

        $stream = $file->getStream();
        file_put_contents($filepath, $stream);

        return '/uploads/pegawai/' . $filename;
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

    public function deletePhoto(string $url)
    {
        $filepath = 'public' . $url;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    public function deleteSignature(string $url)
    {
        $filepath = 'public' . $url;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
