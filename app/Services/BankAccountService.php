<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;

class BankAccountService
{
    /**
     * List bank/cash accounts for payment selection
     */
    public function list(Response $response, array $filters = []): Response
    {
        try {
            $query = ChartOfAccount::query();

            // Default filter: type=asset, category=current_asset, and name contains KAS/BANK
            $query->where('type', 'asset')
                ->where('category', 'current_asset')
                ->where(function ($q) {
                    $q->where('name', 'like', '%KAS%')
                      ->orWhere('name', 'like', '%BANK%');
                });

            // Optional overrides
            if (isset($filters['is_active'])) {
                $query->where('is_active', (bool) $filters['is_active']);
            }

            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            }

            $accounts = $query->orderBy('code')->get();

            return JsonResponder::success($response, $accounts, 'Bank accounts retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve bank accounts: ' . $th->getMessage(), 500);
        }
    }
}
