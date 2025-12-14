<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Support\JsonResponder;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;

class JournalService
{
    /**
     * Create Sales Journal
     * Debit: Accounts Receivable
     * Credit: Sales Revenue
     */
    public function createSalesJournal(Response $response, array $data): Response
    {
        try {
            $journal = DB::connection()->transaction(function () use ($data) {
                $entry = JournalEntry::create([
                    'date' => $data['date'],
                    'description' => $data['description'] ?? 'Sales Journal',
                    'reference' => $data['reference'] ?? null,
                ]);

                // Debit Accounts Receivable
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['receivable_account_id'],
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'description' => $data['description'] ?? 'Accounts Receivable',
                ]);

                // Credit Sales Revenue
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['revenue_account_id'],
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'description' => $data['description'] ?? 'Sales Revenue',
                ]);

                return $entry->load('lines');
            });

            return JsonResponder::success($response, $journal->toArray(), 'Sales journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create sales journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create Sales Payment Journal
     * Debit: Cash/Bank
     * Credit: Accounts Receivable
     */
    public function createSalesPaymentJournal(Response $response, array $data): Response
    {
        try {
            $journal = DB::connection()->transaction(function () use ($data) {
                $entry = JournalEntry::create([
                    'date' => $data['date'],
                    'description' => $data['description'] ?? 'Sales Payment Journal',
                    'reference' => $data['reference'] ?? null,
                ]);

                // Debit Cash/Bank
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['cash_account_id'],
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'description' => $data['description'] ?? 'Cash/Bank',
                ]);

                // Credit Accounts Receivable
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['receivable_account_id'],
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'description' => $data['description'] ?? 'Accounts Receivable',
                ]);

                return $entry->load('lines');
            });

            return JsonResponder::success($response, $journal->toArray(), 'Sales payment journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create sales payment journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create Purchase Journal
     * Debit: Inventory/Purchase Expense
     * Credit: Accounts Payable
     */
    public function createPurchaseJournal(Response $response, array $data): Response
    {
        try {
            $journal = DB::connection()->transaction(function () use ($data) {
                $entry = JournalEntry::create([
                    'date' => $data['date'],
                    'description' => $data['description'] ?? 'Purchase Journal',
                    'reference' => $data['reference'] ?? null,
                ]);

                // Debit Inventory or Expense
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['inventory_account_id'],
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'description' => $data['description'] ?? 'Inventory/Expense',
                ]);

                // Credit Accounts Payable
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['payable_account_id'],
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'description' => $data['description'] ?? 'Accounts Payable',
                ]);

                return $entry->load('lines');
            });

            return JsonResponder::success($response, $journal->toArray(), 'Purchase journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create purchase journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create Purchase Payment Journal
     * Debit: Accounts Payable
     * Credit: Cash/Bank
     */
    public function createPurchasePaymentJournal(Response $response, array $data): Response
    {
        try {
            $journal = DB::connection()->transaction(function () use ($data) {
                $entry = JournalEntry::create([
                    'date' => $data['date'],
                    'description' => $data['description'] ?? 'Purchase Payment Journal',
                    'reference' => $data['reference'] ?? null,
                ]);

                // Debit Accounts Payable
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['payable_account_id'],
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'description' => $data['description'] ?? 'Accounts Payable',
                ]);

                // Credit Cash/Bank
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['cash_account_id'],
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'description' => $data['description'] ?? 'Cash/Bank',
                ]);

                return $entry->load('lines');
            });

            return JsonResponder::success($response, $journal->toArray(), 'Purchase payment journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create purchase payment journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create Expense Journal
     * Debit: Expense Account
     * Credit: Cash/Bank or Accounts Payable
     */
    public function createExpenseJournal(Response $response, array $data): Response
    {
        try {
            $journal = DB::connection()->transaction(function () use ($data) {
                $entry = JournalEntry::create([
                    'date' => $data['date'],
                    'description' => $data['description'] ?? 'Expense Journal',
                    'reference' => $data['reference'] ?? null,
                ]);

                // Debit Expense
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['expense_account_id'],
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'description' => $data['description'] ?? 'Expense',
                ]);

                // Credit Cash/Bank or Payable
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['credit_account_id'],
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'description' => $data['description'] ?? 'Cash/Bank or Payable',
                ]);

                return $entry->load('lines');
            });

            return JsonResponder::success($response, $journal->toArray(), 'Expense journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create expense journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create Expense Payment Journal
     * Debit: Expense Account (if not paid yet) or Accounts Payable
     * Credit: Cash/Bank
     */
    public function createExpensePaymentJournal(Response $response, array $data): Response
    {
        try {
            $journal = DB::connection()->transaction(function () use ($data) {
                $entry = JournalEntry::create([
                    'date' => $data['date'],
                    'description' => $data['description'] ?? 'Expense Payment Journal',
                    'reference' => $data['reference'] ?? null,
                ]);

                // Debit Accounts Payable or Expense
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['debit_account_id'],
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'description' => $data['description'] ?? 'Accounts Payable or Expense',
                ]);

                // Credit Cash/Bank
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['cash_account_id'],
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'description' => $data['description'] ?? 'Cash/Bank',
                ]);

                return $entry->load('lines');
            });

            return JsonResponder::success($response, $journal->toArray(), 'Expense payment journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create expense payment journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create Miscellaneous Journal
     * Custom debit and credit accounts
     */
    public function createMiscellaneousJournal(Response $response, array $data): Response
    {
        try {
            $journal = DB::connection()->transaction(function () use ($data) {
                $entry = JournalEntry::create([
                    'date' => $data['date'],
                    'description' => $data['description'] ?? 'Miscellaneous Journal',
                    'reference' => $data['reference'] ?? null,
                ]);

                // Debit line
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['debit_account_id'],
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'description' => $data['debit_description'] ?? 'Debit',
                ]);

                // Credit line
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $data['credit_account_id'],
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'description' => $data['credit_description'] ?? 'Credit',
                ]);

                return $entry->load('lines');
            });

            return JsonResponder::success($response, $journal->toArray(), 'Miscellaneous journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create miscellaneous journal: ' . $th->getMessage(), 500);
        }
    }
}
