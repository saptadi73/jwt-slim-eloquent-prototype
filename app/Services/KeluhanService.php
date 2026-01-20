<?php
namespace App\Services;

use App\Models\Keluhan;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Support\Str;

class KeluhanService
{
    public function createKeluhan(Response $response, array $data)
    {
        try {
            // Validasi input
            $errors = [];
            if (empty($data['nama']) || !is_string($data['nama'])) {
                $errors[] = 'Nama keluhan wajib diisi';
            } elseif (mb_strlen($data['nama']) > 191) {
                $errors[] = 'Nama maksimal 191 karakter';
            }
            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            $keluhan = new Keluhan($data);
            $keluhan->id = $keluhan->id ?? (string) Str::uuid();
            $keluhan->save();

            return JsonResponder::success($response, $keluhan, 'Keluhan dibuat', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function listKeluhan(Response $response)
    {
        try {
            $keluhan = Keluhan::orderBy('created_at', 'desc')->get();
            return JsonResponder::success($response, $keluhan, 'Data keluhan');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function getKeluhan(Response $response, string $id)
    {
        try {
            $keluhan = Keluhan::find($id);
            if (!$keluhan) {
                return JsonResponder::error($response, 'Keluhan tidak ditemukan', 404);
            }
            return JsonResponder::success($response, $keluhan, 'Detail keluhan');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function updateKeluhan(Response $response, string $id, array $data)
    {
        try {
            $keluhan = Keluhan::find($id);
            if (!$keluhan) {
                return JsonResponder::error($response, 'Keluhan tidak ditemukan', 404);
            }

            // Validasi partial update
            $candidate = [
                'nama' => $data['nama'] ?? $keluhan->nama,
            ];
            $errors = [];
            if (empty($candidate['nama']) || !is_string($candidate['nama'])) {
                $errors[] = 'Nama keluhan wajib diisi';
            } elseif (mb_strlen($candidate['nama']) > 191) {
                $errors[] = 'Nama maksimal 191 karakter';
            }
            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            $keluhan->update($candidate);
            return JsonResponder::success($response, $keluhan->fresh(), 'Keluhan diperbarui');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function deleteKeluhan(Response $response, string $id)
    {
        try {
            $keluhan = Keluhan::find($id);
            if (!$keluhan) {
                return JsonResponder::error($response, 'Keluhan tidak ditemukan', 404);
            }

            $keluhan->delete();
            return JsonResponder::success($response, null, 'Keluhan dihapus');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }
}
