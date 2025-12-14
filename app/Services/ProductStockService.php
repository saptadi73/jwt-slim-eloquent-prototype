<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\PurchaseOrder;
use App\Models\ProductOrderLine;
use App\Models\PurchaseOrderLine;
use App\Models\ProductMoveHistory;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Carbon;

class ProductStockService
{
    /**
     * Hitung stok berdasarkan sum dari product_move_histories
     * direction='in' + qty, direction='out' - qty
     */
    protected function calculateStock(Product $product): int
    {
        $inSum = ProductMoveHistory::where('product_id', $product->id)->where('direction', 'in')->sum('qty');
        $outSum = ProductMoveHistory::where('product_id', $product->id)->where('direction', 'out')->sum('qty');
        return $inSum - $outSum;
    }

    /**
     * Terapkan efek stok untuk 1 purchase order (stok masuk)
     */
    public function applyPurchaseOrder(PurchaseOrder $order): void
    {
        DB::connection()->transaction(function () use ($order) {
            // pastikan relasi productLines sudah ada di model PurchaseOrder
            foreach ($order->productLines as $line) {
                $this->increaseFromPurchaseLine($line, $order);
            }
        });
    }

    /**
     * Terapkan efek stok untuk 1 sale order (stok keluar)
     */
    public function applySaleOrder(SaleOrder $order): void
    {
        DB::connection()->transaction(function () use ($order) {
            foreach ($order->productLines as $line) {
                $this->decreaseFromSaleLine($line, $order);
            }
        });
    }

    /**
     * Tambah stok dari satu baris purchase order line
     */
    protected function increaseFromPurchaseLine(PurchaseOrderLine $line, PurchaseOrder $order): void
    {
        // relasi product() sudah ada di PurchaseOrderLine
        $product = $line->product;

        if (!$product) {
            throw new \RuntimeException('Product tidak ditemukan di PurchaseOrderLine');
        }

        $before = $this->calculateStock($product);
        $qty    = $line->qty; // GANTI jika nama kolom di DB bukan 'qty'
        $after  = $before + $qty;

        $now = Carbon::now();

        ProductMoveHistory::create([
            'product_id'      => $product->id,
            'move_type'       => 'purchase',
            'direction'       => 'in',
            'qty'             => $qty,
            'stock_before'    => $before,
            'stock_after'     => $after,
            'source_table'    => 'purchase_order_line',
            'source_id'       => $line->id,
            'source_order_id' => $order->id,
            'move_date'       => $now,
            'note'            => 'Purchase order ' . ($order->order_number ?? ''),
        ]);

        // Update stok produk berdasarkan sum histories
        $product->stok = $this->calculateStock($product);
        $product->save();
    }

    /**
     * Kurangi stok dari satu baris sale order line
     */
    protected function decreaseFromSaleLine(ProductOrderLine $line, SaleOrder $order): void
    {
        $product = $line->product;

        if (!$product) {
            throw new \RuntimeException('Product tidak ditemukan di ProductOrderLine');
        }

        $before = $this->calculateStock($product);
        $qty    = $line->qty; // GANTI jika nama kolom di DB bukan 'qty'
        $after  = $before - $qty;

        if ($after < 0) {
            throw new \RuntimeException('Stok tidak cukup untuk produk ' . $product->nama);
        }

        $now = Carbon::now();

        ProductMoveHistory::create([
            'product_id'      => $product->id,
            'move_type'       => 'sale',
            'direction'       => 'out',
            'qty'             => $qty,
            'stock_before'    => $before,
            'stock_after'     => $after,
            'source_table'    => 'product_order_line',
            'source_id'       => $line->id,
            'source_order_id' => $order->id,
            'move_date'       => $now,
            'note'            => 'Sale order ' . ($order->order_number ?? ''),
        ]);

        // Update stok produk berdasarkan sum histories
        $product->stok = $this->calculateStock($product);
        $product->save();
    }
}
