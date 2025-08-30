<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAsset;
use App\Utils\Upload;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;
use App\Support\JsonResponder;
use Slim\Psr7\Response;

final class CustomerService
{
    // photo/gambar customer diset oleh upload helper
    private array $customerFields = ['nama','alamat','hp']; 
    // image_path asset bila ada mekanisme upload terpisah
    private array $assetFields    = ['tipe','keterangan','lokasi','brand','model','freon','kapasitas']; 

    /**
     * Endpoint utama pembuatan customer (multipart): validasi, upload (opsional), simpan customer + assets.
     */
    public function createCustomerMultipart(array $data, Response $response, ?UploadedFileInterface $file = null): Response
    {
        try {
            // 1) Validasi input dasar
            $validated = $this->validateCustomerData($data);

            // 2) Upload foto customer (opsional)
            if ($file instanceof UploadedFileInterface && $file->getError() === UPLOAD_ERR_OK) {
                $validated['gambar'] = Upload::storeImage($file, 'customers'); // simpan path relatif
            }

            // 3) Simpan transaksional
            $customer = DB::transaction(function () use ($validated, $data) {
                /** @var Customer $customer */
                $customer = Customer::create($validated);

                // Assets (opsional)
                if (!empty($data['customer_asset']) && is_array($data['customer_asset'])) {
                    foreach ($data['customer_asset'] as $idx => $asset) {
                        $assetPayload = $this->validateAssetData($asset, $idx);
                        $customer->assets()->create($assetPayload);
                    }
                }

                return $customer;
            });

            $customer->load('assets');

            return JsonResponder::success($response, $customer, 'Customer created', 201);

        } catch (InvalidArgumentException $e) {
            return JsonResponder::error($response, $e->getMessage(), 422);
        } catch (\Throwable $e) {
            // Anda bisa log detail $e untuk debugging internal
            return JsonResponder::error($response, 'Internal server error', 500);
        }
    }

    /**
     * Validasi data customer, return payload siap simpan (tanpa gambar).
     * @throws InvalidArgumentException
     */
    private function validateCustomerData(array $data): array
    {
        $payload = Arr::only($data, $this->customerFields);

        foreach (['nama','alamat','hp'] as $field) {
            if (empty($payload[$field]) || !is_string($payload[$field])) {
                throw new InvalidArgumentException("Field '{$field}' wajib diisi");
            }
        }

        // (Opsional) tambahan validasi HP sederhana
        if (!preg_match('/^[0-9+\s\-()]{6,20}$/', $payload['hp'])) {
            throw new InvalidArgumentException("Format 'hp' tidak valid");
        }

        return $payload;
    }

    /**
     * Validasi satu baris asset, return payload siap simpan.
     * @throws InvalidArgumentException
     */
    private function validateAssetData(array $asset, int $index = 0): array
    {
        $payload = Arr::only($asset, $this->assetFields);
        foreach ($this->assetFields as $f) {
            if (!isset($payload[$f]) || $payload[$f] === '') {
                throw new InvalidArgumentException("Asset[{$index}] field '{$f}' wajib diisi");
            }
        }
        return $payload;
    }
}
