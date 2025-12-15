<?php

namespace App\Services;

use App\Models\Service;
use App\Support\JsonResponder;
use Illuminate\Support\Str;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;
use InvalidArgumentException;

class ServiceService
{
    private function validate(array $data): void
    {
        $errors = [];
        // nama: required, string length 1-191
        if (!isset($data['nama']) || !is_string($data['nama']) || trim($data['nama']) === '') {
            $errors[] = 'Field nama wajib diisi';
        } elseif (mb_strlen($data['nama']) > 191) {
            $errors[] = 'Field nama maksimal 191 karakter';
        }

        // harga: required for create, numeric >= 0
        if (!array_key_exists('harga', $data)) {
            $errors[] = 'Field harga wajib diisi';
        } elseif (!is_numeric($data['harga'])) {
            $errors[] = 'Field harga harus numerik';
        } elseif ((float)$data['harga'] < 0) {
            $errors[] = 'Field harga tidak boleh negatif';
        }

        // deskripsi: optional string
        if (isset($data['deskripsi']) && !is_string($data['deskripsi'])) {
            $errors[] = 'Field deskripsi harus string';
        }

        if (!empty($errors)) {
            throw new InvalidArgumentException(implode('; ', $errors));
        }
    }
    public function list(Response $response)
    {
        try {
            $items = Service::with(['kategori'])->get();
            return JsonResponder::success($response, $items);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th);
        }
    }

    public function get(Response $response, string $id)
    {
        try {
            $item = Service::with(['kategori'])->findOrFail($id);
            return JsonResponder::success($response, $item);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, $th);
        }
    }

    public function create(Response $response, array $data)
    {
        DB::beginTransaction();
        try {
            $this->validate($data);
            $service = new Service($data);
            // Explicit UUID assignment for reliability
            $service->id = (string) Str::uuid();
            $service->save();
            DB::commit();
            return JsonResponder::success($response, $service);
        } catch (\Throwable $th) {
            DB::rollBack();
                // If validation failed, return 400 via JsonResponder
                if ($th instanceof InvalidArgumentException) {
                    return JsonResponder::badRequest($response, $th->getMessage());
                }
                // Unknown errors → 500 via JsonResponder
                return JsonResponder::error($response, $th, 500);
        }
    }

    public function update(Response $response, string $id, array $data)
    {
        DB::beginTransaction();
        try {
            $service = Service::findOrFail($id);
            // For update: if harga provided, validate; allow partial updates
            if (array_key_exists('harga', $data) || array_key_exists('nama', $data) || array_key_exists('deskripsi', $data)) {
                // Build a minimal array for validation with defaults from existing
                $candidate = [
                    'nama'      => $data['nama']      ?? $service->nama,
                    'harga'     => $data['harga']     ?? $service->harga,
                    'deskripsi' => $data['deskripsi'] ?? $service->deskripsi,
                ];
                $this->validate($candidate);
            }
            $service->fill($data);
            $service->save();
            DB::commit();
            return JsonResponder::success($response, $service);
        } catch (\Throwable $th) {
            DB::rollBack();
                // If validation failed, return 400 via JsonResponder
                if ($th instanceof InvalidArgumentException) {
                    return JsonResponder::badRequest($response, $th->getMessage());
                }
                // Unknown errors → 500 via JsonResponder
                return JsonResponder::error($response, $th, 500);
        }
    }

    public function delete(Response $response, string $id)
    {
        DB::beginTransaction();
        try {
            $service = Service::findOrFail($id);
            $service->delete();
            DB::commit();
            return JsonResponder::success($response, ['message' => 'Service deleted successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return JsonResponder::error($response, $th);
        }
    }
}
