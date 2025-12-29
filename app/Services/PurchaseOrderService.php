<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\ChartOfAccount;
use App\Services\ProductStockService;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;
use App\Models\PurchaseOrderLine;
use Illuminate\Support\Str;
use App\Models\Vendor as VendorModel;
use App\Models\Product as ProductModel;

class PurchaseOrderService
{
    private ProductStockService $productStockService;
    private AccountingService $accountingService;

    public function __construct(ProductStockService $productStockService, AccountingService $accountingService)
    {
        $this->productStockService = $productStockService;
        $this->accountingService = $accountingService;
    }

    public function createPurchaseOrder(Response $response, array $data)
    {
        DB::beginTransaction();
        try {
            // Validate vendor_id
            $errors = [];
            if (!isset($data['vendor_id']) || !is_string($data['vendor_id'])) {
                $errors[] = 'vendor_id wajib diisi';
            } elseif (!\Ramsey\Uuid\Uuid::isValid($data['vendor_id'])) {
                $errors[] = 'vendor_id harus UUID valid';
            } elseif (!VendorModel::find($data['vendor_id'])) {
                $errors[] = 'vendor_id tidak ditemukan';
            }

            // Validate product_lines
            if (isset($data['product_lines']) && is_array($data['product_lines'])) {
                foreach ($data['product_lines'] as $idx => $line) {
                    if (!isset($line['product_id']) || !\Ramsey\Uuid\Uuid::isValid($line['product_id'])) {
                        $errors[] = "product_lines[$idx].product_id harus UUID valid";
                    } elseif (!ProductModel::find($line['product_id'])) {
                        $errors[] = "product_lines[$idx].product_id tidak ditemukan";
                    }
                }
            }

            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }
            // Create Purchase Order
            $purchaseOrder = new PurchaseOrder($data);
            // Explicitly assign UUID to avoid null id issues
            $purchaseOrder->id = (string) Str::uuid();
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
            // Return with relations for convenience
            $result = PurchaseOrder::with(['vendor', 'productLines.product'])->find($purchaseOrder->id);
            return JsonResponder::success($response, $result);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th, 500);
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

            $errors = [];
            if (isset($data['vendor_id'])) {
                if (!\Ramsey\Uuid\Uuid::isValid($data['vendor_id'])) {
                    $errors[] = 'vendor_id harus UUID valid';
                } elseif (!VendorModel::find($data['vendor_id'])) {
                    $errors[] = 'vendor_id tidak ditemukan';
                }
            }

            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }
            
            $oldStatus = $purchaseOrder->status;
            $purchaseOrder->update($data);
            $newStatus = $purchaseOrder->status;

            // Jika status berubah ke 'confirmed', apply stock dan create journal
            $isStatusChangedToConfirmed = (
                $oldStatus !== OrderStatus::Confirmed && 
                $newStatus === OrderStatus::Confirmed
            );
            
            if ($isStatusChangedToConfirmed) {
                // Apply stock changes
                $this->productStockService->applyPurchaseOrder($purchaseOrder);
                
                // Create purchase journal entry
                try {
                    $this->createPurchaseJournalForOrder($purchaseOrder, $data['created_by'] ?? null);
                } catch (\Exception $journalException) {
                    // Rollback transaction if journal creation fails
                    DB::rollBack();
                    return JsonResponder::error(
                        $response, 
                        'Purchase order updated but journal creation failed: ' . $journalException->getMessage(), 
                        500
                    );
                }
            }

            DB::commit();
            $result = PurchaseOrder::with(['vendor', 'productLines.product'])->find($purchaseOrder->id);
            return JsonResponder::success($response, $result, 'Purchase order updated successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    /**
     * Helper method to create purchase journal for a purchase order
     */
    private function createPurchaseJournalForOrder($purchaseOrder, $createdBy = null)
    {
        return DB::connection()->transaction(function () use ($purchaseOrder, $createdBy) {
            $purchaseOrder = PurchaseOrder::with('vendor', 'productLines.product')->find($purchaseOrder->id);
            
            if (!$purchaseOrder) {
                throw new \Exception('Purchase order not found');
            }

            // Get CoA IDs
            // Get CoA IDs - try 2010 first (Hutang Usaha), then fallback to 2110
            // 1130 = Persediaan (Inventory)
            // 2010 = Hutang Usaha (Accounts Payable) - preferred
            // 2110 = Accounts Payable - alternative
            $inventory = ChartOfAccount::where('code', '1130')->first();
            $accountsPayable = ChartOfAccount::where('code', '2010')->first();
            
            // Fallback to 2110 if 2010 doesn't exist
            if (!$accountsPayable) {
                $accountsPayable = ChartOfAccount::where('code', '2110')->first();
            }

            if (!$inventory || !$accountsPayable) {
                if (!$inventory) {
                    throw new \Exception('Required COA (1130 - Inventory) not found');
                }
                if (!$accountsPayable) {
                    throw new \Exception('Required COA for Hutang Usaha (2010 or 2110) not found');
                }
            }

            // Create journal entry
            $entry = new JournalEntry([
                'entry_date' => $purchaseOrder->order_date,
                'reference_number' => 'PUR-' . $purchaseOrder->order_number,
                'description' => 'Purchase Journal - ' . $purchaseOrder->vendor->name,
                'status' => 'posted',
                'created_by' => $createdBy,
            ]);
            $entry->id = (string) Str::uuid();
            $entry->save();

            // Debit Inventory / Credit Accounts Payable
            $line = new JournalLine([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $inventory->id,
                'description' => 'Inventory Purchase - ' . $purchaseOrder->order_number,
                'debit' => $purchaseOrder->total,
                'credit' => 0,
            ]);
            $line->id = (string) Str::uuid();
            $line->save();

            $line = new JournalLine([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $accountsPayable->id,
                'description' => 'Accounts Payable - ' . $purchaseOrder->vendor->name,
                'debit' => 0,
                'credit' => $purchaseOrder->total,
                'vendor_id' => $purchaseOrder->vendor_id,
            ]);
            $line->id = (string) Str::uuid();
            $line->save();

            return $entry->load('journalLines.chartOfAccount');
        });
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

            $errors = [];
            if (isset($data['product_id'])) {
                if (!\Ramsey\Uuid\Uuid::isValid($data['product_id'])) {
                    $errors[] = 'product_id harus UUID valid';
                } elseif (!ProductModel::find($data['product_id'])) {
                    $errors[] = 'product_id tidak ditemukan';
                }
            }

            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
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

    public function addPurchaseOrderLine(Response $response, string $purchaseOrderID, array $lineData)
    {
        DB::beginTransaction();
        try {
            $errors = [];
            if (!isset($lineData['product_id']) || !\Ramsey\Uuid\Uuid::isValid($lineData['product_id'])) {
                $errors[] = 'product_id harus UUID valid';
            } elseif (!ProductModel::find($lineData['product_id'])) {
                $errors[] = 'product_id tidak ditemukan';
            }

            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }
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