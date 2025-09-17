<?php

namespace App\Services;

use App\Models\Workorder;
use App\Models\WorkOrderAcService;
use App\Models\WorkOrderPenyewaan;
use Illuminate\Support\Str;
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
        $workorders = Workorder::with(['workOrderAcService', 'workorderPenyewaan', 'workorderPenjualan'])->orderBy('created_at', 'desc')->get();
        return JsonResponder::success($response, $workorders, 'Berhasil mengambil daftar workorder', 200);
    }

    public function getPegawaiList(Response $response): Response
    {
        $pegawai = Pegawai::all();
        return JsonResponder::success($response, $pegawai, 'Berhasil mengambil daftar pegawai', 200);
    }
}
