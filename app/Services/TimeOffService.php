<?php

namespace App\Services;

use App\Models\TimeOff;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;

class TimeOffService
{
    /**
     * Get all time offs
     */
    public function index(Response $response, array $params): Response
    {
        try {
            $query = TimeOff::with(['employee', 'approver']);

            // Filter by employee
            if (isset($params['employee_id'])) {
                $query->where('employee_id', $params['employee_id']);
            }

            // Filter by status
            if (isset($params['status'])) {
                $query->where('status', $params['status']);
            }

            // Filter by type
            if (isset($params['type'])) {
                $query->where('type', $params['type']);
            }

            // Filter by date range
            if (isset($params['start_date'])) {
                $query->where('start_date', '>=', $params['start_date']);
            }
            if (isset($params['end_date'])) {
                $query->where('end_date', '<=', $params['end_date']);
            }

            $timeOffs = $query->orderBy('start_date', 'desc')->get();

            return JsonResponder::success($response, $timeOffs, 'Time offs retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve time offs: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get time off by ID
     */
    public function show(Response $response, string $id): Response
    {
        try {
            $timeOff = TimeOff::with(['employee', 'approver'])->findOrFail($id);
            return JsonResponder::success($response, $timeOff, 'Time off retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Time off not found', 404);
        }
    }

    /**
     * Create new time off request
     */
    public function store(Response $response, array $data): Response
    {
        try {
            // Calculate total days
            if (isset($data['start_date']) && isset($data['end_date'])) {
                $start = \Carbon\Carbon::parse($data['start_date']);
                $end = \Carbon\Carbon::parse($data['end_date']);
                $data['total_days'] = $end->diffInDays($start) + 1;
            }

            // Set default status
            if (!isset($data['status'])) {
                $data['status'] = TimeOff::STATUS_PENDING;
            }

            $timeOff = TimeOff::create($data);
            $timeOff->load(['employee', 'approver']);
            
            return JsonResponder::success($response, $timeOff, 'Time off request created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create time off request: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Update time off
     */
    public function update(Response $response, string $id, array $data): Response
    {
        try {
            $timeOff = TimeOff::findOrFail($id);

            // Recalculate total days if dates changed
            if (isset($data['start_date']) && isset($data['end_date'])) {
                $start = \Carbon\Carbon::parse($data['start_date']);
                $end = \Carbon\Carbon::parse($data['end_date']);
                $data['total_days'] = $end->diffInDays($start) + 1;
            }

            $timeOff->update($data);
            $timeOff->load(['employee', 'approver']);
            
            return JsonResponder::success($response, $timeOff, 'Time off updated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to update time off: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Approve time off
     */
    public function approve(Response $response, string $id, array $data): Response
    {
        try {
            $timeOff = TimeOff::findOrFail($id);
            
            $timeOff->update([
                'status' => TimeOff::STATUS_APPROVED,
                'approved_by' => $data['approved_by'],
                'approved_at' => \Carbon\Carbon::now(),
                'notes' => $data['notes'] ?? null
            ]);

            $timeOff->load(['employee', 'approver']);
            
            return JsonResponder::success($response, $timeOff, 'Time off approved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to approve time off: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Reject time off
     */
    public function reject(Response $response, string $id, array $data): Response
    {
        try {
            $timeOff = TimeOff::findOrFail($id);
            
            $timeOff->update([
                'status' => TimeOff::STATUS_REJECTED,
                'approved_by' => $data['approved_by'],
                'approved_at' => \Carbon\Carbon::now(),
                'notes' => $data['notes'] ?? null
            ]);

            $timeOff->load(['employee', 'approver']);
            
            return JsonResponder::success($response, $timeOff, 'Time off rejected successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to reject time off: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Cancel time off
     */
    public function cancel(Response $response, string $id): Response
    {
        try {
            $timeOff = TimeOff::findOrFail($id);
            
            $timeOff->update(['status' => TimeOff::STATUS_CANCELLED]);
            $timeOff->load(['employee', 'approver']);
            
            return JsonResponder::success($response, $timeOff, 'Time off cancelled successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to cancel time off: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Delete time off
     */
    public function destroy(Response $response, string $id): Response
    {
        try {
            $timeOff = TimeOff::findOrFail($id);
            $timeOff->delete();
            
            return JsonResponder::success($response, null, 'Time off deleted successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete time off: ' . $th->getMessage(), 500);
        }
    }
}
