<?php

namespace App\Services;

use App\Models\ChecklistTemplate;
use App\Models\JenisWorkorder;
use App\Models\Tipe;
use App\Models\Brand;
use App\Models\Departemen;
use App\Models\Group;
use App\Models\Pegawai;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CheckListService
{
    public static function isiDepartemen() {
        $now= Carbon::now();
        $departemen = [
            ['nama' => 'HRD'],
            ['nama' => 'Teknisi'],
            ['nama' => 'Finance'],
            ['nama' => 'Sales/Marketing'],
        ];
        $departemen = array_map(function ($r) use ($now) {
            return $r + [
                'id'         => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $departemen);

        Departemen::query()->insert($departemen);
        return Departemen::all();
    }

    public static function isiGroup() {
        $now= Carbon::now();
        $group = [
            ['nama' => 'Teknisi Group A'],
            ['nama' => 'Teknisi Group B'],
            ['nama' => 'Teknisi Group C'],
            ['nama' => 'Teknisi Group D'],
            ['nama' => 'Finance'],
            ['nama' => 'HRD'],
            ['nama' => 'Sales/Marketing'],
        ];
        $group = array_map(function ($r) use ($now) {
            return $r + [
                'id'         => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $group);

        Group::query()->insert($group);
        return Group::all();
    }

    public static function isiPegawai() {
        $now = Carbon::now();
        $pegawai = [
            [
                'nama' => 'Andi',
                'email' => 'andi@example.com',
                'group_id' => 'e6fe16ea-ebac-4d2e-9f4f-b5c15706c724',
                'alamat' => 'Jl. Andi No. 1',
                'hp' => '081234567890',
                'departemen_id' => '1610b297-df74-42ae-9ca0-469cd3e17b1e',
            ],
            [
                'nama' => 'Budi',
                'email' => 'budi@example.com',
                'group_id' => 'e6fe16ea-ebac-4d2e-9f4f-b5c15706c724',
                'departemen_id' => '1610b297-df74-42ae-9ca0-469cd3e17b1e',
                'alamat' => 'Jl. Budi No. 2',
                'hp' => '081234567891',
            ],
            [
                'nama' => 'Cici',
                'email' => 'cici@example.com',
                'group_id' => '620e4e4a-caf3-4118-9da0-08e967c469ac',
                'departemen_id' => '1610b297-df74-42ae-9ca0-469cd3e17b1e',
                'alamat' => 'Jl. Cici No. 3',
                'hp' => '081234567892',
            ],
            [
                'nama' => 'Parto',
                'email' => 'parto@example.com',
                'group_id' => '620e4e4a-caf3-4118-9da0-08e967c469ac',
                'departemen_id' => '1610b297-df74-42ae-9ca0-469cd3e17b1e',
                'alamat' => 'Jl. Parto No. 4',
                'hp' => '081234567893',
            ],
            [
                'nama' => 'Bambang',
                'email' => 'bambang@example.com',
                'group_id' => 'e107661f-a4cb-4e74-8024-c7f6e5a06fe6',
                'departemen_id' => '1610b297-df74-42ae-9ca0-469cd3e17b1e',
                'alamat' => 'Jl. Bambang No. 5',
                'hp' => '081234567894',
            ],
            [
                'nama' => 'Sadiran',
                'email' => 'sadiran@example.com',
                'group_id' => 'e107661f-a4cb-4e74-8024-c7f6e5a06fe6',
                'departemen_id' => '1610b297-df74-42ae-9ca0-469cd3e17b1e',
                'alamat' => 'Jl. Sadiran No. 4',
                'hp' => '081234567893',
            ],
            [
                'nama' => 'Danang',
                'email' => 'danang@example.com',
                'group_id' => '694e2383-96b8-40c1-9c1a-599dcaa3887e',
                'departemen_id' => '1610b297-df74-42ae-9ca0-469cd3e17b1e',
                'alamat' => 'Jl. Danang No. 5',
                'hp' => '0812345237897',
            ],
            [
                'nama' => 'Atmajaya',
                'email' => 'atmajaya@example.com',
                'group_id' => '694e2383-96b8-40c1-9c1a-599dcaa3887e',
                'departemen_id' => '1610b297-df74-42ae-9ca0-469cd3e17b1e',
                'alamat' => 'Jl. Atmajaya No. 4',
                'hp' => '081234562293',
            ],
            [
                'nama' => 'Dodi',
                'email' => 'dodi@example.com',
                'group_id' => '3cf66ee2-f08f-49b7-bf9b-4a89c3cfac21',
                'departemen_id' => 'ac49343f-b616-4a05-b4b3-d399b7419c15',
                'alamat' => 'Jl. Dodi No. 4',
                'hp' => '081234567893',
            ],
            [
                'nama' => 'Eka',
                'email' => 'eka@example.com',
                'group_id' => 'f6811f2d-9f98-49bc-bfa0-1fe57bb6b18f',
                'departemen_id' => '894cd210-420f-47cb-be57-f6eeb27d8622',
                'alamat' => 'Jl. Eka No. 5',
                'hp' => '081234567894',
            ],
            [
                'nama' => 'Fajar',
                'email' => 'fajar@example.com',
                'group_id' => '620e4e4a-caf3-4118-9da0-08e967c469ac',
                'departemen_id' => '1610b297-df74-42ae-9ca0-469cd3e17b1e',
                'alamat' => 'Jl. Fajar No. 6',
                'hp' => '081234567895',
            ],
        ];
        $pegawai = array_map(function ($r) use ($now) {
            return $r + [
                'id'         => (string) Str::uuid(),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $pegawai);

        Pegawai::query()->insert($pegawai);
        return Pegawai::all();
    }

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
