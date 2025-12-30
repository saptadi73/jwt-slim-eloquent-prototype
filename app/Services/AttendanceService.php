<?php

namespace App\Services;

use App\Models\Attendance;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;

class AttendanceService
{
    /**
     * Get all attendances
     */
    public function index(Response $response, array $params): Response
    {
        try {
            $query = Attendance::with(['employee']);

            // Filter by employee
            if (isset($params['employee_id'])) {
                $query->where('employee_id', $params['employee_id']);
            }

            // Filter by date range
            if (isset($params['start_date'])) {
                $query->where('date', '>=', $params['start_date']);
            }
            if (isset($params['end_date'])) {
                $query->where('date', '<=', $params['end_date']);
            }

            // Filter by status
            if (isset($params['status'])) {
                $query->where('status', $params['status']);
            }

            // Filter by specific date
            if (isset($params['date'])) {
                $query->whereDate('date', $params['date']);
            }

            $attendances = $query->orderBy('date', 'desc')->orderBy('check_in', 'desc')->get();

            return JsonResponder::success($response, $attendances, 'Attendances retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve attendances: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get attendance by ID
     */
    public function show(Response $response, string $id): Response
    {
        try {
            $attendance = Attendance::with(['employee'])->findOrFail($id);
            return JsonResponder::success($response, $attendance, 'Attendance retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Attendance not found', 404);
        }
    }

    /**
     * Check in
     */
    public function checkIn(Response $response, array $data): Response
    {
        try {
            // Check if already checked in today
            $today = date('Y-m-d');
            $existing = Attendance::where('employee_id', $data['employee_id'])
                ->whereDate('date', $today)
                ->first();

            if ($existing && $existing->check_in) {
                return JsonResponder::error($response, 'Already checked in today', 400);
            }

            // Handle check-in photo upload if present
            if (isset($data['photo_file'])) {
                $data['check_in_photo'] = $this->handlePhotoUpload($data['photo_file'], 'checkin');
                unset($data['photo_file']);
            }

            $data['date'] = $today;
            $data['check_in'] = \Carbon\Carbon::now();
            $data['status'] = Attendance::STATUS_PRESENT;

            $attendance = Attendance::create($data);
            $attendance->load(['employee']);
            
            return JsonResponder::success($response, $attendance, 'Check in successful', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to check in: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Check out
     */
    public function checkOut(Response $response, string $id, array $data): Response
    {
        try {
            $attendance = Attendance::findOrFail($id);

            if ($attendance->check_out) {
                return JsonResponder::error($response, 'Already checked out', 400);
            }

            // Handle check-out photo upload if present
            if (isset($data['photo_file'])) {
                $data['check_out_photo'] = $this->handlePhotoUpload($data['photo_file'], 'checkout');
                unset($data['photo_file']);
            }

            $data['check_out'] = \Carbon\Carbon::now();
            
            $attendance->update($data);
            $attendance->calculateWorkHours();
            $attendance->save();
            
            $attendance->load(['employee']);
            
            return JsonResponder::success($response, $attendance, 'Check out successful');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to check out: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create manual attendance record
     */
    public function store(Response $response, array $data): Response
    {
        try {
            $attendance = Attendance::create($data);
            
            if (isset($data['check_in']) && isset($data['check_out'])) {
                $attendance->calculateWorkHours();
                $attendance->save();
            }
            
            $attendance->load(['employee']);
            
            return JsonResponder::success($response, $attendance, 'Attendance created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create attendance: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Update attendance
     */
    public function update(Response $response, string $id, array $data): Response
    {
        try {
            $attendance = Attendance::findOrFail($id);
            $attendance->update($data);
            
            if (isset($data['check_in']) && isset($data['check_out'])) {
                $attendance->calculateWorkHours();
                $attendance->save();
            }
            
            $attendance->load(['employee']);
            
            return JsonResponder::success($response, $attendance, 'Attendance updated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to update attendance: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Delete attendance
     */
    public function destroy(Response $response, string $id): Response
    {
        try {
            $attendance = Attendance::findOrFail($id);
            
            // Delete photos if exist
            if ($attendance->check_in_photo) {
                $this->deletePhoto($attendance->check_in_photo);
            }
            if ($attendance->check_out_photo) {
                $this->deletePhoto($attendance->check_out_photo);
            }
            
            $attendance->delete();
            
            return JsonResponder::success($response, null, 'Attendance deleted successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete attendance: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get attendance summary for employee
     */
    public function summary(Response $response, array $params): Response
    {
        try {
            $employeeId = $params['employee_id'];
            $month = $params['month'] ?? date('m');
            $year = $params['year'] ?? date('Y');

            $attendances = Attendance::where('employee_id', $employeeId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            $summary = [
                'total_days' => $attendances->count(),
                'present' => $attendances->where('status', Attendance::STATUS_PRESENT)->count(),
                'absent' => $attendances->where('status', Attendance::STATUS_ABSENT)->count(),
                'late' => $attendances->where('status', Attendance::STATUS_LATE)->count(),
                'half_day' => $attendances->where('status', Attendance::STATUS_HALF_DAY)->count(),
                'on_leave' => $attendances->where('status', Attendance::STATUS_ON_LEAVE)->count(),
                'sick' => $attendances->where('status', Attendance::STATUS_SICK)->count(),
                'total_work_hours' => $attendances->sum('work_hours'),
                'total_overtime_hours' => $attendances->sum('overtime_hours'),
            ];

            return JsonResponder::success($response, $summary, 'Attendance summary retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to get attendance summary: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Handle photo upload
     */
    private function handlePhotoUpload($file, string $type): string
    {
        $uploadDir = __DIR__ . '/../../public/uploads/attendance/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = uniqid($type . '_') . '_' . $file->getClientFilename();
        $filepath = $uploadDir . $filename;
        
        $file->moveTo($filepath);
        
        return '/uploads/attendance/' . $filename;
    }

    /**
     * Delete photo file
     */
    private function deletePhoto(string $photoPath): void
    {
        $filepath = __DIR__ . '/../../public' . $photoPath;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}
