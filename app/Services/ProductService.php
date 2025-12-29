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
use App\Models\Brand;
use App\Models\Kategori as KategoriModel;
use App\Models\Satuan as SatuanModel;
use InvalidArgumentException;

class ProductService
{
    private function isValidUuid($value): bool
    {
        if (!is_string($value)) return false;
        return preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/', $value) === 1;
    }
    public function createProduct(Response $response, array $data, ?UploadedFileInterface $file = null)
    {
        try {
            // Validasi input
            $errors = [];
            if (empty($data['nama']) || !is_string($data['nama'])) {
                $errors[] = 'Nama produk wajib diisi';
            } elseif (mb_strlen($data['nama']) > 191) {
                $errors[] = 'Nama maksimal 191 karakter';
            }
            
            // Normalize decimal fields: convert empty string to null
            if (isset($data['harga']) && $data['harga'] === '') {
                $data['harga'] = null;
            }
            if (isset($data['hpp']) && $data['hpp'] === '') {
                $data['hpp'] = null;
            }
            
            if (isset($data['harga']) && $data['harga'] !== null) {
                if (!is_numeric($data['harga'])) {
                    $errors[] = 'Harga harus numerik';
                } elseif ((float)$data['harga'] < 0) {
                    $errors[] = 'Harga tidak boleh negatif';
                }
            }
            if (isset($data['hpp']) && $data['hpp'] !== null) {
                if (!is_numeric($data['hpp'])) {
                    $errors[] = 'HPP harus numerik';
                } elseif ((float)$data['hpp'] < 0) {
                    $errors[] = 'HPP tidak boleh negatif';
                }
            }
            // Optional FK validations if present
            foreach (['brand_id' => Brand::class, 'kategori_id' => KategoriModel::class, 'satuan_id' => SatuanModel::class] as $key => $modelClass) {
                if (isset($data[$key])) {
                    if (!$this->isValidUuid($data[$key])) {
                        $errors[] = "$key harus UUID valid";
                    } else {
                        if (!$modelClass::find($data[$key])) {
                            $errors[] = "$key tidak ditemukan";
                        }
                    }
                }
            }

            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            // Kompatibilitas kolom: map 'type' -> 'tipe' (DB menggunakan 'tipe')
            if (isset($data['type']) && !isset($data['tipe'])) {
                $data['tipe'] = $data['type'];
                unset($data['type']);
            }

            // Remove empty object/array for gambar if present
            if (isset($data['gambar']) && (is_array($data['gambar']) || is_object($data['gambar']))) {
                unset($data['gambar']);
            }

            // Validasi dan set default untuk stok (integer field)
            if (!isset($data['stok']) || $data['stok'] === '' || $data['stok'] === null) {
                $data['stok'] = 0;
            } elseif (!is_numeric($data['stok'])) {
                return JsonResponder::badRequest($response, ['Stok harus berupa angka']);
            } else {
                $data['stok'] = (int) $data['stok'];
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
            return JsonResponder::error($response, $th, 500);
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

            // Validasi partial update
            $candidate = [
                'nama'  => $data['nama']  ?? $product->nama,
                'harga' => $data['harga'] ?? $product->harga,
                'hpp'   => $data['hpp'] ?? $product->hpp,
            ];
            
            // Normalize decimal fields: convert empty string to null
            if (isset($data['harga']) && $data['harga'] === '') {
                $data['harga'] = null;
                $candidate['harga'] = null;
            }
            if (isset($data['hpp']) && $data['hpp'] === '') {
                $data['hpp'] = null;
                $candidate['hpp'] = null;
            }
            
            $errors = [];
            if (empty($candidate['nama']) || !is_string($candidate['nama'])) {
                $errors[] = 'Nama produk wajib diisi';
            } elseif (mb_strlen($candidate['nama']) > 191) {
                $errors[] = 'Nama maksimal 191 karakter';
            }
            if ($candidate['harga'] !== null && !is_numeric($candidate['harga'])) {
                $errors[] = 'Harga harus numerik';
            } elseif ($candidate['harga'] !== null && (float)$candidate['harga'] < 0) {
                $errors[] = 'Harga tidak boleh negatif';
            }
            if (isset($candidate['hpp']) && $candidate['hpp'] !== null) {
                if (!is_numeric($candidate['hpp'])) {
                    $errors[] = 'HPP harus numerik';
                } elseif ((float)$candidate['hpp'] < 0) {
                    $errors[] = 'HPP tidak boleh negatif';
                }
            }

            // Optional FK validations if present
            foreach (['brand_id' => Brand::class, 'kategori_id' => KategoriModel::class, 'satuan_id' => SatuanModel::class] as $key => $modelClass) {
                if (isset($data[$key])) {
                    if (!$this->isValidUuid($data[$key])) {
                        $errors[] = "$key harus UUID valid";
                    } else {
                        if (!$modelClass::find($data[$key])) {
                            $errors[] = "$key tidak ditemukan";
                        }
                    }
                }
            }
            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            // Kompatibilitas kolom: map 'type' -> 'tipe' (DB menggunakan 'tipe')
            if (isset($data['type']) && !isset($data['tipe'])) {
                $data['tipe'] = $data['type'];
                unset($data['type']);
            }

            // Remove empty object/array for gambar if present
            if (isset($data['gambar']) && (is_array($data['gambar']) || is_object($data['gambar']))) {
                unset($data['gambar']);
            }

            // Validasi dan set default untuk stok (integer field)
            if (isset($data['stok'])) {
                if ($data['stok'] === '' || $data['stok'] === null) {
                    $data['stok'] = 0;
                } elseif (!is_numeric($data['stok'])) {
                    return JsonResponder::badRequest($response, ['Stok harus berupa angka']);
                } else {
                    $data['stok'] = (int) $data['stok'];
                }
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
            return JsonResponder::error($response, $th, 500);
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
