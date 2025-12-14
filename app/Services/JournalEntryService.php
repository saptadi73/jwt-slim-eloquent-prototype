<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Support\JsonResponder;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;

class JournalEntryService
{
    /**
     * Get all journal entries
     */
    public function getAll(Response $response): Response
    {
        try {
            $entries = JournalEntry::with('lines')->get();
            return JsonResponder::success($response, $entries, 'Journal entries retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve journal entries: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get journal entry by ID
     */
    public function getById(Response $response, string $id): Response
    {
        try {
            $entry = JournalEntry::with('lines')->find($id);
            if (!$entry) {
                return JsonResponder::error($response, 'Journal entry not found', 404);
            }
            return JsonResponder::success($response, $entry, 'Journal entry retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve journal entry: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create new journal entry
     */
    public function create(Response $response, array $data): Response
    {
        try {
            $entry = DB::connection()->transaction(function () use ($data) {
                return JournalEntry::create($data);
            });
            return JsonResponder::success($response, $entry, 'Journal entry created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create journal entry: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Update journal entry
     */
    public function update(Response $response, string $id, array $data): Response
    {
        try {
            $entry = JournalEntry::find($id);
            if (!$entry) {
                return JsonResponder::error($response, 'Journal entry not found', 404);
            }

            DB::connection()->transaction(function () use ($entry, $data) {
                $entry->update($data);
            });

            $entry = $entry->fresh();
            return JsonResponder::success($response, $entry, 'Journal entry updated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to update journal entry: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Delete journal entry
     */
    public function delete(Response $response, string $id): Response
    {
        try {
            $entry = JournalEntry::find($id);
            if (!$entry) {
                return JsonResponder::error($response, 'Journal entry not found', 404);
            }

            $entry->delete();
            return JsonResponder::success($response, null, 'Journal entry deleted successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete journal entry: ' . $th->getMessage(), 500);
        }
    }
}
