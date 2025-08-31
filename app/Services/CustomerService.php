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

class CustomerService
{
    public function createCustomerAndAsset(Request $request, Response $response, array $data, File $file)
    {
        $data['kode_pelanggan'] = 'CUST-' . str_pad((string)(Customer::max('id') + 1), 5, '0', STR_PAD_LEFT);
        $customer_data=Arr::only($data, ['nama', 'alamat', 'hp', 'kode_pelanggan']);
        $asset_data=Arr::only($data, ['tipe','keterangan','lokasi','brand','model','freon','kapasitas']);

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
            }else{
                $customer->gambar = null;
                $customer->save();
                $msg_file = $file ? 'File upload error code: ' . $file->getError() : 'No file uploaded';
            }
            return JsonResponder::success($response, $customer, 'Customer and Asset created' . ($msg_file ?? ''));
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create Customer and Asset: '. $th->getMessage(), 500);
        }
    }

    public function createCustomerAsset(array $data): CustomerAsset
    {
        return CustomerAsset::create($data);
    }
}

?>