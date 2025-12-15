<?php
namespace App\Services;

use App\Models\Satuan;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Support\Str;

class SatuanService
{
    public function createSatuan(Response $response, array $data)
    {
        try {
            // Validasi input
            if (empty($data['nama'])) {
                return JsonResponder::error($response, 'Nama satuan wajib diisi', 422);
            }

            $satuan = new Satuan($data);
            $satuan->id = $satuan->id ?? (string) Str::uuid();
            $satuan->save();

            return JsonResponder::success($response, $satuan, 'Satuan dibuat', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function listSatuans(Response $response)
    {
        try {
            $satuans = Satuan::with('products')->get();
            return JsonResponder::success($response, $satuans, 'Data satuan');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function getSatuan(Response $response, string $id)
    {
        try {
            $satuan = Satuan::with('products')->find($id);
            if (!$satuan) {
                return JsonResponder::error($response, 'Satuan tidak ditemukan', 404);
            }
            return JsonResponder::success($response, $satuan, 'Detail satuan');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function updateSatuan(Response $response, string $id, array $data)
    {
        try {
            $satuan = Satuan::find($id);
            if (!$satuan) {
                return JsonResponder::error($response, 'Satuan tidak ditemukan', 404);
            }

            $satuan->update($data);

            return JsonResponder::success($response, $satuan, 'Satuan diperbarui');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function deleteSatuan(Response $response, string $id)
    {
        try {
            $satuan = Satuan::find($id);
            if (!$satuan) {
                return JsonResponder::error($response, 'Satuan tidak ditemukan', 404);
            }

            $satuan->delete();
            return JsonResponder::success($response, null, 'Satuan dihapus');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }
}
