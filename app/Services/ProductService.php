<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Support\JsonResponder;
use App\Utils\Upload;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use App\Support\RequestHelper;

class ProductService
{
    public function createProduct(Response $response, array $data, ?UploadedFileInterface $file = null)
    {
        try {
            // Validasi input
            if (empty($data['nama'])) {
                return JsonResponder::error($response, 'Nama produk wajib diisi', 422);
            }

            // Kompatibilitas kolom: map 'type' -> 'tipe' (DB menggunakan 'tipe')
            if (isset($data['type']) && !isset($data['tipe'])) {
                $data['tipe'] = $data['type'];
                unset($data['type']);
            }

            // Handle file upload
            if ($file && $file->getError() === UPLOAD_ERR_OK) {
                try {
                    $data['gambar'] = Upload::storeImage($file, 'products');
                } catch (\Exception $e) {
                    return JsonResponder::error($response, 'Upload gagal: ' . $e->getMessage(), 400);
                }
            }

            $product = new Product($data);
            // Set UUID secara eksplisit agar tidak tergantung fillable
            $product->id = $product->id ?? (string) Str::uuid();
            $product->save();

            // Debug info
            $debug = [
                'file_present' => (bool) $file,
                'file_error'   => $file ? $file->getError() : null,
                'saved_path'   => $product->gambar ?? null,
            ];

            return JsonResponder::success($response, [
                'product' => $product,
                'debug'   => $debug,
            ], 'Produk dibuat', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function listProducts(Response $response)
    {
        try {
            $products = Product::with(['kategori', 'satuan', 'brand'])->get();
            return JsonResponder::success($response, $products, 'Data produk');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function getProduct(Response $response, string $id)
    {
        try {
            $product = Product::with(['kategori', 'satuan', 'brand'])->find($id);
            if (!$product) {
                return JsonResponder::error($response, 'Produk tidak ditemukan', 404);
            }
            return JsonResponder::success($response, $product, 'Detail produk');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function updateProduct(Response $response, string $id, array $data, ?UploadedFileInterface $file = null)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return JsonResponder::error($response, 'Produk tidak ditemukan', 404);
            }

            // Kompatibilitas kolom: map 'type' -> 'tipe' (DB menggunakan 'tipe')
            if (isset($data['type']) && !isset($data['tipe'])) {
                $data['tipe'] = $data['type'];
                unset($data['type']);
            }

            // Handle file upload
            if ($file && $file->getError() === UPLOAD_ERR_OK) {
                try {
                    if ($product->gambar) {
                        Upload::deleteImage($product->gambar);
                    }
                    $data['gambar'] = Upload::storeImage($file, 'products');
                } catch (\Exception $e) {
                    return JsonResponder::error($response, 'Upload gagal: ' . $e->getMessage(), 400);
                }
            }

            $product->update($data);

            // Debug info
            $debug = [
                'file_present' => (bool) $file,
                'file_error'   => $file ? $file->getError() : null,
                'saved_path'   => $product->gambar ?? null,
            ];

            return JsonResponder::success($response, [
                'product' => $product,
                'debug'   => $debug,
            ], 'Produk diperbarui');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    public function deleteProduct(Response $response, string $id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return JsonResponder::error($response, 'Produk tidak ditemukan', 404);
            }

            // Delete file gambar jika ada
            if ($product->gambar) {
                try {
                    Upload::deleteImage($product->gambar);
                } catch (\Exception $e) {
                    // Log error tapi jangan hentikan proses delete
                }
            }

            $product->delete();
            return JsonResponder::success($response, null, 'Produk dihapus');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }
}
