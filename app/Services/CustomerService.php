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
    /** Field milik tabel customers */
    private array $customerFields = ['nama','alamat','hp','gambar'];

    /** Field milik tabel customer_assets */
    private array $assetFields = ['tipe','keterangan','lokasi','brand','model','freon','kapasitas'];

    /**
     * Endpoint utama pembuatan customer (multipart):
     * - validasi
     * - upload (opsional)
     * - simpan customer + assets (ATOMIK)
     */
    public function createCustomerMultipart(array $data, Response $response, ?UploadedFileInterface $file = null): Response
    {
        try {
            // 1) Validasi field dasar
            [$customerData, $assetData] = $this->validateAndSplit($data);

            // 2) Upload foto (opsional)
            if ($file instanceof UploadedFileInterface && $file->getError() === UPLOAD_ERR_OK) {
                // Simpan path relatif, misal: "customers/2025/08/xyz.jpg"
                $customerData['gambar'] = Upload::storeImage($file, 'customers');
            }

            // 3) Simpan transaksional (customer + asset)
            [$customer, $customerAsset] = DB::transaction(function () use ($customerData, $assetData) {
                /** @var Customer $customer */
                $customer = Customer::create($customerData);

                // Set FK dan buat asset
                $assetData['customer_id'] = $customer->id;
                /** @var CustomerAsset $asset */
                $asset = CustomerAsset::create($assetData);

                return [$customer, $asset];
            });

            // Muat relasi setelah keduanya dibuat
            $customer->load('assets');

            return JsonResponder::success(
                $response,
                [
                    'customer' => $customer,
                    'asset'    => $customerAsset,
                ],
                'Customer created',
                201
            );

        } catch (InvalidArgumentException $e) {
            return JsonResponder::error($response, $e->getMessage(), 422);
        } catch (\Throwable $e) {
            // TODO: log $e untuk debugging internal
            return JsonResponder::error($response, 'Internal server error', 500);
        }
    }

    /**
     * Validasi & bagi payload menjadi 2 bagian: customers & customer_assets.
     * @return array{0: array, 1: array}
     * @throws InvalidArgumentException
     */
    private function validateAndSplit(array $data): array
    {
        // Ambil sesuai domain tabelnya
        $customer = Arr::only($data, $this->customerFields);
        $asset    = Arr::only($data, $this->assetFields);

        // Validasi minimum untuk tabel customers
        foreach (['nama','alamat','hp'] as $field) {
            if (empty($customer[$field]) || !is_string($customer[$field])) {
                throw new InvalidArgumentException("Field '{$field}' wajib diisi");
            }
        }

        // Validasi HP sederhana
        if (!preg_match('/^[0-9+\s\\-()]{6,20}$/', $customer['hp'])) {
            throw new InvalidArgumentException("Format 'hp' tidak valid");
        }

        // (Opsional) Validasi minimal untuk assets
        // Misal tipe/lokasi wajib jika ingin memaksa perekaman asset
        // if (empty($asset['tipe'])) { throw new InvalidArgumentException(\"Field 'tipe' wajib diisi\"); }

        return [$customer, $asset];
    }
}
