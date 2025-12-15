<?php

namespace App\Services;

use App\Models\Vendor;
use App\Support\JsonResponder;
use App\Utils\Upload;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface as UploadedFile;

class VendorService
{
    public function listVendors(Response $response): Response
    {
        $vendors = Vendor::all();
        return JsonResponder::success($response, $vendors, 'Daftar vendor');
    }

    public function getVendor(Response $response, string $id): Response
    {
        $vendor = Vendor::find($id);
        if (!$vendor) {
            return JsonResponder::error($response, 'Vendor tidak ditemukan', 404);
        }
        return JsonResponder::success($response, $vendor, 'Detail vendor');
    }

    public function createVendor(Response $response, array $data, ?UploadedFile $file = null): Response
    {
        $payload = Arr::only($data, ['nama', 'alamat', 'email', 'hp']);

        // Validation
        $errors = [];
        if (empty($payload['nama']) || !is_string($payload['nama'])) {
            $errors[] = 'Nama vendor wajib diisi';
        } elseif (mb_strlen($payload['nama']) > 191) {
            $errors[] = 'Nama maksimal 191 karakter';
        }
        if (!empty($payload['email'])) {
            if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email tidak valid';
            }
        }
        if (!empty($errors)) {
            return JsonResponder::badRequest($response, $errors);
        }

        $vendor = new Vendor($payload);
        $vendor->id = (string) Str::uuid();
        
        if ($file && $file->getError() === UPLOAD_ERR_OK) {
            try {
                $vendor->gambar = Upload::storeImage($file, 'vendors');
            } catch (\Exception $e) {
                return JsonResponder::error($response, 'Upload gagal: ' . $e->getMessage(), 400);
            }
        }
        
        $vendor->save();

        // Debug info: apakah file diterima dan disimpan
        $debug = [
            'file_present' => (bool) $file,
            'file_error'   => $file ? $file->getError() : null,
            'saved_path'   => $vendor->gambar ?? null,
        ];

        return JsonResponder::success($response, [
            'vendor' => $vendor,
            'debug'  => $debug,
        ], 'Vendor dibuat', 201);
    }

    public function updateVendor(Response $response, string $id, array $data, ?UploadedFile $file = null): Response
    {
        $vendor = Vendor::find($id);
        if (!$vendor) {
            return JsonResponder::error($response, 'Vendor tidak ditemukan', 404);
        }

        $payload = Arr::only($data, ['nama', 'alamat', 'email', 'hp']);

        // Validation (partial)
        $candidate = [
            'nama'  => $payload['nama']  ?? $vendor->nama,
            'email' => $payload['email'] ?? $vendor->email,
        ];
        $errors = [];
        if (empty($candidate['nama']) || !is_string($candidate['nama'])) {
            $errors[] = 'Nama vendor wajib diisi';
        } elseif (mb_strlen($candidate['nama']) > 191) {
            $errors[] = 'Nama maksimal 191 karakter';
        }
        if (!empty($candidate['email']) && !filter_var($candidate['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid';
        }
        if (!empty($errors)) {
            return JsonResponder::badRequest($response, $errors);
        }
        
        // Jika ada file baru, simpan dan hapus file lama
        if ($file && $file->getError() === UPLOAD_ERR_OK) {
            try {
                if ($vendor->gambar) {
                    Upload::deleteImage($vendor->gambar);
                }
                $payload['gambar'] = Upload::storeImage($file, 'vendors');
            } catch (\Exception $e) {
                return JsonResponder::error($response, 'Upload gagal: ' . $e->getMessage(), 400);
            }
        }

        $vendor->update($payload);

        $debug = [
            'file_present' => (bool) $file,
            'file_error'   => $file ? $file->getError() : null,
            'saved_path'   => $vendor->gambar ?? null,
        ];

        return JsonResponder::success($response, [
            'vendor' => $vendor,
            'debug'  => $debug,
        ], 'Vendor diperbarui');
    }

    public function deleteVendor(Response $response, string $id): Response
    {
        $vendor = Vendor::find($id);
        if (!$vendor) {
            return JsonResponder::error($response, 'Vendor tidak ditemukan', 404);
        }

        // Delete file gambar jika ada
        if ($vendor->gambar) {
            try {
                Upload::deleteImage($vendor->gambar);
            } catch (\Exception $e) {
                // Log error tapi jangan hentikan proses delete vendor
                // (file mungkin sudah dihapus manual)
            }
        }

        $vendor->delete();
        return JsonResponder::success($response, null, 'Vendor dihapus');
    }
}
