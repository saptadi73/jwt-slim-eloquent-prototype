<?php

namespace App\Services;

use App\Models\ChecklistTemplate;
use App\Models\JenisWorkorder;
use App\Models\Tipe;
use App\Models\Brand;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CheckListService
{
    public static function isiBrand()
    {
        $now  = Carbon::now();
        $brands = [
            ['nama' => 'Samsung'],
            ['nama' => 'Mitsubishi'],
            ['nama' => 'Panasonic'],
            ['nama' => 'Sharp'],
            ['nama' => 'Gree'],
            ['nama' => 'BestLife'],
            ['nama' => 'Changhong'],
            ['nama' => 'Polytron'],
            ['nama' => 'Midea'],
            ['nama' => 'Toshiba'],
            ['nama' => 'Flife'],
        ];
        $brands = array_map(function ($r) use ($now) {
            return $r + [
                'id'         => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $brands);

        Brand::query()->insert($brands);
        return Brand::all(); // kembalikan seluruh data brand
    }

    public static function isiTableJenisWorkorder() {
        $now  = Carbon::now();
        $jenisWorkorder = [
            ['nama' => 'Jasa/Service AC'],
            ['nama' => 'Penjualan AC'],
            ['nama' => 'Penyewaan AC'],
        ];
        $jenisWorkorder = array_map(function ($r) use ($now) {
            return $r + [
                'id'         => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $jenisWorkorder);

        JenisWorkorder::query()->insert($jenisWorkorder);
        return JenisWorkorder::all();
    }

    public static function isiTableTipe()
    {
        $now  = Carbon::now();
        $tipe = [
            ['nama' => 'Split'],
            ['nama' => 'Windows'],
            ['nama' => 'Standing'],
            ['nama' => 'Portable'],
            ['nama' => 'Cassette'],
            ['nama' => 'Ducting'],
            ['nama' => 'Floor Standing'],
            ['nama' => 'VRF/VRV'],
            ['nama' => 'Central Chiller'],
        ];
        $tipe = array_map(function ($r) use ($now) {
            return $r + [
                'id'         => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $tipe);

        Tipe::query()->insert($tipe);
        return Tipe::all();
    }

    public static function isiTableTeknisiServiceAC()
    {
        $now  = Carbon::now();
        $rows = [
            [
                'no_urut' => 1,
                'kode_checklist' => 'A1JS1',
                'title' => 'Bagian Indoor',
                'checklist' => 'Evaporator',
                'pic' => 'teknisi',
                'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
                'no_urut' => 2,
                'kode_checklist' => 'A1JS2',
                'title' => 'Bagian Indoor',
                'checklist' => 'Fan/Blower Indoor',
                'pic' => 'teknisi',
                'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
            'no_urut' => 3,
            'kode_checklist' => 'A1JS3',
            'title' => 'Bagian Indoor',
            'checklist' => 'Kondisi Swing',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
            'no_urut' => 4,
            'kode_checklist' => 'A1JS4',
            'title' => 'Bagian Indoor',
            'checklist' => 'Tegangan Input',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
        ],
            [
            'no_urut' => 5,
            'kode_checklist' => 'A1JS5',
            'title' => 'Bagian Indoor',
            'checklist' => 'Thermis/Temp Sensor',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
            'no_urut' => 6,
            'kode_checklist' => 'A1JS6',
            'title' => 'Bagian Indoor',
            'checklist' => 'Check temperature',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
            'no_urut' => 7,
            'kode_checklist' => 'A1JS7',
            'title' => 'Bagian Indoor',
            'checklist' => 'Lain-lain',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
            'no_urut' => 8,
            'kode_checklist' => 'B1JS8',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Kondensor',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
            'no_urut' => 9,
            'kode_checklist' => 'B1JS9',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Fan Outdoor',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
            'no_urut' => 10,
            'kode_checklist' => 'B1JS10',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Capasitor',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
            'no_urut' => 11,
            'kode_checklist' => 'B1JS11',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Tekanan Freon',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
            'no_urut' => 12,
            'kode_checklist' => 'B1JS12',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Arus/Amperer',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
            'no_urut' => 13,
            'kode_checklist' => 'B1JS13',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Check Temperatur',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
            ],
            [
            'no_urut' => 14,
            'kode_checklist' => 'B1JS14',
            'title' => 'Bagian Outdoor',
            'checklist' => 'Lain-lain',
            'pic' => 'teknisi',
            'jenis_id' => '2c052542-a1e5-411b-8dba-e8afe943c4ea'
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
            'kode_checklist' => 'A2JL1',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Indoor',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 2,
            'kode_checklist' => 'A2JL2',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 3,
            'kode_checklist' => 'A2JL3',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Pipa AC',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 4,
            'kode_checklist' => 'A2JL4',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Selang Buangan',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 5,
            'kode_checklist' => 'A2JL5',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Bracket Outdoor',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 6,
            'kode_checklist' => 'A2JL6',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Kabel Listrik',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 7,
            'kode_checklist' => 'A2JL7',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Stop Kontak dan Plug',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 8,
            'kode_checklist' => 'A2JL8',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Dinabolt dan Fisher',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 9,
            'kode_checklist' => 'B2JL9',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Unit Indoor',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 10,
            'kode_checklist' => 'B2JL10',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 11,
            'kode_checklist' => 'B2JL11',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Kelistrikan',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 12,
            'kode_checklist' => 'B2JL12',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Pipa AC',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 13,
            'kode_checklist' => 'B2JL13',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Buangan Air',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 14,
            'kode_checklist' => 'B2JL14',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Vaccum',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 15,
            'kode_checklist' => 'B2JL15',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Tekanan Freon',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 16,
            'kode_checklist' => 'B2JL16',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Arus(Ampere)',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 17,
            'kode_checklist' => 'B2JL17',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Temperature Evaporator',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 18,
            'kode_checklist' => 'B2JL18',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Temperature Kondensor',
            'pic' => 'teknisi',
            'jenis_id' => 'b11bfa6c-c21d-4ca3-b8bc-48633b2d5a13'
            ],
            [
            'no_urut' => 1,
            'kode_checklist' => 'A3SW1',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Indoor',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 2,
            'kode_checklist' => 'A3SW2',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 3,
            'kode_checklist' => 'A3SW3',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Pipa AC',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 4,
            'kode_checklist' => 'A3SW4',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Selang Buangan',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 5,
            'kode_checklist' => 'A3SW5',
            'title' => 'Unit AC & accessories',
            'checklist' => 'Kabel Listrik dan Accessories',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 6,
            'kode_checklist' => 'B3SW6',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Unit Indoor',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 7,
            'kode_checklist' => 'B3SW7',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 8,
            'kode_checklist' => 'B3SW8',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Kelistrikan',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 9,
            'kode_checklist' => 'B3SW9',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Pipa AC',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 10,
            'kode_checklist' => 'B3SW10',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Instalasi Buangan Air',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 11,
            'kode_checklist' => 'B3SW11',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Vaccum',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 12,
            'kode_checklist' => 'B3SW12',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Tekanan Freon',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 13,
            'kode_checklist' => 'B3SW13',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Arus(Ampere)',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 14,
            'kode_checklist' => 'B3SW14',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Temperature Evaporator',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 15,
            'kode_checklist' => 'B3SW15',
            'title' => 'Pekerjaan Pemasangan AC',
            'checklist' => 'Cek Temperature Kondensor',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 16,
            'kode_checklist' => 'C3SW16',
            'title' => 'Pekerjaan Bongkar AC',
            'checklist' => 'Unit Indoor',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 17,
            'kode_checklist' => 'C3SW17',
            'title' => 'Pekerjaan Bongkar AC',
            'checklist' => 'Unit Outdoor',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 18,
            'kode_checklist' => 'C3SW18',
            'title' => 'Pekerjaan Bongkar AC',
            'checklist' => 'Pipa AC',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 19,
            'kode_checklist' => 'C3SW19',
            'title' => 'Pekerjaan Bongkar AC',
            'checklist' => 'Selang Buangan',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
            ],
            [
            'no_urut' => 20,
            'kode_checklist' => 'C3SW20',
            'title' => 'Pekerjaan Bongkar AC',
            'checklist' => 'Kabel Listrik dan Accessories',
            'pic' => 'teknisi',
            'jenis_id' => 'e3dd352d-7746-4362-960a-2aeea6f21b56'
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
