<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAsset;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Support\RequestHelper;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\UploadedFileInterface as File;
use App\Utils\Upload;
use App\Models\Brand;
use App\Models\Tipe;

class CustomerService
{
    public function createCustomerAndAsset(Request $request, Response $response, array $data, File $file)
    {
        $data['kode_pelanggan'] = 'CUST-' . str_pad((string)(Customer::max('id') + 1), 5, '0', STR_PAD_LEFT);
        $customer_data = Arr::only($data, ['nama', 'alamat', 'hp', 'kode_pelanggan']);
        $asset_data = Arr::only($data, ['tipe_id', 'keterangan', 'lokasi', 'brand_id', 'model', 'freon', 'kapasitas']);

        try {
            $customer_data['id'] = Str::uuid();
            $asset_data['id'] = Str::uuid();
            $customer = Customer::create($customer_data);
            $asset_data['customer_id'] = $customer_data['id'];
            CustomerAsset::create($asset_data);
            if ($file && $file->getError() === UPLOAD_ERR_OK) {
                $filename = Upload::storeImage($file, 'customers');
                $customer->gambar = $filename;
                $customer->save();
            } else {
                $customer->gambar = null;
                $customer->save();
                $msg_file = $file ? 'File upload error code: ' . $file->getError() : 'No file uploaded';
            }
            return JsonResponder::success($response, $customer, 'Customer and Asset created' . ($msg_file ?? ''));
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create Customer and Asset: ' . $th->getMessage(), 500);
        }
    }

    public function createCustomerAsset(array $data): CustomerAsset
    {
        return CustomerAsset::create($data);
    }

    public function updateCustomerAndAsset(Request $request, Response $response, Customer $customer, array $data, ?File $file)
    {
        $customer_data = Arr::only($data, ['nama', 'alamat', 'hp']);
        $asset_data = Arr::only($data, ['tipe_id', 'keterangan', 'lokasi', 'brand_id', 'model', 'freon', 'kapasitas']);

        try {
            $customer->update($customer_data);
            if ($customer->asset) {
                $customer->asset->update($asset_data);
            } else {
                $asset_data['id'] = Str::uuid();
                $asset_data['customer_id'] = $customer->id;
                CustomerAsset::create($asset_data);
            }
            if ($file && $file->getError() === UPLOAD_ERR_OK) {
                // Delete old image if exists
                if ($customer->gambar) {
                    Upload::deleteImage($customer->gambar, 'customers');
                }
                $filename = Upload::storeImage($file, 'customers');
                $customer->gambar = $filename;
                $customer->save();
            } elseif ($file) {
                $msg_file = 'File upload error code: ' . $file->getError();
            }
            return JsonResponder::success($response, $customer, 'Customer and Asset updated' . ($msg_file ?? ''));
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to update Customer and Asset: ' . $th->getMessage(), 500);
        }
    }

    public function deleteCustomerAndAsset(Request $request, Response $response, Customer $customer)
    {
        try {
            // Hapus gambar jika ada
            if ($customer->gambar) {
                Upload::deleteImage($customer->gambar);
            }
            // Hapus asset terkait
            if ($customer->asset) {
                $customer->asset->delete();
            }
            // Hapus customer
            $customer->delete();
            return JsonResponder::success($response, null, 'Customer and Asset deleted');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete Customer and Asset: ' . $th->getMessage(), 500);
        }
    }

    public function getCustomerWithAsset(Response $response, string $customerId)
    {

        try {
            print_r($customerId); // Debug: pastikan ID diterima dengan benar
            $data = Customer::with('customerassets')->find($customerId);

            return JsonResponder::success($response, $data, 'Customer with Asset retrieved');
        } catch (\Throwable $th) {
            //throw $th;
            return JsonResponder::error($response, 'Failed to retrieve Customer with Asset: ' . $th->getMessage(), 500);
        }
    }

    public function createTipe(Response $response, Request $request, array $data)
    {
        try {
            $data['id'] = Str::uuid();
            $tipe = Tipe::create($data);
            return JsonResponder::success($response, $tipe, 'Tipe created');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create Tipe: ' . $th->getMessage(), 500);
        }
    }

    public function createBrand(Response $response, Request $request, array $data)
    {
        try {
            $data['id'] = Str::uuid();
            $brand = Brand::create($data);
            return JsonResponder::success($response, $brand, 'Brand created');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create Brand: ' . $th->getMessage(), 500);
        }
    }

    public function listTipe(Response $response)
    {
        $data = Tipe::all();
        return JsonResponder::success($response, $data, 'List of Tipe retrieved');
    }

    public function listBrand(Response $response)
    {
        $data = Brand::all();
        return JsonResponder::success($response, $data, 'List of Brand retrieved');
    }

    public function listCustomerAssets(Response $response, string $customerId)
    {
        $customer = Customer::with('asset')->find($customerId);
        if (!$customer) {
            return JsonResponder::error($response, 'Customer not found', 404);
        }
        return JsonResponder::success($response, $customer->asset, 'List of Customer Assets retrieved');
    }

    public function deleteTipe(Response $response, string $tipeId)
    {
        try {
            $tipe = Tipe::find($tipeId);
            if (!$tipe) {
                return JsonResponder::error($response, 'Tipe not found', 404);
            }
            $tipe->delete();
            return JsonResponder::success($response, null, 'Tipe deleted');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete Tipe: ' . $th->getMessage(), 500);
        }
    }

    public function deleteBrand(Response $response, string $brandId)
    {
        try {
            $brand = Brand::find($brandId);
            if (!$brand) {
                return JsonResponder::error($response, 'Brand not found', 404);
            }
            $brand->delete();
            return JsonResponder::success($response, null, 'Brand deleted');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete Brand: ' . $th->getMessage(), 500);
        }
    }

    public function updateTipe(Response $response, string $tipeId, array $data)
    {
        try {
            $tipe = Tipe::find($tipeId);
            if (!$tipe) {
                return JsonResponder::error($response, 'Tipe not found', 404);
            }
            $tipe->update($data);
            return JsonResponder::success($response, $tipe, 'Tipe updated');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to update Tipe: ' . $th->getMessage(), 500);
        }
    }

    public function updateBrand(Response $response, string $brandId, array $data)
    {
        try {
            $brand = Brand::find($brandId);
            if (!$brand) {
                return JsonResponder::error($response, 'Brand not found', 404);
            }
            $brand->update($data);
            return JsonResponder::success($response, $brand, 'Brand updated');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to update Brand: ' . $th->getMessage(), 500);
        }
    }

    public function getAllCustomers(Response $response)
    {
        try {
            $data = Customer::with('customerassets')->get();
            return JsonResponder::success($response, $data, 'All Customers retrieved');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve Customers: ' . $th->getMessage(), 500);
        }
    }
}
