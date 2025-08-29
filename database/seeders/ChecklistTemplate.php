<?php
namespace Database\Seeders;

use App\Models\ChecklistTemplate;

class ChecklistTemplateSeeder
{
    public function run()
    {
        ChecklistTemplate::create([
            'no_urut' => 1,
            'kode_checklist' => 'A',
            'title' => 'Bagian Indoor',
            'checklist' => 'Evaporator',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);

        ChecklistTemplate::create([
            'no_urut' => 2,
            'kode_checklist' => 'A',
            'title' => 'Bagian Indoor',
            'checklist' => 'Fan/Blower Indoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 3,
            'kode_checklist' => 'A',
            'title' => 'Bagian Indoor',
            'checklist' => 'Kondisi Swing',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 4,
            'kode_checklist' => 'A',
            'title' => 'Bagian Indoor',
            'checklist' => 'Tegangan Input',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 5,
            'kode_checklist' => 'A',
            'title' => 'Bagian Indoor',
            'checklist' => 'Thermis/Temp Sensor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 6,
            'kode_checklist' => 'A',
            'title' => 'Bagian Indoor',
            'checklist' => 'Check temperature',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 7,
            'kode_checklist' => 'A',
            'title' => 'Bagian Indoor',
            'checklist' => 'Lain-lain',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 8,
            'kode_checklist' => 'B',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Kondensor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 9,
            'kode_checklist' => 'B',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Fan Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 10,
            'kode_checklist' => 'B',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Capasitor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 11,
            'kode_checklist' => 'B',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Tekanan Freon',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 12,
            'kode_checklist' => 'B',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Arus/Amperer',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 13,
            'kode_checklist' => 'B',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Check Temperatur',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 14,
            'kode_checklist' => 'B',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Lain-lain',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 1,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Indoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 2,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 3,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Pipa AC',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 4,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Selang Buangan',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 5,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Bracket Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 6,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Kabel Listrik',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 7,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Stop Kontak dan Plug',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 8,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Dinabolt dan Fisher',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 9,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Unit Indoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 10,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 11,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Kelistrikan',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 12,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Pipa AC',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 13,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Buangan Air',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 14,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Vaccum',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 15,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Tekanan Freon',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 16,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Arus(Ampere)',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 17,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Temperature Evaporator',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 17,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Temperature Kondensor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 1,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Indoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 2,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 3,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Pipa AC',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 4,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Selang Buangan',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
        ]);
        ChecklistTemplate::create([
            'no_urut' => 5,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Kabel Listrik dan Accessories',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
        ]);
    }
}
