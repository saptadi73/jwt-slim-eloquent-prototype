<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Services\ProductStockService;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\PurchaseOrderLine;

class PurchaseOrderService
{
    private ProductStockService $productStockService;

    public function __construct(ProductStockService $productStockService)
    {
        $this->productStockService = $productStockService;
    }

    public function createPurchaseOrder(Response $response, array $data)
    {
        DB::beginTransaction();
        try {
            // Create Purchase Order
            $purchaseOrder = new PurchaseOrder($data);
            $purchaseOrder->save();

            // Create Purchase Order Lines
            if (isset($data['product_lines']) && is_array($data['product_lines'])) {
                foreach ($data['product_lines'] as $lineData) {
                    $lineData['purchase_order_id'] = $purchaseOrder->id;
                    $productLine = new PurchaseOrderLine($lineData);
                    $productLine->save();
                }
            }

            DB::commit();
            return JsonResponder::success($response, $purchaseOrder);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }

    public function getPurchaseOrder(Response $response, string $purchaseOrderID)
    {
        try {
            $purchaseOrder = PurchaseOrder::with(['vendor', 'productLines.product'])->findOrFail($purchaseOrderID);
            return JsonResponder::success($response, $purchaseOrder);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th);
        }
    }

    public function listPurchaseOrders(Response $response)
    {
        try {
            $purchaseOrders = PurchaseOrder::with(['vendor'])->get();
            return JsonResponder::success($response, $purchaseOrders);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th);
        }
    }

    public function updatePurchaseOrder(Response $response, string $purchaseOrderID, array $data)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderID);
            $oldStatus = $purchaseOrder->status;
            $purchaseOrder->update($data);

            // Jika status berubah ke 'confirmed', apply stock
            if ($oldStatus != OrderStatus::Confirmed && $purchaseOrder->status == OrderStatus::Confirmed) {
                $this->productStockService->applyPurchaseOrder($purchaseOrder);
            }

            // Optionally handle updating purchase order lines here

            DB::commit();
            return JsonResponder::success($response, $purchaseOrder);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }

    public function deletePurchaseOrder(Response $response, string $purchaseOrderID)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderID);
            $purchaseOrder->delete();

            // Optionally handle deleting related purchase order lines here

            DB::commit();
            return JsonResponder::success($response, ['message' => 'Purchase Order deleted successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }

    public function listVendors(Response $response)
    {
        try {
            $vendors = Vendor::all();
            return JsonResponder::success($response, $vendors);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th);
        }
    }

    public function deletePurchaseOrderLine(Response $response, string $purchaseOrderlineID)
    {
        DB::beginTransaction();
        try {
            if (isset($purchaseOrderlineID)) {
                $purchaseOrderLine = PurchaseOrderLine::find($purchaseOrderlineID);
                $purchaseOrderLine->delete();
                
            }
            DB::commit();
            return JsonResponder::success($response, $purchaseOrderLine);
        } catch (\Throwable $th) {
            //throw $th;
            return JsonResponder::error($response, $th);
        }
    }

    public function updatePurchaseOrderLine(Response $response, string $purchaseOrderlineID, array $data)
    {
        DB::beginTransaction();
        try {
            $purchaseOrderLine = PurchaseOrderLine::find($purchaseOrderlineID);
            if (!$purchaseOrderLine) {
                return JsonResponder::error($response, "Purchase Order Line not found");
            }
            $purchaseOrderLine->fill($data);
            $purchaseOrderLine->save();
            DB::commit();
            return JsonResponder::success($response, $purchaseOrderLine);

        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }

    public function AddPurchaseOrderLine(Response $response, string $purchaseOrderID, array $lineData)
    {
        DB::beginTransaction();
        try {
            $purchaseOrderLine = new PurchaseOrderLine($lineData);
            $purchaseOrderLine->purchase_order_id = $purchaseOrderID;
            $purchaseOrderLine->save();
            DB::commit();
            return JsonResponder::success($response, $purchaseOrderLine);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }
}