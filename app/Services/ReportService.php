<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Support\JsonResponder;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;

class ReportService
{
    /**
     * Generate Balance Sheet Report
     * Shows assets, liabilities, and equity at a specific date
     */
    public function getBalanceSheet(Response $response, array $params): Response
    {
        try {
            $asOfDate = $params['as_of_date'] ?? date('Y-m-d');

            // Get all accounts with their balances
            $accounts = ChartOfAccount::where('is_active', true)->get();

            $assets = [];
            $liabilities = [];
            $equity = [];
            
            $totalAssets = 0;
            $totalLiabilities = 0;
            $totalEquity = 0;

            foreach ($accounts as $account) {
                $balance = $this->calculateAccountBalance($account->id, $asOfDate);

                if ($balance == 0 && !isset($params['show_zero_balance'])) {
                    continue; // Skip zero balance accounts unless requested
                }

                $accountData = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => $balance,
                ];

                // Group by account type
                if (in_array($account->type, ['asset', 'Asset', 'ASSET'])) {
                    $assets[] = $accountData;
                    $totalAssets += $balance;
                } elseif (in_array($account->type, ['liability', 'Liability', 'LIABILITY'])) {
                    $liabilities[] = $accountData;
                    $totalLiabilities += $balance;
                } elseif (in_array($account->type, ['equity', 'Equity', 'EQUITY'])) {
                    $equity[] = $accountData;
                    $totalEquity += $balance;
                }
            }

            // Calculate retained earnings (net income from P&L)
            $netIncome = $this->calculateNetIncome($asOfDate);
            $equity[] = [
                'code' => '3900',
                'name' => 'Retained Earnings',
                'balance' => $netIncome,
            ];
            $totalEquity += $netIncome;

            $report = [
                'report_name' => 'Balance Sheet',
                'as_of_date' => $asOfDate,
                'assets' => [
                    'accounts' => $assets,
                    'total' => $totalAssets,
                ],
                'liabilities' => [
                    'accounts' => $liabilities,
                    'total' => $totalLiabilities,
                ],
                'equity' => [
                    'accounts' => $equity,
                    'total' => $totalEquity,
                ],
                'total_liabilities_and_equity' => $totalLiabilities + $totalEquity,
                'balanced' => abs(($totalAssets) - ($totalLiabilities + $totalEquity)) < 0.01,
            ];

            return JsonResponder::success($response, $report, 'Balance sheet generated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to generate balance sheet: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Generate Profit & Loss (Income Statement) Report
     * Shows revenue and expenses for a period
     */
    public function getProfitAndLoss(Response $response, array $params): Response
    {
        try {
            $startDate = $params['start_date'] ?? date('Y-m-01'); // First day of current month
            $endDate = $params['end_date'] ?? date('Y-m-d');

            // Get all revenue and expense accounts
            $accounts = ChartOfAccount::where('is_active', true)
                ->whereIn('type', ['revenue', 'Revenue', 'REVENUE', 'expense', 'Expense', 'EXPENSE'])
                ->get();

            $revenues = [];
            $expenses = [];
            
            $totalRevenue = 0;
            $totalExpense = 0;

            foreach ($accounts as $account) {
                $balance = $this->calculateAccountBalancePeriod($account->id, $startDate, $endDate);

                if ($balance == 0 && !isset($params['show_zero_balance'])) {
                    continue;
                }

                $accountData = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => abs($balance),
                ];

                if (in_array($account->type, ['revenue', 'Revenue', 'REVENUE'])) {
                    $revenues[] = $accountData;
                    $totalRevenue += abs($balance);
                } else {
                    $expenses[] = $accountData;
                    $totalExpense += abs($balance);
                }
            }

            $netIncome = $totalRevenue - $totalExpense;

            $report = [
                'report_name' => 'Profit & Loss Statement',
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'revenues' => [
                    'accounts' => $revenues,
                    'total' => $totalRevenue,
                ],
                'expenses' => [
                    'accounts' => $expenses,
                    'total' => $totalExpense,
                ],
                'net_income' => $netIncome,
            ];

            return JsonResponder::success($response, $report, 'P&L statement generated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to generate P&L: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Generate Cash Book Report
     * Shows all cash/bank transactions
     */
    public function getCashBook(Response $response, array $params): Response
    {
        try {
            $startDate = $params['start_date'] ?? date('Y-m-01');
            $endDate = $params['end_date'] ?? date('Y-m-d');

            // Get cash/bank accounts
            $cashAccounts = ChartOfAccount::where('is_active', true)
                ->where('code', 'like', '111%') // Assuming 111x are cash/bank accounts
                ->get();

            $cashAccountIds = $cashAccounts->pluck('id')->toArray();

            // Get opening balance
            $openingBalance = 0;
            foreach ($cashAccountIds as $accountId) {
                $openingBalance += $this->calculateAccountBalance($accountId, date('Y-m-d', strtotime($startDate . ' -1 day')));
            }

            // Get all transactions for the period
            $transactions = JournalLine::with(['journalEntry', 'chartOfAccount'])
                ->whereIn('chart_of_account_id', $cashAccountIds)
                ->whereHas('journalEntry', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('entry_date', [$startDate, $endDate])
                        ->where('status', 'posted');
                })
                ->orderBy('created_at')
                ->get();

            $entries = [];
            $runningBalance = $openingBalance;
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($transactions as $line) {
                $debit = (float) $line->debit;
                $credit = (float) $line->credit;
                
                $runningBalance += ($debit - $credit);
                $totalDebit += $debit;
                $totalCredit += $credit;

                $entries[] = [
                    'date' => $line->journalEntry->entry_date,
                    'reference' => $line->journalEntry->reference_number,
                    'description' => $line->description,
                    'account' => $line->chartOfAccount->name,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $runningBalance,
                ];
            }

            $report = [
                'report_name' => 'Cash Book',
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'opening_balance' => $openingBalance,
                'transactions' => $entries,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'closing_balance' => $runningBalance,
            ];

            return JsonResponder::success($response, $report, 'Cash book generated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to generate cash book: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Generate Aged Ledger Report (Receivable/Payable Aging)
     * Shows outstanding receivables or payables by age
     */
    public function getAgedLedger(Response $response, array $params): Response
    {
        try {
            $asOfDate = $params['as_of_date'] ?? date('Y-m-d');
            $type = $params['type'] ?? 'receivable'; // 'receivable' or 'payable'

            // Determine which account to use
            if ($type === 'receivable') {
                $account = ChartOfAccount::where('code', '1120')->first(); // A/R
                $partyType = 'customer';
            } else {
                $account = ChartOfAccount::where('code', '2110')->first(); // A/P
                $partyType = 'vendor';
            }

            if (!$account) {
                return JsonResponder::error($response, 'Required account not found', 404);
            }

            // Get all journal lines for this account
            $lines = JournalLine::with(['journalEntry', $partyType])
                ->where('chart_of_account_id', $account->id)
                ->whereHas('journalEntry', function ($query) use ($asOfDate) {
                    $query->where('entry_date', '<=', $asOfDate)
                        ->where('status', 'posted');
                })
                ->where(function ($query) use ($partyType) {
                    $query->whereNotNull($partyType . '_id');
                })
                ->get();

            // Group by customer/vendor
            $grouped = [];
            foreach ($lines as $line) {
                $partyId = $type === 'receivable' ? $line->customer_id : $line->vendor_id;
                $party = $type === 'receivable' ? $line->customer : $line->vendor;

                if (!$partyId || !$party) continue;

                if (!isset($grouped[$partyId])) {
                    $grouped[$partyId] = [
                        'party_id' => $partyId,
                        'party_name' => $party->name ?? 'Unknown',
                        'transactions' => [],
                        'total' => 0,
                        'current' => 0,
                        '1_30_days' => 0,
                        '31_60_days' => 0,
                        '61_90_days' => 0,
                        'over_90_days' => 0,
                    ];
                }

                $amount = ($type === 'receivable') ? 
                    ((float)$line->debit - (float)$line->credit) : 
                    ((float)$line->credit - (float)$line->debit);

                $entryDate = $line->journalEntry->entry_date;
                $daysOld = (strtotime($asOfDate) - strtotime($entryDate)) / 86400;

                $grouped[$partyId]['transactions'][] = [
                    'date' => $entryDate,
                    'reference' => $line->journalEntry->reference_number,
                    'description' => $line->description,
                    'amount' => $amount,
                    'days_old' => (int)$daysOld,
                ];

                $grouped[$partyId]['total'] += $amount;

                // Categorize by age
                if ($daysOld <= 30) {
                    $grouped[$partyId]['current'] += $amount;
                } elseif ($daysOld <= 60) {
                    $grouped[$partyId]['1_30_days'] += $amount;
                } elseif ($daysOld <= 90) {
                    $grouped[$partyId]['31_60_days'] += $amount;
                } elseif ($daysOld <= 120) {
                    $grouped[$partyId]['61_90_days'] += $amount;
                } else {
                    $grouped[$partyId]['over_90_days'] += $amount;
                }
            }

            // Calculate totals
            $summary = [
                'total' => 0,
                'current' => 0,
                '1_30_days' => 0,
                '31_60_days' => 0,
                '61_90_days' => 0,
                'over_90_days' => 0,
            ];

            foreach ($grouped as &$item) {
                $summary['total'] += $item['total'];
                $summary['current'] += $item['current'];
                $summary['1_30_days'] += $item['1_30_days'];
                $summary['31_60_days'] += $item['31_60_days'];
                $summary['61_90_days'] += $item['61_90_days'];
                $summary['over_90_days'] += $item['over_90_days'];
            }

            $report = [
                'report_name' => 'Aged ' . ucfirst($type) . ' Ledger',
                'as_of_date' => $asOfDate,
                'type' => $type,
                'details' => array_values($grouped),
                'summary' => $summary,
            ];

            return JsonResponder::success($response, $report, 'Aged ledger generated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to generate aged ledger: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Helper: Calculate account balance as of a date
     */
    private function calculateAccountBalance(string $accountId, string $asOfDate): float
    {
        $lines = JournalLine::where('chart_of_account_id', $accountId)
            ->whereHas('journalEntry', function ($query) use ($asOfDate) {
                $query->where('entry_date', '<=', $asOfDate)
                    ->where('status', 'posted');
            })
            ->get();

        $balance = 0;
        foreach ($lines as $line) {
            $balance += ((float)$line->debit - (float)$line->credit);
        }

        return $balance;
    }

    /**
     * Helper: Calculate account balance for a period
     */
    private function calculateAccountBalancePeriod(string $accountId, string $startDate, string $endDate): float
    {
        $lines = JournalLine::where('chart_of_account_id', $accountId)
            ->whereHas('journalEntry', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('entry_date', [$startDate, $endDate])
                    ->where('status', 'posted');
            })
            ->get();

        $balance = 0;
        foreach ($lines as $line) {
            $balance += ((float)$line->debit - (float)$line->credit);
        }

        return $balance;
    }

    /**
     * Helper: Calculate net income (for retained earnings)
     */
    private function calculateNetIncome(string $asOfDate): float
    {
        // Get all revenue and expense accounts
        $accounts = ChartOfAccount::whereIn('type', ['revenue', 'Revenue', 'REVENUE', 'expense', 'Expense', 'EXPENSE'])->get();

        $netIncome = 0;
        foreach ($accounts as $account) {
            $balance = $this->calculateAccountBalancePeriod($account->id, '2000-01-01', $asOfDate);
            
            // Revenue increases credit, expense increases debit
            if (in_array($account->type, ['revenue', 'Revenue', 'REVENUE'])) {
                $netIncome -= $balance; // Revenue has negative balance (credit)
            } else {
                $netIncome -= $balance; // Expense has positive balance (debit)
            }
        }

        return $netIncome;
    }

    /**
     * Generate Trial Balance Report
     * Shows all accounts with their debit and credit balances
     */
    public function getTrialBalance(Response $response, array $params): Response
    {
        try {
            $asOfDate = $params['as_of_date'] ?? date('Y-m-d');

            $accounts = ChartOfAccount::where('is_active', true)
                ->orderBy('code')
                ->get();

            $entries = [];
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($accounts as $account) {
                $balance = $this->calculateAccountBalance($account->id, $asOfDate);

                if ($balance == 0 && !isset($params['show_zero_balance'])) {
                    continue;
                }

                // Determine if balance is debit or credit based on normal balance
                $debit = 0;
                $credit = 0;

                if ($account->normal_balance === 'debit') {
                    $debit = $balance;
                } else {
                    $credit = abs($balance);
                }

                $entries[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => $debit,
                    'credit' => $credit,
                ];

                $totalDebit += $debit;
                $totalCredit += $credit;
            }

            $report = [
                'report_name' => 'Trial Balance',
                'as_of_date' => $asOfDate,
                'accounts' => $entries,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'balanced' => abs($totalDebit - $totalCredit) < 0.01,
            ];

            return JsonResponder::success($response, $report, 'Trial balance generated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to generate trial balance: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Generate General Ledger Report for a specific account
     */
    public function getGeneralLedger(Response $response, array $params): Response
    {
        try {
            $accountId = $params['account_id'];
            $startDate = $params['start_date'] ?? date('Y-m-01');
            $endDate = $params['end_date'] ?? date('Y-m-d');

            $account = ChartOfAccount::find($accountId);
            if (!$account) {
                return JsonResponder::error($response, 'Account not found', 404);
            }

            // Get opening balance
            $openingBalance = $this->calculateAccountBalance($accountId, date('Y-m-d', strtotime($startDate . ' -1 day')));

            // Get transactions
            $lines = JournalLine::with(['journalEntry'])
                ->where('chart_of_account_id', $accountId)
                ->whereHas('journalEntry', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('entry_date', [$startDate, $endDate])
                        ->where('status', 'posted');
                })
                ->orderBy('created_at')
                ->get();

            $transactions = [];
            $runningBalance = $openingBalance;
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($lines as $line) {
                $debit = (float)$line->debit;
                $credit = (float)$line->credit;
                
                $runningBalance += ($debit - $credit);
                $totalDebit += $debit;
                $totalCredit += $credit;

                $transactions[] = [
                    'date' => $line->journalEntry->entry_date,
                    'reference' => $line->journalEntry->reference_number,
                    'description' => $line->description,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $runningBalance,
                ];
            }

            $report = [
                'report_name' => 'General Ledger',
                'account' => [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                ],
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'opening_balance' => $openingBalance,
                'transactions' => $transactions,
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'closing_balance' => $runningBalance,
            ];

            return JsonResponder::success($response, $report, 'General ledger generated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to generate general ledger: ' . $th->getMessage(), 500);
        }
    }
}
