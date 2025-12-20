<?php

namespace App\Services;

use App\Models\SaleOrder;
use App\Models\ProductOrderLine;
use App\Models\Customer;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\ProductService;
use Illuminate\Database\Capsule\Manager as DB;
use Exception;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Brand;
use App\Models\ServiceOrderLine;
use App\Models\Service;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use InvalidArgumentException;

class SaleOrderService
{
    private ProductStockService $productStockService;

    public function __construct(ProductStockService $productStockService)
    {
        $this->productStockService = $productStockService;
    }

    public function createSaleOrder(Response $response, array $data)
    {
        DB::beginTransaction();
        try {
            // Basic FK validation: customer_id
            $errors = [];
            if (!isset($data['customer_id']) || !is_string($data['customer_id'])) {
                $errors[] = 'customer_id wajib diisi';
            } elseif (!\Ramsey\Uuid\Uuid::isValid($data['customer_id'])) {
                $errors[] = 'customer_id harus UUID valid';
            } elseif (!Customer::find($data['customer_id'])) {
                $errors[] = 'customer_id tidak ditemukan';
            }

            // Validate product lines
            if (isset($data['product_lines']) && is_array($data['product_lines'])) {
                foreach ($data['product_lines'] as $idx => $line) {
                    if (!isset($line['product_id']) || !\Ramsey\Uuid\Uuid::isValid($line['product_id'])) {
                        $errors[] = "product_lines[$idx].product_id harus UUID valid";
                    } elseif (!Product::find($line['product_id'])) {
                        $errors[] = "product_lines[$idx].product_id tidak ditemukan";
                    }
                }
            }

            // Validate service lines
            if (isset($data['service_lines']) && is_array($data['service_lines'])) {
                foreach ($data['service_lines'] as $idx => $line) {
                    if (!isset($line['service_id']) || !\Ramsey\Uuid\Uuid::isValid($line['service_id'])) {
                        $errors[] = "service_lines[$idx].service_id harus UUID valid";
                    } elseif (!Service::find($line['service_id'])) {
                        $errors[] = "service_lines[$idx].service_id tidak ditemukan";
                    }
                }
            }

            // Normalize optional snapshot customer fields (always optional)
            foreach (['nama', 'alamat', 'hp', 'keterangan'] as $k) {
                if (array_key_exists($k, $data)) {
                    if ($data[$k] === null || $data[$k] === '') {
                        // keep null/empty as-is
                        continue;
                    }
                    // cast to string
                    $data[$k] = (string) $data[$k];
                    if ($k === 'hp') {
                        // remove spaces and cap length to 30
                        $data[$k] = substr(preg_replace('/\s+/', '', $data[$k]), 0, 30);
                    }
                }
            }

            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }
            // Create Sale Order
            $saleOrder = new SaleOrder($data);
            $saleOrder->save();

            // Create Product Order Lines
            if (isset($data['product_lines']) && is_array($data['product_lines'])) {
                foreach ($data['product_lines'] as $lineData) {
                    $lineData['sale_order_id'] = $saleOrder->id;
                    $productLine = new ProductOrderLine($lineData);
                    $productLine->save();
                }
            }

            // Create Service Order Lines
            if (isset($data['service_lines']) && is_array($data['service_lines'])) {
                foreach ($data['service_lines'] as $lineData) {
                    $lineData['sale_order_id'] = $saleOrder->id;
                    $serviceLine = new ServiceOrderLine($lineData);
                    $serviceLine->save();
                }
            }

            DB::commit();
            return JsonResponder::success($response, $saleOrder);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th, 500);
        }
    }

    public function getSaleOrder(Response $response, string $SaleOrderID)
    {
        try {
            $saleOrder = SaleOrder::with(['customer', 'productLines.product', 'serviceLines.service'])
                ->findOrFail($SaleOrderID);
            return JsonResponder::success($response, $saleOrder);
        } catch (\Throwable $th) {
            //throw $th;
            return JsonResponder::error($response, $th);
        }
    }

    public function updateSaleOrder(Response $response, string $SaleOrderID, array $data)
    {
        DB::beginTransaction();
        try {
            $saleOrder = SaleOrder::find($SaleOrderID);
            if (!$saleOrder) {
                throw new ModelNotFoundException("Sale Order not found");
            }

            $errors = [];
            if (isset($data['customer_id'])) {
                if (!Uuid::isValid($data['customer_id'])) {
                    $errors[] = 'customer_id harus UUID valid';
                } elseif (!Customer::find($data['customer_id'])) {
                    $errors[] = 'customer_id tidak ditemukan';
                }
            }

            // Normalize optional snapshot customer fields on update (always optional)
            foreach (['nama', 'alamat', 'hp', 'keterangan'] as $k) {
                if (array_key_exists($k, $data)) {
                    if ($data[$k] === null || $data[$k] === '') {
                        continue;
                    }
                    $data[$k] = (string) $data[$k];
                    if ($k === 'hp') {
                        $data[$k] = substr(preg_replace('/\s+/', '', $data[$k]), 0, 30);
                    }
                }
            }

            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            $oldStatus = $saleOrder->status;
            $saleOrder->fill($data);
            $saleOrder->save();

            // Jika status berubah ke 'confirmed', apply stock
            if ($oldStatus != OrderStatus::Confirmed && $saleOrder->status == OrderStatus::Confirmed) {
                $this->productStockService->applySaleOrder($saleOrder);
            }

            DB::commit();
            return JsonResponder::success($response, $saleOrder);

        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }

    public function listSaleOrders(Response $response)
    {
        try {
            $saleOrders = SaleOrder::with(['customer'])->get();
            return JsonResponder::success($response, $saleOrders);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th);
        }
    }

    public function deleteSaleOrder(Response $response, string $SaleOrderID)
    {
        DB::beginTransaction();
        try {
            $saleOrder = SaleOrder::find($SaleOrderID);
            if (!$saleOrder) {
                throw new ModelNotFoundException("Sale Order not found");
            }
            $saleOrder->delete();
            DB::commit();
            return JsonResponder::success($response, ["message" => "Sale Order deleted successfully"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }

    public function changeOrderStatus(Response $response, string $SaleOrderID, OrderStatus $newStatus)
    {
        DB::beginTransaction();
        try {
            $saleOrder = SaleOrder::find($SaleOrderID);
            if (!$saleOrder) {
                throw new ModelNotFoundException("Sale Order not found");
            }
            $saleOrder->status = $newStatus;
            $saleOrder->save();
            DB::commit();
            return JsonResponder::success($response, $saleOrder);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }

    public function deleteProductLine(Response $response, string $lineID)
    {
        DB::beginTransaction();
        try {
            $productLine = ProductOrderLine::find($lineID);
            if (!$productLine) {
                throw new ModelNotFoundException("Product Order Line not found");
            }
            $productLine->delete();
            DB::commit();
            return JsonResponder::success($response, ["message" => "Product Order Line deleted successfully"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }

    public function deleteServiceLine(Response $response, string $lineID)
    {
        DB::beginTransaction();
        try {
            $serviceLine = ServiceOrderLine::find($lineID);
            if (!$serviceLine) {
                throw new ModelNotFoundException("Service Order Line not found");
            }
            $serviceLine->delete();
            DB::commit();
            return JsonResponder::success($response, ["message" => "Service Order Line deleted successfully"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }

    public function AddProductLine(Response $response, string $SaleOrderID, array $lineData)
    {
        DB::beginTransaction();
        try {
            $saleOrder = SaleOrder::find($SaleOrderID);
            if (!$saleOrder) {
                throw new ModelNotFoundException("Sale Order not found");
            }

            $errors = [];
            if (!isset($lineData['product_id']) || !Uuid::isValid($lineData['product_id'])) {
                $errors[] = 'product_id harus UUID valid';
            } elseif (!Product::find($lineData['product_id'])) {
                $errors[] = 'product_id tidak ditemukan';
            }

            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            $lineData['sale_order_id'] = $SaleOrderID;
            $productLine = new ProductOrderLine($lineData);
            $productLine->save();
            DB::commit();
            return JsonResponder::success($response, $productLine);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }

    public function AddServiceLine(Response $response, string $SaleOrderID, array $lineData)
    {
        DB::beginTransaction();
        try {
            $saleOrder = SaleOrder::find($SaleOrderID);
            if (!$saleOrder) {
                throw new ModelNotFoundException("Sale Order not found");
            }

            $errors = [];
            if (!isset($lineData['service_id']) || !Uuid::isValid($lineData['service_id'])) {
                $errors[] = 'service_id harus UUID valid';
            } elseif (!Service::find($lineData['service_id'])) {
                $errors[] = 'service_id tidak ditemukan';
            }

            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            $lineData['sale_order_id'] = $SaleOrderID;
            $serviceLine = new ServiceOrderLine($lineData);
            $serviceLine->save();
            DB::commit();
            return JsonResponder::success($response, $serviceLine);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }
}

