<?php
namespace App\Services;

use App\Models\Workorder;
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
    public static function nextWorkOrderNumber()
    {
        $lastWorkOrder = Workorder::orderBy('id', 'desc')->first();
        if ($lastWorkOrder) {
            $lastNumber = (int) substr($lastWorkOrder->workorder_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }
        return 'WO-' . date('Ymd') . '-' . $nextNumber;
    }

}

?>