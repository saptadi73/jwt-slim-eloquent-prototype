<?php

namespace App\Services;

use App\Models\Departemen;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Ramsey\Uuid\Uuid;

class DepartmentService
{
    /**
     * Get all departments with employee count
     */
    public function getDepartmentsWithCount(Response $response): Response
    {
        try {
            $departments = Departemen::withCount('pegawai')
                ->orderBy('nama')
                ->get()
                ->map(function ($dept) {
                    return [
                        'id' => $dept->id,
                        'nama' => $dept->nama,
                        'jumlah_karyawan' => $dept->pegawai_count,
                    ];
                });

            return JsonResponder::success($response, $departments, 'Departments with employee count retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve departments: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get all departments
     */
    public function index(Response $response, array $params): Response
    {
        try {
            $query = Departemen::query();

            // Filter by active status (if applicable)
            if (isset($params['is_active'])) {
                $query->where('is_active', $params['is_active']);
            }

            // Include pegawai count
            if (isset($params['with_employee_count'])) {
                $query->withCount('pegawai');
            }

            $departments = $query->orderBy('nama')->get();

            return JsonResponder::success($response, $departments, 'Departments retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve departments: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get department by ID
     */
    public function show(Response $response, string $id): Response
    {
        try {
            $department = Departemen::with('pegawai')->findOrFail($id);
            return JsonResponder::success($response, $department, 'Department retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Department not found', 404);
        }
    }

    /**
     * Create new department
     */
    public function store(Response $response, array $data): Response
    {
        try {
            // Generate UUID if not provided
            if (!isset($data['id'])) {
                $data['id'] = Uuid::uuid4()->toString();
            }
            
            $department = Departemen::create($data);
            return JsonResponder::success($response, $department, 'Department created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create department: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Update department
     */
    public function update(Response $response, string $id, array $data): Response
    {
        try {
            $department = Departemen::findOrFail($id);
            $department->update($data);
            
            return JsonResponder::success($response, $department, 'Department updated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to update department: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Delete department
     */
    public function destroy(Response $response, string $id): Response
    {
        try {
            $department = Departemen::findOrFail($id);
            
            // Check if department has pegawai
            if ($department->pegawai()->count() > 0) {
                return JsonResponder::error($response, 'Cannot delete department with employees', 400);
            }
            
            $department->delete();
            
            return JsonResponder::success($response, null, 'Department deleted successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete department: ' . $th->getMessage(), 500);
        }
    }
}
