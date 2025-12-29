<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Support\JsonResponder;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;

class ChartOfAccountService
{
    /**
     * Get all chart of accounts
     */
    public function getAll(Response $response): Response
    {
        try {
            $coas = ChartOfAccount::all()->toArray();
            return JsonResponder::success($response, $coas, 'Chart of accounts retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve chart of accounts: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get chart of account by ID
     */
    public function getById(Response $response, string $id): Response
    {
        try {
            $coa = ChartOfAccount::find($id);
            if (!$coa) {
                return JsonResponder::error($response, 'Chart of account not found', 404);
            }
            return JsonResponder::success($response, $coa->toArray(), 'Chart of account retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve chart of account: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get all expense-type chart of accounts
     */
    public function getExpenses(Response $response): Response
    {
        try {
            $coas = ChartOfAccount::where('type', 'expense')->get()->toArray();
            return JsonResponder::success($response, $coas, 'Expense chart of accounts retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve expense chart of accounts: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get all liability-type chart of accounts
     */
    public function getLiabilities(Response $response): Response
    {
        try {
            $coas = ChartOfAccount::where('type', 'liability')->get()->toArray();
            return JsonResponder::success($response, $coas, 'Liability chart of accounts retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve liability chart of accounts: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create new chart of account
     */
    public function create(Response $response, array $data): Response
    {
        try {
            $coa = DB::connection()->transaction(function () use ($data) {
                return ChartOfAccount::create($data);
            });
            return JsonResponder::success($response, $coa->toArray(), 'Chart of account created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create chart of account: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Update chart of account
     */
    public function update(Response $response, string $id, array $data): Response
    {
        try {
            $coa = ChartOfAccount::find($id);
            if (!$coa) {
                return JsonResponder::error($response, 'Chart of account not found', 404);
            }

            DB::connection()->transaction(function () use ($coa, $data) {
                $coa->update($data);
            });

            $coa = $coa->fresh();
            return JsonResponder::success($response, $coa->toArray(), 'Chart of account updated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to update chart of account: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Delete chart of account
     */
    public function delete(Response $response, string $id): Response
    {
        try {
            $coa = ChartOfAccount::find($id);
            if (!$coa) {
                return JsonResponder::error($response, 'Chart of account not found', 404);
            }

            $coa->delete();
            return JsonResponder::success($response, null, 'Chart of account deleted successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete chart of account: ' . $th->getMessage(), 500);
        }
    }
}
