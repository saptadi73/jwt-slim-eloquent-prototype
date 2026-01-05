<?php

namespace App\Services;

use App\Models\Workorder;
use App\Models\SaleOrder;
use App\Models\PurchaseOrder;
use App\Models\Expense;
use App\Models\Attendance;
use App\Models\Pegawai;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get workorder metrics for today and overview
     */
    public static function getWorkorderMetrics()
    {
        $today = Carbon::today();
        $now = Carbon::now();

        // Workorder hari ini
        $workOrdersToday = Workorder::whereDate('tanggal', $today)->count();

        // Workorder belum selesai (pending) - anggap status != 'completed' atau 'finished'
        $workOrdersPending = Workorder::whereNotIn('jenis', ['completed', 'finished'])
            ->count();

        // Workorder yang sudah selesai
        $workOrdersFinished = Workorder::whereIn('jenis', ['completed', 'finished'])
            ->count();

        // Total workorder
        $workOrdersTotal = Workorder::count();

        return [
            'today' => $workOrdersToday,
            'pending' => $workOrdersPending,
            'finished' => $workOrdersFinished,
            'total' => $workOrdersTotal
        ];
    }

    /**
     * Get attendance metrics for today
     */
    public static function getAttendanceMetrics()
    {
        $today = Carbon::today();

        // Pegawai yang hadir hari ini
        $presentToday = Attendance::whereDate('date', $today)
            ->where('status', 'hadir')
            ->count();

        // Total pegawai
        $totalPegawai = Pegawai::count();

        return [
            'present' => $presentToday,
            'total' => $totalPegawai,
            'absent' => $totalPegawai - $presentToday
        ];
    }

    /**
     * Get workorder status for pie chart
     */
    public static function getWorkorderStatus()
    {
        $completed = Workorder::whereIn('jenis', ['completed', 'finished'])->count();
        $pending = Workorder::whereNotIn('jenis', ['completed', 'finished'])->count();

        return [
            'completed' => $completed,
            'pending' => $pending,
            'total' => $completed + $pending
        ];
    }

    /**
     * Get sales data for last N months
     * @param int $months Number of months to retrieve (default 6)
     */
    public static function getSalesData($months = 6)
    {
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Get all sales within date range
        $sales = SaleOrder::whereBetween('order_date', [$startDate, $endDate])
            ->get(['order_date', 'total']);

        // Format data untuk chart
        $data = [];
        $current = $startDate->copy();

        for ($i = 0; $i < $months; $i++) {
            $monthLabel = $current->format('M'); // Jan, Feb, etc
            $yearMonth = $current->format('Y-m');

            // Sum total for this month
            $monthTotal = $sales->filter(function ($sale) use ($yearMonth) {
                return Carbon::parse($sale->order_date)->format('Y-m') === $yearMonth;
            })->sum('total');

            $data[] = [
                'month' => $monthLabel,
                'monthFull' => $current->format('F'),
                'sales' => (float) $monthTotal
            ];

            $current->addMonth();
        }

        return $data;
    }

    /**
     * Get purchases data for last N months
     * @param int $months Number of months to retrieve (default 6)
     */
    public static function getPurchasesData($months = 6)
    {
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Get all purchases within date range
        $purchases = PurchaseOrder::whereBetween('order_date', [$startDate, $endDate])
            ->get(['order_date', 'total']);

        // Format data untuk chart
        $data = [];
        $current = $startDate->copy();

        for ($i = 0; $i < $months; $i++) {
            $monthLabel = $current->format('M');
            $yearMonth = $current->format('Y-m');

            // Sum total for this month
            $monthTotal = $purchases->filter(function ($purchase) use ($yearMonth) {
                return Carbon::parse($purchase->order_date)->format('Y-m') === $yearMonth;
            })->sum('total');

            $data[] = [
                'month' => $monthLabel,
                'monthFull' => $current->format('F'),
                'purchases' => (float) $monthTotal
            ];

            $current->addMonth();
        }

        return $data;
    }

    /**
     * Get expenses data for last N months
     * @param int $months Number of months to retrieve (default 6)
     */
    public static function getExpensesData($months = 6)
    {
        $startDate = Carbon::now()->subMonths($months - 1)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Get all expenses within date range
        $expenses = Expense::whereBetween('tanggal', [$startDate, $endDate])
            ->get(['tanggal', 'jumlah']);

        // Format data untuk chart
        $data = [];
        $current = $startDate->copy();

        for ($i = 0; $i < $months; $i++) {
            $monthLabel = $current->format('M');
            $yearMonth = $current->format('Y-m');

            // Sum total for this month
            $monthTotal = $expenses->filter(function ($expense) use ($yearMonth) {
                return Carbon::parse($expense->tanggal)->format('Y-m') === $yearMonth;
            })->sum('jumlah');

            $data[] = [
                'month' => $monthLabel,
                'monthFull' => $current->format('F'),
                'expenses' => (float) $monthTotal
            ];

            $current->addMonth();
        }

        return $data;
    }

    /**
     * Get combined comparison data for Purchase, Expenses vs Sales
     * @param int $months Number of months to retrieve (default 6)
     */
    public static function getPurchaseExpensesSalesComparison($months = 6)
    {
        $sales = self::getSalesData($months);
        $purchases = self::getPurchasesData($months);
        $expenses = self::getExpensesData($months);

        // Merge data
        $data = [];
        foreach ($sales as $key => $sale) {
            $data[] = [
                'month' => $sale['month'],
                'monthFull' => $sale['monthFull'],
                'sales' => $sale['sales'],
                'purchases' => $purchases[$key]['purchases'] ?? 0,
                'expenses' => $expenses[$key]['expenses'] ?? 0
            ];
        }

        return $data;
    }

    /**
     * Get complete dashboard data
     */
    public static function getCompleteDashboard($months = 6)
    {
        return [
            'workorder_metrics' => self::getWorkorderMetrics(),
            'attendance_metrics' => self::getAttendanceMetrics(),
            'workorder_status' => self::getWorkorderStatus(),
            'sales_data' => self::getSalesData($months),
            'purchases_data' => self::getPurchasesData($months),
            'expenses_data' => self::getExpensesData($months),
            'comparison_data' => self::getPurchaseExpensesSalesComparison($months),
        ];
    }

    /**
     * Get dashboard summary (only metrics, no charts)
     */
    public static function getDashboardSummary()
    {
        return [
            'workorder_metrics' => self::getWorkorderMetrics(),
            'attendance_metrics' => self::getAttendanceMetrics(),
            'workorder_status' => self::getWorkorderStatus(),
        ];
    }
}
