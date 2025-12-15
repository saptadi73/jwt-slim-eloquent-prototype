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
            if (empty($data['nama'])) {
                return JsonResponder::error($response, 'Nama kategori wajib diisi', 422);
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
