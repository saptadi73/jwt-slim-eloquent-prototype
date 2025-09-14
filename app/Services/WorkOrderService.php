<?php
namespace App\Services;

use App\Models\Workorder;
use App\Models\WorkOrderAcService;
use App\Models\WorkOrderPenyewaan;
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

    public function createWorkorderPemeliharaan(Response $response, array $data): Response{
        $nowo = $this->nextWoCode();
        $workorder = Workorder::create([
            'nowo' => $nowo,
            'jenis' => 'pemeliharaan',
            'tanggal' => date('Y-m-d'),
        ]);

        try {
            // Buat entri di workorder_service
            WorkOrderAcService::create([
                'workorder_id' => $workorder->id,
                'status' => 'baru',
                'nowo' => $nowo,
                'customer_asset_id' => $data['asset_id'],
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
            return JsonResponder::success($response, $workorder, 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal membuat workorder: ' . $th->getMessage(), 500);
        }
    }

    public function createWorkOrderPenjualan(Response $response, array $data): Response{
        $nowo = $this->nextWoCode();
        $workorder = Workorder::create([
            'nowo' => $nowo,
            'jenis' => 'penjualan',
            'tanggal' => date('Y-m-d'),
        ]);

        try {
            // Buat entri di workorder_penjualan
            WorkorderPenjualan::create([
                'workorder_id' => $workorder->id,
                'status' => 'baru',
                'nowo' => $nowo,
                'customer_asset_id' => $data['asset_id'],
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
            return JsonResponder::success($response, $workorder, 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal membuat workorder: ' . $th->getMessage(), 500);
        }
    }

    public function createWorkorderPenyewaan(Response $response, array $data): Response{
        $nowo = $this->nextWoCode();
        $workorder = Workorder::create([
            'nowo' => $nowo,
            'jenis' => 'penyewaan',
            'tanggal' => date('Y-m-d'),
        ]);

        try {
            // Buat entri di workorder_penyewaan
            WorkOrderPenyewaan::create([
                'workorder_id' => $workorder->id,
                'status' => 'baru',
                'nowo' => $nowo,
                'customer_asset_id' => $data['asset_id'],
                'teknisi_id' => $data['teknisi_id'],
                'check_unit' => $data['check_unit'] ?? null,
                'keterangan_unit' => $data['keterangan_unit'] ?? null,
                'check_remote' => $data['check_remote'] ?? null,
                'keterangan_remote' => $data['keterangan_remote'] ?? null,
                'check_pipa' => $data['check_pipa'] ?? null,
                'keterangan_pipa' => $data['keterangan_pipa'] ?? null,
                'check_selang' => $data['check_selang'] ?? null,
                'keterangan_selang' => $data['keterangan_selang'] ?? null,
                'check_kabel' => $data['check_kabel'] ?? null,
                'keterangan_kabel' ?? null,
                'check_drainase' => $data['check_drainase'] ?? null,
                'keterangan_drainase' => $data['keterangan_drainase'] ?? null,
                'hasil_pekerjaan' => $data['hasil_pekerjaan'] ?? null,
                'tanda_tangan_pelanggan' => $data['tanda_tangan_pelanggan'] ?? null,
            ]);
            return JsonResponder::success($response, $workorder, 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Gagal membuat workorder: ' . $th->getMessage(), 500);
        }
    }

    public function getWorkOrderById(Response $response, array $args): Response{
        $workorder = Workorder::with(['workOrderAcService', 'workorderPenyewaan', 'workorderPenjualan'])->find($args['id']);
        if (!$workorder) {
            return JsonResponder::error($response, 'Workorder tidak ditemukan', 404);
        }
        return JsonResponder::success($response, $workorder);
    }

    public function listWorkOrders(Response $response): Response{
        $workorders = Workorder::with(['workOrderAcService', 'workorderPenyewaan', 'workorderPenjualan'])->orderBy('created_at', 'desc')->get();
        return JsonResponder::success($response, $workorders);
    }

}

?>