<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\BiayaWorkorder;
use App\Support\JsonResponder;
use App\Utils\Upload;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

class ExpenseService
{
    /**
     * Get list of all expenses
     */
    public function listExpenses(Response $response)
    {
        try {
            $expenses = Expense::all();
            return JsonResponder::success($response, $expenses, 'List of expenses');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    /**
     * Get single expense by ID
     */
    public function getExpense(Response $response, string $id)
    {
        try {
            $expense = Expense::find($id);
            if (!$expense) {
                return JsonResponder::error($response, 'Expense not found', 404);
            }
            return JsonResponder::success($response, $expense, 'Expense detail');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    /**
     * Get list of biaya workorder (Work Order Costs)
     */
    public function listBiayaWorkorder(Response $response)
    {
        try {
            $biaya = BiayaWorkorder::with(['workorder', 'product'])->get();
            return JsonResponder::success($response, $biaya, 'List of work order costs');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    /**
     * Get single biaya workorder by ID
     */
    public function getBiayaWorkorder(Response $response, string $id)
    {
        try {
            $biaya = BiayaWorkorder::with(['workorder', 'product'])->find($id);
            if (!$biaya) {
                return JsonResponder::error($response, 'Biaya Workorder not found', 404);
            }
            return JsonResponder::success($response, $biaya, 'Biaya Workorder detail');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    /**
     * Create new expense
     */
    public function createExpense(Response $response, array $data, ?UploadedFileInterface $file = null)
    {
        try {
            $errors = [];
            
            if (empty($data['nomor'])) {
                $errors[] = 'Nomor (number) is required';
            }
            if (empty($data['tanggal'])) {
                $errors[] = 'Tanggal (date) is required';
            }
            if (empty($data['jumlah'])) {
                $errors[] = 'Jumlah (amount) is required';
            }

            if (!empty($errors)) {
                return JsonResponder::badRequest($response, $errors);
            }

            // Handle file upload for bukti (optional)
            if ($file) {
                if ($file->getError() === UPLOAD_ERR_OK) {
                    try {
                        $data['bukti'] = Upload::storeImage($file, 'expenses');
                    } catch (\Throwable $e) {
                        return JsonResponder::error($response, 'Upload gagal: ' . $e->getMessage(), 400);
                    }
                } elseif ($file->getError() !== UPLOAD_ERR_NO_FILE) {
                    return JsonResponder::error($response, 'Upload error code: ' . $file->getError(), 400);
                }
            }

            $expense = new Expense($data);
            $expense->id = $expense->id ?? (string) Str::uuid();
            $expense->save();

            return JsonResponder::success($response, $expense, 'Expense created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    /**
     * Update existing expense
     */
    public function updateExpense(Response $response, string $id, array $data, ?UploadedFileInterface $file = null)
    {
        try {
            $expense = Expense::find($id);
            if (!$expense) {
                return JsonResponder::error($response, 'Expense not found', 404);
            }

            // Handle file upload (replace existing bukti if new file provided)
            if ($file) {
                if ($file->getError() === UPLOAD_ERR_OK) {
                    try {
                        $newPath = Upload::storeImage($file, 'expenses');
                        if (!empty($expense->bukti)) {
                            Upload::deleteImage($expense->bukti);
                        }
                        $data['bukti'] = $newPath;
                    } catch (\Throwable $e) {
                        return JsonResponder::error($response, 'Upload gagal: ' . $e->getMessage(), 400);
                    }
                } elseif ($file->getError() !== UPLOAD_ERR_NO_FILE) {
                    return JsonResponder::error($response, 'Upload error code: ' . $file->getError(), 400);
                }
            }

            $expense->update($data);
            return JsonResponder::success($response, $expense, 'Expense updated successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }

    /**
     * Delete expense
     */
    public function deleteExpense(Response $response, string $id)
    {
        try {
            $expense = Expense::find($id);
            if (!$expense) {
                return JsonResponder::error($response, 'Expense not found', 404);
            }

            $expense->delete();
            return JsonResponder::success($response, null, 'Expense deleted successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th->getMessage(), 500);
        }
    }
}
