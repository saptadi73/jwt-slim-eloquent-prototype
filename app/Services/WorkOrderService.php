<?php

namespace App\Services;

use App\Models\Workorder;
use App\Models\WorkOrderAcService;
use App\Models\WorkOrderPenyewaan;
use Illuminate\Support\Str;
use Psr\Http\Message\UploadedFileInterface as File;
use App\Utils\Upload;
use App\Models\Pegawai;
use App\Models\Customer;
use App\Models\CustomerAsset;
use App\Models\WorkorderPenjualan;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Checklist;
use App\Models\ChecklistTemplate;
use App\Models\JenisWorkorder;
use App\Models\Brand;
use App\Models\RentalAsset;
use App\Models\Tipe;

class WorkOrderService
{
    private function nextWoCode(): string
    {
        $prefix = 'WO-';
        // Ambil angka terbesar dari nowo yang sudah ada
        $max = Workorder::where('nowo', 'like', $prefix . '%')
            ->selectRaw("MAX(CAST(SUBSTRING(nowo, LENGTH(?) + 1) AS INTEGER)) as max_code", [$prefix])
            ->value('max_code');

        $next = ((int)$max) + 1;
        return $prefix . str_pad((string)$next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate random 11 digit alphanumeric string
     */
    private function random11(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $result = '';
        for ($i = 0; $i < 11; $i++) {
            $result .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $result;
    }

    public function createWorkorderPemeliharaan(Response $response, array $data): Response
    {
        $nowo = $this->nextWoCode();
        $workorder = Workorder::create([
            'id' => Str::uuid(),
            'nowo' => $nowo,
            'jenis' => 'pemeliharaan',
            'tanggal' => date('Y-m-d'),
        ]);

        try {
            // Buat entri di workorder_service
            $WorkOrderAcService = WorkOrderAcService::create([
                'workorder_id' => $workorder->id,
                'id' => Str::uuid(),
                'status' => 'baru',
                'nowo' => $nowo,
                'customer_asset_id' => $data['customer_asset_id'],
                'teknisi_id' => $data['teknisi_id'],
                'keluhan' => $data['keluhan'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
                'pengecekan' => $data['pengecekan'] ?? null,
                'service' => $data['service'] ?? null,
                'tambah_freon' => $data['tambah_freon'] ?? null,
                'isi_freon' => $data['isi_freon'] ?? null,
                'bongkar' => $data['bongkar'] ?? null,
                'pasang' => $data['pasang'] ?? null,
                'bongkar_pasang' => $data['bongkar_pasang'] ?? null,
                'perbaikan' => $data['perbaikan'] ?? null,
                'check_evaporator' => $data['check_evaporator'] ?? null,
                'keterangan_evaporator' => $data['keterangan_evaporator'] ?? null,
                'check_fan_indoor' => $data['check_fan_indoor'] ?? null,
                'keterangan_fan_indoor' => $data['keterangan_fan_indoor'] ?? null,
                'check_swing' => $data['check_swing'] ?? null,
                'keterangan_swing' => $data['keterangan_swing'] ?? null,
                'check_tegangan_input' => $data['check_tegangan_input'] ?? null,
                'keterangan_tegangan_input' => $data['keterangan_tegangan_input'] ?? null,
                'check_thermis' => $data['check_thermis'] ?? null,
                'keterangan_thermis' => $data['keterangan_thermis'] ?? null,
                'check_temperatur_indoor' => $data['check_temperatur_indoor'] ?? null,
                'keterangan_temperatur_indoor' => $data['keterangan_temperatur_indoor'] ?? null,
                'check_lain_indoor' => $data['check_lain_indoor'] ?? null,
                'keterangan_lain_indoor' => $data['keterangan_lain_indoor'] ?? null,
                'check_kondensor' => $data['check_kondensor'] ?? null,
                'keterangan_kondensor' => $data['keterangan_kondensor'] ?? null,
                'check_fan_outdoor' => $data['check_fan_outdoor'] ?? null,
                'keterangan_fan_outdoor' => $data['keterangan_fan_outdoor'] ?? null,
                'check_kapasitor' => $data['check_kapasitor'] ?? null,
                'keterangan_kapasitor' => $data['keterangan_kapasitor'] ?? null,
                'check_tekanan_freon' => $data['check_tekanan_freon'] ?? null,
                'keterangan_tekanan_freon' => $data['keterangan_tekanan_freon'] ?? null,
                'check_arus' => $data['check_arus'] ?? null,
                'keterangan_arus' => $data['keterangan_arus'] ?? null,
                'check_temperatur_outdoor' => $data['check_temperatur_outdoor'] ?? null,
                'keterangan_temperatur_outdoor' => $data['keterangan_temperatur_outdoor'] ?? null,
                'check_lain_outdoor' => $data['check_lain_outdoor'] ?? null,
                'keterangan_lain_outdoor' => $data['keterangan_lain_outdoor'] ?? null,
                'hasil_pekerjaan' => $data['hasil_pekerjaan'] ?? null,
                'tanda_tangan_pelanggan' => $data['tanda_tangan_pelanggan'] ?? null,
            ]);
            return JsonResponder::success($response, $WorkOrderAcService, "Berhasil membuat workorder", 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal membuat workorder: ' . $th->getMessage(), 500);
        }
    }

    public function createWorkOrderPenjualan(Response $response, array $data): Response
    {
        $nowo = $this->nextWoCode();
        $workorder = Workorder::create([
            'id' => Str::uuid(),
            'nowo' => $nowo,
            'jenis' => 'penjualan',
            'tanggal' => date('Y-m-d'),
        ]);

        try {
            // Buat entri di workorder_penjualan
            $workorderpenjualan = WorkorderPenjualan::create([
                'workorder_id' => $workorder->id,
                'id' => Str::uuid(),
                'status' => 'baru',
                'nowo' => $nowo,
                'customer_asset_id' => $data['customer_asset_id'],
                'teknisi_id' => $data['teknisi_id'],
                'check_indoor' => $data['check_indoor'] ?? null,
                'keterangan_indoor' => $data['keterangan_indoor'] ?? null,
                'check_outdoor' => $data['check_outdoor'] ?? null,
                'keterangan_outdoor' => $data['keterangan_outdoor'] ?? null,
                'check_pipa' => $data['check_pipa'] ?? null,
                'keterangan_pipa' => $data['keterangan_pipa'] ?? null,
                'check_selang' => $data['check_selang'] ?? null,
                'keterangan_selang' => $data['keterangan_selang'] ?? null,
                'check_kabel' => $data['check_kabel'] ?? null,
                'keterangan_kabel' => $data['keterangan_kabel'] ?? null,
                'check_inst_indoor' => $data['check_inst_indoor'] ?? null,
                'keterangan_inst_indoor' => $data['keterangan_inst_indoor'] ?? null,
                'check_inst_outdoor' => $data['check_inst_outdoor'] ?? null,
                'keterangan_inst_outdoor' => $data['keterangan_inst_outdoor'] ?? null,
                'check_inst_listrik' => $data['check_inst_listrik'] ?? null,
                'keterangan_inst_listrik' => $data['keterangan_inst_listrik'] ?? null,
                'check_inst_pipa' => $data['check_inst_pipa'] ?? null,
                'keterangan_inst_pipa' => $data['keterangan_inst_pipa'] ?? null,
                'check_buangan' => $data['check_buangan'] ?? null,
                'keterangan_buangan' => $data['keterangan_buangan'] ?? null,
                'check_vaccum' => $data['check_vaccum'] ?? null,
                'keterangan_vaccum' => $data['keterangan_vaccum'] ?? null,
                'check_freon' => $data['check_freon'] ?? null,
                'keterangan_freon' => $data['keterangan_freon'] ?? null,
                'check_arus' => $data['check_arus'] ?? null,
                'keterangan_arus' => $data['keterangan_arus'] ?? null,
                'check_eva' => $data['check_eva'] ?? null,
                'keterangan_eva' => $data['keterangan_eva'] ?? null,
                'check_kondensor' => $data['check_kondensor'] ?? null,
                'keterangan_kondensor' => $data['keterangan_kondensor'] ?? null,
                'hasil_pekerjaan' => $data['hasil_pekerjaan'] ?? null,
                'tanda_tangan_pelanggan' => $data['tanda_tangan_pelanggan'] ?? null,
            ]);
            return JsonResponder::success($response, $workorderpenjualan, "Berhasil membuat workorder", 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal membuat workorder: ' . $th->getMessage(), 500);
        }
    }

    public function createWorkorderPenyewaan(Response $response, array $data): Response
    {
        $nowo = $this->nextWoCode();
        $workorder = Workorder::create([
            'id' => Str::uuid(),
            'nowo' => $nowo,
            'jenis' => 'penyewaan',
            'tanggal' => date('Y-m-d'),
        ]);

        try {
            // Buat entri di workorder_penyewaan
           $workOrderPenyewaan = WorkOrderPenyewaan::create([
                'workorder_id' => $workorder->id,
                'id' => Str::uuid(),
                'status' => 'baru',
                'nowo' => $nowo,
                'rental_asset_id' => $data['rental_asset_id'],
                'teknisi_id' => $data['teknisi_id'],
                'customer_id' => $data['customer_id'],
                'hasil_pekerjaan' => $data['hasil_pekerjaan'] ?? null,
                'checkIndoor' => $data['checkIndoor'] ?? null,
                'keteranganIndoor' => $data['keteranganIndoor'] ?? null,
                'checkOutdoor' => $data['checkOutdoor'] ?? null,
                'keteranganOutdoor' => $data['keteranganOutdoor'] ?? null,
                'checkPipa' => $data['checkPipa'] ?? null,
                'keteranganPipa' => $data['keteranganPipa'] ?? null,
                'checkSelang' => $data['checkSelang'] ?? null,
                'keteranganSelang' => $data['keteranganSelang'] ?? null,
                'checkKabel' => $data['checkKabel'] ?? null,
                'keteranganKabel' => $data['keteranganKabel'] ?? null,
                'checkInstIndoor' => $data['checkInstIndoor'] ?? null,
                'keteranganInstIndoor' => $data['keteranganInstIndoor'] ?? null,
                'checkInstOutdoor' => $data['checkInstOutdoor'] ?? null,
                'keteranganInstOutdoor' => $data['keteranganInstOutdoor'] ?? null,
                'checkInstListrik' => $data['checkInstListrik'] ?? null,
                'keteranganInstListrik' => $data['keteranganInstListrik'] ?? null,
                'checkInstPipa' => $data['checkInstPipa'] ?? null,
                'keteranganInstPipa' => $data['keteranganInstPipa'] ?? null,
                'checkBuangan' => $data['checkBuangan'] ?? null,
                'keteranganBuangan' => $data['keteranganBuangan'] ?? null,
                'checkVaccum' => $data['checkVaccum'] ?? null,
                'keteranganVaccum' => $data['keteranganVaccum'] ?? null,
                'checkFreon' => $data['checkFreon'] ?? null,
                'keteranganFreon' => $data['keteranganFreon'] ?? null,
                'checkArus' => $data['checkArus'] ?? null,
                'keteranganArus' => $data['keteranganArus'] ?? null,
                'checkEva' => $data['checkEva'] ?? null,
                'keteranganEva' => $data['keteranganEva'] ?? null,
                'checkKondensor' => $data['checkKondensor'] ?? null,
                'keteranganKondensor' => $data['keteranganKondensor'] ?? null,
                'checkIndoorB' => $data['checkIndoorB'] ?? false,
                'keteranganIndoorB' => $data['keteranganIndoorB'] ?? '',
                'checkOutdoorB' => $data['checkOutdoorB'] ?? false,
                'keteranganOutdoorB' => $data['keteranganOutdoorB'] ?? '',
                'checkPipaB' => $data['checkPipaB'] ?? false,
                'keteranganPipaB' => $data['keteranganPipaB'] ?? '',
                'checkSelangB' => $data['checkSelangB'] ?? false,
                'keteranganSelangB' => $data['keteranganSelangB'] ?? '',
                'checkKabelB' => $data['checkKabelB'] ?? false,
                'keteranganKabelB' => $data['keteranganKabelB'] ?? '',
                'tanda_tangan_pelanggan' => $data['tanda_tangan_pelanggan'] ?? null,
            ]);
            return JsonResponder::success($response, $workOrderPenyewaan, 'Workorder Penyewaan Berhasil dibuat',201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal membuat workorder: ' . $th->getMessage(), 500);
        }
    }

    public function getWorkOrderById(Response $response, array $args): Response
    {
        $workorder = Workorder::with(['workOrderAcService', 'workorderPenyewaan', 'workorderPenjualan'])->find($args['id']);
        if (!$workorder) {
            return JsonResponder::error($response, 'Workorder tidak ditemukan', 404);
        }
        return JsonResponder::success($response, $workorder,'Berhasil mengambil workorder', 200);
    }

    public function listWorkOrders(Response $response): Response
    {
        $workorders = Workorder::orderBy('created_at', 'desc')->get();

        $result = $workorders->map(function($wo) {
            $nama_pelanggan = null;
            if ($wo->workOrderAcService) {
                $nama_pelanggan = $wo->workOrderAcService->customerAsset->customer->nama;
                $nama_pegawai = $wo->workOrderAcService->pegawai->nama ?? null;
                $status = $wo->workOrderAcService->status ?? null;
                $hp = $wo->workOrderAcService->customerAsset->customer->hp ?? null;
                $customerCode = $wo->workOrderAcService->customerCode ?? null;

            } elseif ($wo->workorderPenyewaan) {
                $nama_pelanggan = $wo->workorderPenyewaan->customer->nama;
                $nama_pegawai = $wo->workorderPenyewaan->pegawai->nama ?? null;
                $status = $wo->workorderPenyewaan->status ?? null;
                $hp = $wo->workorderPenyewaan->customer->hp ?? null;
                $customerCode = $wo->workorderPenyewaan->customerCode ?? null;
            } elseif ($wo->workorderPenjualan) {
                $nama_pelanggan = $wo->workorderPenjualan->customerAsset->customer->nama;
                $nama_pegawai = $wo->workorderPenjualan->pegawai->nama ?? null;
                $status = $wo->workorderPenjualan->status ?? null;
                $hp = $wo->workorderPenjualan->customerAsset->customer->hp ?? null;
                $customerCode = $wo->workorderPenjualan->customerCode ?? null;
            }
            return array_merge($wo->toArray(), [
                'nama_pelanggan' => $nama_pelanggan,
                'nama_pegawai' => $nama_pegawai,
                'status' => $status,
                'hp' => $hp,
                'customerCode' => $customerCode,
            ]);
        });

        return JsonResponder::success($response, $result, 'Berhasil mengambil daftar workorder', 200);
    }

    public function getPegawaiList(Response $response): Response
    {
        $pegawai = Pegawai::all();
        return JsonResponder::success($response, $pegawai, 'Berhasil mengambil daftar pegawai', 200);
    }

    public function getWorkOrderServiceById(Response $response, $workorder_id): Response
    {
        $workOrderAcService = WorkOrderAcService::with(['customerAsset.customer', 'pegawai', 'customerAsset.brand', 'customerAsset.tipe'])->where('id', $workorder_id)->first();
        if (!$workOrderAcService) {
            return JsonResponder::error($response, 'Workorder Service tidak ditemukan', 404);
        }
        return JsonResponder::success($response, $workOrderAcService, 'Berhasil mengambil workorder service', 200);
    }

    public function getWorkOrderPenyewaanById(Response $response, $workorder_id): Response
    {
        $workOrderPenyewaan = WorkOrderPenyewaan::with(['customer', 'pegawai', 'rentalAsset.brand', 'rentalAsset.tipe'])->where('id', $workorder_id)->first();
        if (!$workOrderPenyewaan) {
            return JsonResponder::error($response, 'Workorder Penyewaan tidak ditemukan', 404);
        }
        return JsonResponder::success($response, $workOrderPenyewaan, 'Berhasil mengambil workorder penyewaan', 200);
    }

    public function getWorkOrderPenjualanById(Response $response, $workorder_id): Response
    {
        $workOrderPenjualan = WorkorderPenjualan::with(['customerAsset.customer', 'pegawai', 'customerAsset.brand', 'customerAsset.tipe'])->where('id', $workorder_id)->first();
        if (!$workOrderPenjualan) {
            return JsonResponder::error($response, 'Workorder Penjualan tidak ditemukan', 404);
        }
        return JsonResponder::success($response, $workOrderPenjualan, 'Berhasil mengambil workorder penjualan', 200);
    }

    public function updateWorkOrderService(Response $response, array $data, $workorder_id): Response
    {
        $workOrderAcService = WorkOrderAcService::where('id', $workorder_id)->first();
        if (!$workOrderAcService) {
            return JsonResponder::error($response, 'Workorder Service tidak ditemukan', 404);
        }

        try {
            $workOrderAcService->update($data);
            return JsonResponder::success($response, $workOrderAcService, 'Berhasil memperbarui workorder service', 200);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal memperbarui workorder service: ' . $th->getMessage(), 500);
        }
    }

    public function updateWorkOrderPenyewaan(Response $response, array $data, $workorder_id): Response
    {
        $workOrderPenyewaan = WorkOrderPenyewaan::where('id', $workorder_id)->first();
        if (!$workOrderPenyewaan) {
            return JsonResponder::error($response, 'Workorder Penyewaan tidak ditemukan', 404);
        }

        try {
            $workOrderPenyewaan->update($data);
            return JsonResponder::success($response, $workOrderPenyewaan, 'Berhasil memperbarui workorder penyewaan', 200);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal memperbarui workorder penyewaan: ' . $th->getMessage(), 500);
        }
    }

    public function updateWorkOrderPenjualan(Response $response, array $data, $workorder_id): Response
    {
        $workOrderPenjualan = WorkorderPenjualan::where('id', $workorder_id)->first();
        if (!$workOrderPenjualan) {
            return JsonResponder::error($response, 'Workorder Penjualan tidak ditemukan', 404);
        }

        try {
            $workOrderPenjualan->update($data);
            return JsonResponder::success($response, $workOrderPenjualan, 'Berhasil memperbarui workorder penjualan', 200);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal memperbarui workorder penjualan: ' . $th->getMessage(), 500);
        }
    }

    public function  setLinkSignatureWorkorderService(Response $response, $workorder_id): Response
    {
        $customerCode = $this->random11();
        $workOrderAcService = WorkOrderAcService::where('id', $workorder_id)->first();
        if (!$workOrderAcService) {
            return JsonResponder::error($response, 'Workorder Service tidak ditemukan', 404);
        }

        try {
            $workOrderAcService->customerCode = $customerCode;
            $workOrderAcService->status = 'waiting signature';
            $workOrderAcService->save();
            return JsonResponder::success($response, $customerCode, 'Berhasil memperbarui tanda tangan pelanggan', 200);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal memperbarui tanda tangan pelanggan: ' . $th->getMessage(), 500);
        }
    }

    public function  setLinkSignatureWorkorderPenyewaan(Response $response, $workorder_id): Response
    {
        $customerCode = $this->random11();
        $workOrderPenyewaan = WorkOrderPenyewaan::where('id', $workorder_id)->first();
        if (!$workOrderPenyewaan) {
            return JsonResponder::error($response, 'Workorder Penyewaan tidak ditemukan', 404);
        }

        try {
            $workOrderPenyewaan->customerCode = $customerCode;
            $workOrderPenyewaan->status = 'waiting signature';
            $workOrderPenyewaan->save();
            return JsonResponder::success($response, $customerCode, 'Berhasil memperbarui tanda tangan pelanggan', 200);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal memperbarui tanda tangan pelanggan: ' . $th->getMessage(), 500);
        }
    }

    public function  setLinkSignatureWorkorderPenjualan(Response $response, $workorder_id): Response
    {
        $customerCode = $this->random11();
        $workOrderPenjualan = WorkorderPenjualan::where('id', $workorder_id)->first();
        if (!$workOrderPenjualan) {
            return JsonResponder::error($response, 'Workorder Penjualan tidak ditemukan', 404);
        }

        try {
            $workOrderPenjualan->customerCode = $customerCode;
            $workOrderPenjualan->status = 'waiting signature';
            $workOrderPenjualan->save();
            return JsonResponder::success($response, $customerCode, 'Berhasil memperbarui tanda tangan pelanggan', 200);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal memperbarui tanda tangan pelanggan: ' . $th->getMessage(), 500);
        }
    }

    public function updateSignatureWorkorderService(Response $response, File $file, $customerCode): Response
    {

        $workOrderAcService = WorkOrderAcService::where('customerCode', $customerCode)->first();
        if (!$workOrderAcService) {
            return JsonResponder::error($response, 'Workorder Service tidak ditemukan', 404);
        }
        if ($file && $file->getError() === UPLOAD_ERR_OK) {
                $filename = Upload::storeImage($file, 'tanda_tangan');
                $workOrderAcService->tanda_tangan_pelanggan = $filename;
                $workOrderAcService->status = 'selesai';
                $workOrderAcService->save();
            }else {
                $workOrderAcService->tanda_tangan_pelanggan = null;
                $workOrderAcService->status = 'selesai';
                $workOrderAcService->save();
                return JsonResponder::error($response, 'File tanda tangan tidak ada, Tutup tanpa tanda tangan', 400);
            }
            $customer_asset_id = $workOrderAcService->customer_asset_id;
            $customerAsset = CustomerAsset::find($customer_asset_id);
            $customerAsset->lastService = date('Y-m-d');
            $customerAsset->nextService = date('Y-m-d', strtotime('+4 months'));
            $customerAsset->save();

        try {
            $workOrderAcService->tanda_tangan_pelanggan = $data['tanda_tangan_pelanggan'] ?? null;
            $workOrderAcService->save();
            return JsonResponder::success($response, $workOrderAcService, 'Berhasil memperbarui tanda tangan pelanggan', 200);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal memperbarui tanda tangan pelanggan: ' . $th->getMessage(), 500);
        }
    }
    public function updateSignatureWorkorderPenyewaan(Response $response, File $file, $customerCode): Response
    {

        $workOrderPenyewaan = WorkOrderPenyewaan::where('customerCode', $customerCode)->first();
        if (!$workOrderPenyewaan) {
            return JsonResponder::error($response, 'Workorder Penyewaan tidak ditemukan', 404);
        }
        if ($file && $file->getError() === UPLOAD_ERR_OK) {
                $filename = Upload::storeImage($file, 'tanda_tangan');
                $workOrderPenyewaan->tanda_tangan_pelanggan = $filename;
                $workOrderPenyewaan->status = 'selesai';
                $workOrderPenyewaan->save();
            }else {
                $workOrderPenyewaan->tanda_tangan_pelanggan = null;
                $workOrderPenyewaan->status = 'selesai';
                $workOrderPenyewaan->save();
                return JsonResponder::error($response, 'File tanda tangan tidak ada, Tutup tanpa tanda tangan', 400);
            }
            $rental_asset_id = $workOrderPenyewaan->rental_asset_id;
            $rentalAsset = RentalAsset::find($rental_asset_id);
            $harga_sewa = $rentalAsset->harga_sewa ?? 0;
            $harga_perolehan = $rentalAsset->harga_perolehan ?? 0;
            $harga_perolehan_akhir = $harga_perolehan - $harga_sewa;
            $rentalAsset->sisa_harga_sekarang = $harga_perolehan_akhir;
        try {
            $workOrderPenyewaan->tanda_tangan_pelanggan = $data['tanda_tangan_pelanggan'] ?? null;
            $workOrderPenyewaan->save();
            return JsonResponder::success($response, $workOrderPenyewaan, 'Berhasil memperbarui tanda tangan pelanggan', 200);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal memperbarui tanda tangan pelanggan: ' . $th->getMessage(), 500);
        }
    }
    public function updateSignatureWorkorderPenjualan(Response $response, File $file, $customerCode): Response
    {

        $workOrderPenjualan = WorkorderPenjualan::where('customerCode', $customerCode)->first();
        if (!$workOrderPenjualan) {
            return JsonResponder::error($response, 'Workorder Penjualan tidak ditemukan', 404);
        }
        if ($file && $file->getError() === UPLOAD_ERR_OK) {
                $filename = Upload::storeImage($file, 'tanda_tangan');
                $workOrderPenjualan->tanda_tangan_pelanggan = $filename;
                $workOrderPenjualan->status = 'selesai';
                $workOrderPenjualan->save();
            }else {
                $workOrderPenjualan->tanda_tangan_pelanggan = null;
                $workOrderPenjualan->status = 'selesai';
                $workOrderPenjualan->save();
                return JsonResponder::error($response, 'File tanda tangan tidak ada, Tutup tanpa tanda tangan', 400);
            }
            $customer_asset_id = $workOrderPenjualan->customer_asset_id;
            $customerAsset = CustomerAsset::find($customer_asset_id);
            $customerAsset->lastService = date('Y-m-d');
            $customerAsset->nextService = date('Y-m-d', strtotime('+4 months'));
            $customerAsset->save();

        try {
            $workOrderPenjualan->tanda_tangan_pelanggan = $data['tanda_tangan_pelanggan'] ?? null;
            $workOrderPenjualan->save();
            return JsonResponder::success($response, $workOrderPenjualan, 'Berhasil memperbarui tanda tangan pelanggan', 200);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal memperbarui tanda tangan pelanggan: ' . $th->getMessage(), 500);
        }
    }

    public function getWorkoderServiceByCustomerCode(Response $response, $customerCode): Response
    {
        $workOrderAcService = WorkOrderAcService::with(['customerAsset.customer', 'pegawai', 'customerAsset.brand', 'customerAsset.tipe'])->where('customerCode', $customerCode)->first();
        if (!$workOrderAcService) {
            return JsonResponder::error($response, 'Workorder Service tidak ditemukan', 404);
        }
        return JsonResponder::success($response, $workOrderAcService, 'Berhasil mengambil workorder service', 200);
    }

    public function getWorkoderPenyewaanByCustomerCode(Response $response, $customerCode): Response
    {
        $workOrderPenyewaan = WorkOrderPenyewaan::with(['customer', 'pegawai', 'rentalAsset.brand', 'rentalAsset.tipe'])->where('customerCode', $customerCode)->first();
        if (!$workOrderPenyewaan) {
            return JsonResponder::error($response, 'Workorder Penyewaan tidak ditemukan', 404);
        }
        return JsonResponder::success($response, $workOrderPenyewaan, 'Berhasil mengambil workorder penyewaan', 200);
    }

    public function getWorkoderPenjualanByCustomerCode(Response $response, $customerCode): Response
    {
        $workOrderPenjualan = WorkorderPenjualan::with(['customerAsset.customer', 'pegawai', 'customerAsset.brand', 'customerAsset.tipe'])->where('customerCode', $customerCode)->first();
        if (!$workOrderPenjualan) {
            return JsonResponder::error($response, 'Workorder Penjualan tidak ditemukan', 404);
        }
        return JsonResponder::success($response, $workOrderPenjualan, 'Berhasil mengambil workorder penjualan', 200);
    }

}
