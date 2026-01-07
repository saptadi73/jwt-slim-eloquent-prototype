<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductMoveHistory;
use App\Support\JsonResponder;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;

class InventoryReportService
{
    /**
     * GET /api/reports/product-movements
     * Params: product_id (optional), start_date (Y-m-d), end_date (Y-m-d)
     */
    public function getProductMovements(Response $response, array $params): Response
    {
        try {
            $startDate = $params['start_date'] ?? date('Y-m-01');
            $endDate   = $params['end_date'] ?? date('Y-m-d');
            $productId = $params['product_id'] ?? null;

            // Build candidate product IDs: those with moves in range (and optionally filtered by product_id)
            $productIdsQuery = ProductMoveHistory::query()
                ->when($productId, function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                })
                ->whereBetween('move_date', [$startDate, $endDate])
                ->select('product_id')
                ->distinct();

            $productIds = $productIdsQuery->pluck('product_id');
            if ($productId && $productIds->isEmpty()) {
                // If user asked for a specific product but no movement in range, still return opening/closing without lines
                $productIds = collect([$productId]);
            }

            $items = [];

            foreach ($productIds as $pid) {
                $product = Product::find($pid);
                if (!$product) {
                    // Skip if product missing
                    continue;
                }

                // Opening stock (sum of qty before startDate)
                $openingQty = (float) ProductMoveHistory::where('product_id', $pid)
                    ->where('move_date', '<', $startDate)
                    ->sum('qty');

                // Movements in period
                $lines = ProductMoveHistory::where('product_id', $pid)
                    ->whereBetween('move_date', [$startDate, $endDate])
                    ->orderBy('move_date')
                    ->orderBy('created_at')
                    ->get();

                $running = $openingQty;
                $totalIn = 0.0;
                $totalOut = 0.0;
                $tx = [];

                foreach ($lines as $row) {
                    $qty = (float) $row->qty; // positive in, negative out per ProductStockService
                    $qtyIn = $qty > 0 ? $qty : 0.0;
                    $qtyOut = $qty < 0 ? abs($qty) : 0.0;
                    $running += $qty;

                    $totalIn += $qtyIn;
                    $totalOut += $qtyOut;

                    $tx[] = [
                        'date' => $row->move_date ? date('Y-m-d H:i:s', strtotime($row->move_date)) : ($row->created_at ? $row->created_at->format('Y-m-d H:i:s') : null),
                        'move_type' => $row->move_type ?? null,
                        'direction' => $row->direction ?? ($qty >= 0 ? 'in' : 'out'),
                        'qty_in' => $qtyIn,
                        'qty_out' => $qtyOut,
                        'stock_before' => $row->stock_before ?? null,
                        'stock_after' => $row->stock_after ?? null,
                        'source_table' => $row->source_table ?? null,
                        'source_id' => $row->source_id ?? null,
                        'source_order_id' => $row->source_order_id ?? null,
                        'note' => $row->note ?? null,
                    ];
                }

                $closingQty = $running;

                $items[] = [
                    'product' => [
                        'id' => $product->id,
                        'kode' => $product->kode,
                        'nama' => $product->nama,
                        'satuan' => optional($product->satuan)->nama ?? null,
                        'brand' => optional($product->brand)->nama ?? null,
                    ],
                    'opening_qty' => $openingQty,
                    'total_in' => $totalIn,
                    'total_out' => $totalOut,
                    'net_movement' => $totalIn - $totalOut,
                    'closing_qty' => $closingQty,
                    'transactions' => $tx,
                ];
            }

            $report = [
                'report_name' => 'Laporan Pergerakan Barang',
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'filter' => [
                    'product_id' => $productId,
                ],
                'items' => $items,
            ];

            return JsonResponder::success($response, $report, 'Laporan pergerakan barang berhasil dibuat');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal membuat laporan pergerakan barang: ' . $th->getMessage(), 500);
        }
    }

    /**
     * GET /api/reports/product-movements/summary
     * Required params: start_date (Y-m-d), end_date (Y-m-d)
     * Optional: product_id
     * Returns per-product opening, total_in, total_out, net_movement, closing (no transaction lines)
     */
    public function getProductMovementsSummary(Response $response, array $params): Response
    {
        try {
            $startDate = $params['start_date'] ?? null;
            $endDate   = $params['end_date'] ?? null;
            $productId = $params['product_id'] ?? null;

            if (!$startDate || !$endDate) {
                return JsonResponder::error($response, 'Parameter start_date dan end_date wajib diisi', 400);
            }

            $productIdsQuery = ProductMoveHistory::query()
                ->when($productId, function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                })
                ->whereBetween('move_date', [$startDate, $endDate])
                ->select('product_id')
                ->distinct();

            $productIds = $productIdsQuery->pluck('product_id');
            if ($productId && $productIds->isEmpty()) {
                $productIds = collect([$productId]);
            }

            $items = [];

            foreach ($productIds as $pid) {
                $product = Product::find($pid);
                if (!$product) {
                    continue;
                }

                $openingQty = (float) ProductMoveHistory::where('product_id', $pid)
                    ->where('move_date', '<', $startDate)
                    ->sum('qty');

                $periodQty = ProductMoveHistory::where('product_id', $pid)
                    ->whereBetween('move_date', [$startDate, $endDate])
                    ->get(['qty']);

                $totalIn = 0.0;
                $totalOut = 0.0;
                foreach ($periodQty as $row) {
                    $qty = (float) $row->qty;
                    if ($qty >= 0) { $totalIn += $qty; } else { $totalOut += abs($qty); }
                }

                $closingQty = $openingQty + $totalIn - $totalOut;

                $items[] = [
                    'product' => [
                        'id' => $product->id,
                        'kode' => $product->kode,
                        'nama' => $product->nama,
                        'satuan' => optional($product->satuan)->nama ?? null,
                        'brand' => optional($product->brand)->nama ?? null,
                    ],
                    'opening_qty' => $openingQty,
                    'total_in' => $totalIn,
                    'total_out' => $totalOut,
                    'net_movement' => $totalIn - $totalOut,
                    'closing_qty' => $closingQty,
                ];
            }

            $report = [
                'report_name' => 'Ringkasan Pergerakan Barang',
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'filter' => [
                    'product_id' => $productId,
                ],
                'items' => $items,
            ];

            return JsonResponder::success($response, $report, 'Ringkasan pergerakan barang berhasil dibuat');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal membuat ringkasan pergerakan: ' . $th->getMessage(), 500);
        }
    }
}
