<?php

namespace App\Services;

use App\Models\Position;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;

class PositionService
{
    /**
     * Get all positions
     */
    public function index(Response $response, array $params): Response
    {
        try {
            $query = Position::query();

            // Filter by active status
            if (isset($params['is_active'])) {
                $query->where('is_active', $params['is_active']);
            }

            // Include employee count
            if (isset($params['with_employee_count'])) {
                $query->withCount('employees');
            }

            $positions = $query->orderBy('name')->get();

            return JsonResponder::success($response, $positions, 'Positions retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve positions: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get position by ID
     */
    public function show(Response $response, string $id): Response
    {
        try {
            $position = Position::with('employees')->findOrFail($id);
            return JsonResponder::success($response, $position, 'Position retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Position not found', 404);
        }
    }

    /**
     * Create new position
     */
    public function store(Response $response, array $data): Response
    {
        try {
            $position = Position::create($data);
            return JsonResponder::success($response, $position, 'Position created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create position: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Update position
     */
    public function update(Response $response, string $id, array $data): Response
    {
        try {
            $position = Position::findOrFail($id);
            $position->update($data);
            
            return JsonResponder::success($response, $position, 'Position updated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to update position: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Delete position
     */
    public function destroy(Response $response, string $id): Response
    {
        try {
            $position = Position::findOrFail($id);
            
            // Check if position has employees
            if ($position->employees()->count() > 0) {
                return JsonResponder::error($response, 'Cannot delete position with employees', 400);
            }
            
            $position->delete();
            
            return JsonResponder::success($response, null, 'Position deleted successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete position: ' . $th->getMessage(), 500);
        }
    }
}
