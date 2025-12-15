<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\ProductOrderLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\ProductMoveHistory;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Carbon;

class ProductStockService
{
    /**
     * Hitung stok berdasarkan sum qty dari product_move_histories
     * Purchase: qty positif (+)
     * Sale: qty negatif (-)
     * Final stock = SUM(qty)
     */
    protected function calculateStock(Product $product): float
    {
        return ProductMoveHistory::where('product_id', $product->id)->sum('qty') ?? 0;
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
     * Menyimpan qty positif (+$line->qty)
     */
    protected function increaseFromPurchaseLine(PurchaseOrderLine $line, PurchaseOrder $order): void
    {
        $product = $line->product;

        if (!$product) {
            throw new \RuntimeException('Product tidak ditemukan di PurchaseOrderLine');
        }

        $stockBefore = $this->calculateStock($product);
        $qty = (float) $line->qty; // qty positif untuk pembelian
        $stockAfter = $stockBefore + $qty;

        $now = Carbon::now();

        ProductMoveHistory::create([
            'product_id'      => $product->id,
            'move_type'       => 'purchase',
            'direction'       => 'in', // Pembelian = stok masuk
            'qty'             => $qty, // Positif untuk purchase
            'stock_before'    => $stockBefore,
            'stock_after'     => $stockAfter,
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
     * Menyimpan qty negatif (-$line->qty)
     */
    protected function decreaseFromSaleLine(ProductOrderLine $line, SaleOrder $order): void
    {
        $product = $line->product;

        if (!$product) {
            throw new \RuntimeException('Product tidak ditemukan di ProductOrderLine');
        }

        $stockBefore = $this->calculateStock($product);
        $qty = -1 * (float) $line->qty; // qty negatif untuk penjualan
        $stockAfter = $stockBefore + $qty; // += negatif = kurang

        if ($stockAfter < 0) {
            throw new \RuntimeException('Stok tidak cukup untuk produk ' . $product->nama);
        }

        $now = Carbon::now();

        ProductMoveHistory::create([
            'product_id'      => $product->id,
            'move_type'       => 'sale',
            'direction'       => 'out', // Penjualan = stok keluar
            'qty'             => $qty, // Negatif untuk sale
            'stock_before'    => $stockBefore,
            'stock_after'     => $stockAfter,
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
