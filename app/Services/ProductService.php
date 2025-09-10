<?php
namespace App\Services;

use App\Models\Product;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Support\RequestHelper;

class ProductService
{
    public function createProduct(Request $request, Response $response, array $data)
    {
        try {
            $product = new Product($data);
            $product->save();

            return JsonResponder::success($response, $product);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th);
        }
    }

    public function listProducts(Response $response)
    {
        try {
            $products = Product::with(['kategori', 'satuan'])->get();
            return JsonResponder::success($response, $products);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th);
        }
    }
}
?>