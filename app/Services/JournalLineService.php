<?php

namespace App\Services;

use App\Models\JournalLine;
use App\Support\JsonResponder;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;

class JournalLineService
{
    /**
     * Get all journal lines
     */
    public function getAll(Response $response): Response
    {
        try {
            $lines = JournalLine::with(['journalEntry', 'chartOfAccount'])->get();
            return JsonResponder::success($response, $lines, 'Journal lines retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve journal lines: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get journal line by ID
     */
    public function getById(Response $response, string $id): Response
    {
        try {
            $line = JournalLine::with(['journalEntry', 'chartOfAccount'])->find($id);
            if (!$line) {
                return JsonResponder::error($response, 'Journal line not found', 404);
            }
            return JsonResponder::success($response, $line, 'Journal line retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve journal line: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create new journal line
     */
    public function create(Response $response, array $data): Response
    {
        try {
            $line = DB::connection()->transaction(function () use ($data) {
                return JournalLine::create($data);
            });
            return JsonResponder::success($response, $line, 'Journal line created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create journal line: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Update journal line
     */
    public function update(Response $response, string $id, array $data): Response
    {
        try {
            $line = JournalLine::find($id);
            if (!$line) {
                return JsonResponder::error($response, 'Journal line not found', 404);
            }

            DB::connection()->transaction(function () use ($line, $data) {
                $line->update($data);
            });

            $line = $line->fresh();
            return JsonResponder::success($response, $line, 'Journal line updated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to update journal line: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Delete journal line
     */
    public function delete(Response $response, string $id): Response
    {
        try {
            $line = JournalLine::find($id);
            if (!$line) {
                return JsonResponder::error($response, 'Journal line not found', 404);
            }

            $line->delete();
            return JsonResponder::success($response, null, 'Journal line deleted successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete journal line: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get journal lines by journal entry ID
     */
    public function getByJournalEntryId(Response $response, string $journalEntryId): Response
    {
        try {
            $lines = JournalLine::where('journal_entry_id', $journalEntryId)->with('chartOfAccount')->get();
            return JsonResponder::success($response, $lines, 'Journal lines retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve journal lines: ' . $th->getMessage(), 500);
        }
    }
}
