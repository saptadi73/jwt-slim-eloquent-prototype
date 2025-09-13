<?php
namespace App\Services;

use App\Models\Workorder;
use App\Models\WorkOrderAcService;
use App\Models\WorkOrderPenyewaan;
use App\Models\Pegawai;
use App\Models\Customer;
use App\Models\CustomerAsset;
use App\Models\WorkorderPenjualan;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Checklist;
use App\Models\ChecklistTemplate;
use App\Models\JenisWorkorder;
use App\Models\Brand;
use App\Models\Tipe;

class WorkOrderService
{
    private function nextWoCode(): string
    {
        $prefix = 'WO-';
        // Ambil angka terbesar dari nowo yang sudah ada
        $max = Workorder::where('nowo', 'like', $prefix . '%')
            ->selectRaw("MAX(CAST(SUBSTRING(nowo, LENGTH(?) + 1) AS INTEGER)) as max_code", [$prefix])
            ->value('max_code');

        $next = ((int)$max) + 1;
        return $prefix . str_pad((string)$next, 5, '0', STR_PAD_LEFT);
    }

    

}

?>