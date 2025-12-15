<?php
namespace App\Services;

use App\Models\Brand;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Support\Str;

class BrandService
{
    public function createBrand(Response $response, array $data)
    {
        try {
            // Validasi input
            $errors = [];
            if (empty($data['nama']) || !is_string($data['nama'])) {
                $errors[] = 'Nama brand wajib diisi';
            } elseif (mb_strlen($data['nama']) > 191) {
                $errors[] = 'Nama maksimal 191 karakter';
            }
            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            $brand = new Brand($data);
            // Set UUID secara eksplisit agar tidak tergantung fillable
            $brand->id = $brand->id ?? (string) Str::uuid();
            $brand->save();

            return JsonResponder::success($response, $brand, 'Brand dibuat', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function listBrands(Response $response)
    {
        try {
            $brands = Brand::with('products')->get();
            return JsonResponder::success($response, $brands, 'Data brand');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function getBrand(Response $response, string $id)
    {
        try {
            $brand = Brand::with('products')->find($id);
            if (!$brand) {
                return JsonResponder::error($response, 'Brand tidak ditemukan', 404);
            }
            return JsonResponder::success($response, $brand, 'Detail brand');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function updateBrand(Response $response, string $id, array $data)
    {
        try {
            $brand = Brand::find($id);
            if (!$brand) {
                return JsonResponder::error($response, 'Brand tidak ditemukan', 404);
            }

            // Validasi partial update
            $candidate = [
                'nama' => $data['nama'] ?? $brand->nama,
            ];
            $errors = [];
            if (empty($candidate['nama']) || !is_string($candidate['nama'])) {
                $errors[] = 'Nama brand wajib diisi';
            } elseif (mb_strlen($candidate['nama']) > 191) {
                $errors[] = 'Nama maksimal 191 karakter';
            }
            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            $brand->update($data);

            return JsonResponder::success($response, $brand, 'Brand diperbarui');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function deleteBrand(Response $response, string $id)
    {
        try {
            $brand = Brand::find($id);
            if (!$brand) {
                return JsonResponder::error($response, 'Brand tidak ditemukan', 404);
            }

            $brand->delete();
            return JsonResponder::success($response, null, 'Brand dihapus');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }
}
