<?php
namespace App\Services;

use App\Models\Kategori;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Support\Str;

class KategoriService
{
    public function createKategori(Response $response, array $data)
    {
        try {
            // Validasi input
            $errors = [];
            if (empty($data['nama']) || !is_string($data['nama'])) {
                $errors[] = 'Nama kategori wajib diisi';
            } elseif (mb_strlen($data['nama']) > 191) {
                $errors[] = 'Nama maksimal 191 karakter';
            }
            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            $kategori = new Kategori($data);
            $kategori->id = $kategori->id ?? (string) Str::uuid();
            $kategori->save();

            return JsonResponder::success($response, $kategori, 'Kategori dibuat', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function listKategoris(Response $response)
    {
        try {
            $kategoris = Kategori::with('products')->get();
            return JsonResponder::success($response, $kategoris, 'Data kategori');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function getKategori(Response $response, string $id)
    {
        try {
            $kategori = Kategori::with('products')->find($id);
            if (!$kategori) {
                return JsonResponder::error($response, 'Kategori tidak ditemukan', 404);
            }
            return JsonResponder::success($response, $kategori, 'Detail kategori');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function updateKategori(Response $response, string $id, array $data)
    {
        try {
            $kategori = Kategori::find($id);
            if (!$kategori) {
                return JsonResponder::error($response, 'Kategori tidak ditemukan', 404);
            }

            // Validasi partial update
            $candidate = [
                'nama' => $data['nama'] ?? $kategori->nama,
            ];
            $errors = [];
            if (empty($candidate['nama']) || !is_string($candidate['nama'])) {
                $errors[] = 'Nama kategori wajib diisi';
            } elseif (mb_strlen($candidate['nama']) > 191) {
                $errors[] = 'Nama maksimal 191 karakter';
            }
            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            $kategori->update($data);

            return JsonResponder::success($response, $kategori, 'Kategori diperbarui');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function deleteKategori(Response $response, string $id)
    {
        try {
            $kategori = Kategori::find($id);
            if (!$kategori) {
                return JsonResponder::error($response, 'Kategori tidak ditemukan', 404);
            }

            $kategori->delete();
            return JsonResponder::success($response, null, 'Kategori dihapus');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }
}
