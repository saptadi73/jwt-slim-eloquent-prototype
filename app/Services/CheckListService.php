<?php

namespace App\Services;

use App\Models\ChecklistTemplate;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CheckListService
{
    public static function isiTableTeknisiServiceAC(): void
    {
        $now  = Carbon::now();
        $rows = [
            [
                'no_urut' => 1,
                'kode_checklist' => 'A01',
                'title' => 'Bagian Indoor',
                'checklist' => 'Evaporator',
                'pic' => 'teknisi',
                'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
                'no_urut' => 2,
                'kode_checklist' => 'A02',
                'title' => 'Bagian Indoor',
                'checklist' => 'Fan/Blower Indoor',
                'pic' => 'teknisi',
                'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 3,
            'kode_checklist' => 'A03',
            'title' => 'Bagian Indoor',
            'checklist' => 'Kondisi Swing',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 4,
            'kode_checklist' => 'A04',
            'title' => 'Bagian Indoor',
            'checklist' => 'Tegangan Input',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ],
            [
            'no_urut' => 5,
            'kode_checklist' => 'A05',
            'title' => 'Bagian Indoor',
            'checklist' => 'Thermis/Temp Sensor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 6,
            'kode_checklist' => 'A06',
            'title' => 'Bagian Indoor',
            'checklist' => 'Check temperature',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 7,
            'kode_checklist' => 'A07',
            'title' => 'Bagian Indoor',
            'checklist' => 'Lain-lain',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 8,
            'kode_checklist' => 'B01',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Kondensor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 9,
            'kode_checklist' => 'B02',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Fan Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 10,
            'kode_checklist' => 'B03',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Capasitor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 11,
            'kode_checklist' => 'B04',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Tekanan Freon',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 12,
            'kode_checklist' => 'B05',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Arus/Amperer',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 13,
            'kode_checklist' => 'B06',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Check Temperatur',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 14,
            'kode_checklist' => 'B07',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Lain-lain',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
        ];

        // Tambahkan UUID & timestamps, karena insert() tak memanggil events
        $rows = array_map(function ($r) use ($now) {
            return $r + [
                'id'         => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $rows);

        ChecklistTemplate::query()->insert($rows);
    }

    public static function isiTableTeknisiServiceAC2(): void
    {
        $now  = Carbon::now();
        $rows = [
            [
                'no_urut' => 1,
                'kode_checklist' => 'A01',
                'title' => 'Bagian Indoor',
                'checklist' => 'Evaporator',
                'pic' => 'teknisi',
                'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
                'no_urut' => 2,
                'kode_checklist' => 'A02',
                'title' => 'Bagian Indoor',
                'checklist' => 'Fan/Blower Indoor',
                'pic' => 'teknisi',
                'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 3,
            'kode_checklist' => 'A03',
            'title' => 'Bagian Indoor',
            'checklist' => 'Kondisi Swing',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 4,
            'kode_checklist' => 'A04',
            'title' => 'Bagian Indoor',
            'checklist' => 'Tegangan Input',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
        ],
            [
            'no_urut' => 5,
            'kode_checklist' => 'A05',
            'title' => 'Bagian Indoor',
            'checklist' => 'Thermis/Temp Sensor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 6,
            'kode_checklist' => 'A06',
            'title' => 'Bagian Indoor',
            'checklist' => 'Check temperature',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 7,
            'kode_checklist' => 'A07',
            'title' => 'Bagian Indoor',
            'checklist' => 'Lain-lain',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 8,
            'kode_checklist' => 'B01',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Kondensor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 9,
            'kode_checklist' => 'B02',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Fan Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 10,
            'kode_checklist' => 'B03',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Capasitor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 11,
            'kode_checklist' => 'B04',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Tekanan Freon',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 12,
            'kode_checklist' => 'B05',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Arus/Amperer',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 13,
            'kode_checklist' => 'B06',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Check Temperatur',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 14,
            'kode_checklist' => 'B07',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Lain-lain',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Jasa/Service AC'
            ],
            [
            'no_urut' => 1,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Indoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 2,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 3,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Pipa AC',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 4,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Selang Buangan',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 5,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Bracket Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 6,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Kabel Listrik',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 7,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Stop Kontak dan Plug',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 8,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Dinabolt dan Fisher',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 9,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Unit Indoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 10,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 11,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Kelistrikan',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 12,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Pipa AC',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 13,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Buangan Air',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 14,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Vaccum',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 15,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Tekanan Freon',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 16,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Arus(Ampere)',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 17,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Temperature Evaporator',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 18,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Temperature Kondensor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penjualan AC'
            ],
            [
            'no_urut' => 1,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Indoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 2,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 3,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Pipa AC',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 4,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Selang Buangan',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 5,
            'kode_checklist' => 'A',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Kabel Listrik dan Accessories',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 6,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Unit Indoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 7,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 8,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Kelistrikan',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 9,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Pipa AC',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 10,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Buangan Air',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 11,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Vaccum',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 12,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Tekanan Freon',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 13,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Arus(Ampere)',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 14,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Temperature Evaporator',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 15,
            'kode_checklist' => 'B',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Temperature Kondensor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 16,
            'kode_checklist' => 'C',
            'title' => 'Pekerjaan Bongkar AC',
            'checklist' => 'Unit Indoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 17,
            'kode_checklist' => 'C',
            'title' => 'Pekerjaan Bongkar AC',
            'checklist' => 'Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 18,
            'kode_checklist' => 'C',
            'title' => 'Pekerjaan Bongkar AC',
            'checklist' => 'Pipa AC',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 19,
            'kode_checklist' => 'C',
            'title' => 'Pekerjaan Bongkar AC',
            'checklist' => 'Selang Buangan',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
            [
            'no_urut' => 20,
            'kode_checklist' => 'C',
            'title' => 'Pekerjaan Bongkar AC',
            'checklist' => 'Kabel Listrik dan Accessories',
            'pic' => 'teknisi',
            'jenis_workorder' => 'Penyewaan AC'
            ],
        ];

        // Tambahkan UUID & timestamps, karena insert() tak memanggil events
        $rows = array_map(function ($r) use ($now) {
            return $r + [
                'id'         => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $rows);

        ChecklistTemplate::query()->insert($rows);
    }
}
