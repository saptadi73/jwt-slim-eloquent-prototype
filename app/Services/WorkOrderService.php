<?php
namespace App\Services;

use App\Models\Workorder;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Checklist;
use App\Models\ChecklistTemplate;

class WorkOrderService
{
    public function createWorkOrder(Request $request, Response $response, array $data)
    {
        try {
            $data['id'] = \Illuminate\Support\Str::uuid();
            $workorder = Workorder::create($data);
            return JsonResponder::success($response, $workorder, 'Work Order created');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create Work Order: '. $th->getMessage(), 500);
        }
    }

    public function getWorkOrderById(Response $response, string $id)
    {
        $workorder = Workorder::with(['customer', 'group', 'saleBarangLines', 'saleJasaLines'])->find($id);
        if (!$workorder) {
            return JsonResponder::error($response, 'Work Order not found', 404);
        }
        return JsonResponder::success($response, $workorder);
    }

    public function updateWorkOrder(Request $request, Response $response, string $id, array $data)
    {
        try {
            $workorder = Workorder::find($id);
            if (!$workorder) {
                return JsonResponder::error($response, 'Work Order not found', 404);
            }
            $workorder->update($data);
            return JsonResponder::success($response, $workorder, 'Work Order updated');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to update Work Order: '. $th->getMessage(), 500);
        }
    }

    public function deleteWorkOrder(Request $request, Response $response, string $id)
    {
        try {
            $workorder = Workorder::find($id);
            if (!$workorder) {
                return JsonResponder::error($response, 'Work Order not found', 404);
            }
            $workorder->delete();
            return JsonResponder::success($response, null, 'Work Order deleted');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete Work Order: '. $th->getMessage(), 500);
        }
    }

    public function getAllWorkOrders(Response $response)
    {
        $workorders = Workorder::with(['customer', 'group', 'saleBarangLines', 'saleJasaLines'])->get();
        return JsonResponder::success($response, $workorders);
    }

    public function getChecklistTemplateByJenisTitle(Response $response, array $data)
    {
        $checklistTemplates = ChecklistTemplate::where('jenis_id', $data['jenis_id'])->where('title', 'like', '%'.$data['title'].'%')->get();
        return JsonResponder::success($response, $checklistTemplates);

    }

    public function inputChecklist(Request $request, Response $response, array $data)
    {
        try {
            $checklist = Checklist::create($data);
            return JsonResponder::success($response, $checklist, 'Checklist created');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create Checklist: '. $th->getMessage(), 500);
        }
    }

    public function getChecklistsByWorkOrder(Response $response, string $workorderId)
    {
        $checklists = Checklist::where('workorder_id', $workorderId)->with(['checklist', 'pegawai','checklist_template'])->get();
        return JsonResponder::success($response, $checklists);
    }

}

?>