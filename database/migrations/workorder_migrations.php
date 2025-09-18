<?php

/**
 * database_setup.php
 * Rebuild skema: drop dengan aman, lalu create semua tabel dengan FK rapi.
 */

require __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


// Koneksi ke MySQL (default, dikomentari)
/*
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'erpmini',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
*/

// Koneksi ke PostgreSQL (aktif, gunakan ENV)
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => '127.0.0.1',
    'database'  => 'erpmini',
    'username'  => 'openpg',
    'password'  => 'openpgpwd',
    'charset'   => 'utf8',
    'prefix'    => '',
    'schema'    => 'public',
    'port'      => 5432,
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

date_default_timezone_set('Asia/Jakarta');

// DROP SEMUA TABEL CHILD DULU, BARU PARENT
Capsule::schema()->dropIfExists('workorder_service');
Capsule::schema()->dropIfExists('workorder_penjualan');
Capsule::schema()->dropIfExists('workorder_penyewaan');
Capsule::schema()->dropIfExists('workorders');

// ...tabel lain yang punya FK ke workorders juga didrop sebelum workorders...

// CREATE PARENT DULU, BARU CHILD
Capsule::schema()->create('workorders', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nowo')->unique();
    $table->date('tanggal');
    $table->string('jenis')->nullable();
    $table->timestamps();
});
echo "Tabel workorders dibuat.\n";

Capsule::schema()->create('workorder_service', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->uuid('customer_asset_id');
    $table->uuid('teknisi_id')->nullable();
    $table->string('keluhan')->nullable();
    $table->string('keterangan')->nullable();
    $table->string('pengecekan')->nullable();
    $table->string('service')->nullable();
    $table->string('tambah_freon')->nullable();
    $table->string('isi_freon')->nullable();
    $table->string('bongkar')->nullable();
    $table->string('pasang')->nullable();
    $table->string('bongkar_pasang')->nullable();
    $table->string('perbaikan')->nullable();
    $table->boolean('check_evaporator')->default(false);
    $table->string('keterangan_evaporator')->nullable();
    $table->boolean('check_fan_indoor')->default(false);
    $table->string('keterangan_fan_indoor')->nullable();
    $table->boolean('check_swing')->default(false);
    $table->string('keterangan_swing')->nullable();
    $table->boolean('check_tegangan_input')->default(false);
    $table->string('keterangan_tegangan_input')->nullable();
    $table->boolean('check_thermis')->default(false);
    $table->string('keterangan_thermis')->nullable();
    $table->boolean('check_temperatur_indoor')->default(false);
    $table->string('keterangan_temperatur_indoor')->nullable();
    $table->boolean('check_lain_indoor')->default(false);
    $table->string('keterangan_lain_indoor')->nullable();
    $table->boolean('check_kondensor')->default(false);
    $table->string('keterangan_kondensor')->nullable();
    $table->boolean('check_fan_outdoor')->default(false);
    $table->string('keterangan_fan_outdoor')->nullable();
    $table->boolean('check_kapasitor')->default(false);
    $table->string('keterangan_kapasitor')->nullable();
    $table->boolean('check_tekanan_freon')->default(false);
    $table->string('keterangan_tekanan_freon')->nullable();
    $table->boolean('check_arus')->default(false);
    $table->string('keterangan_arus')->nullable();
    $table->boolean('check_temperatur_outdoor')->default(false);
    $table->string('keterangan_temperatur_outdoor')->nullable();
    $table->boolean('check_lain_outdoor')->default(false);
    $table->string('keterangan_lain_outdoor')->nullable();
    $table->string('hasil_pekerjaan')->nullable();
    $table->string('tanda_tangan_pelanggan')->nullable();
    $table->string('status')->nullable()->default('open');
    $table->uuid('workorder_id')->nullable();
    $table->timestamps();

    // Foreign key untuk customer
    $table->foreign('customer_asset_id')->references('id')->on('customer_assets')->onDelete('cascade');
    $table->foreign('teknisi_id')->references('id')->on('pegawai')->onDelete('cascade');
    $table->foreign('workorder_id')->references('id')->on('workorders')->onDelete('cascade');
}); // Hapus tabel jika ada, untuk rebuild
echo "Tabel workorder_service dibuat.\n";

Capsule::schema()->create('workorder_penjualan', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->uuid('customer_asset_id');
    $table->uuid('teknisi_id')->nullable();
    $table->boolean('check_indoor')->default(false);
    $table->string('keterangan_indoor')->nullable();
    $table->boolean('check_outdoor')->default(false);
    $table->string('keterangan_outdoor')->nullable();
    $table->boolean('check_pipa')->default(false);
    $table->string('keterangan_pipa')->nullable();
    $table->boolean('check_selang')->default(false);
    $table->string('keterangan_selang')->nullable();
    $table->boolean('check_kabel')->default(false);
    $table->string('keterangan_kabel')->nullable();
    $table->boolean('check_inst_indoor')->default(false);
    $table->string('keterangan_inst_indoor')->nullable();
    $table->boolean('check_inst_outdoor')->default(false);
    $table->string('keterangan_inst_outdoor')->nullable();
    $table->boolean('check_inst_listrik')->default(false);
    $table->string('keterangan_inst_listrik')->nullable();
    $table->boolean('check_inst_pipa')->default(false);
    $table->string('keterangan_inst_pipa')->nullable();
    $table->boolean('check_buangan')->default(false);
    $table->string('keterangan_buangan')->nullable();
    $table->boolean('check_vaccum')->default(false);
    $table->string('keterangan_vaccum')->nullable();
    $table->boolean('check_freon')->default(false);
    $table->string('keterangan_freon')->nullable();
    $table->boolean('check_arus')->default(false);
    $table->string('keterangan_arus')->nullable();
    $table->boolean('check_eva')->default(false);
    $table->string('keterangan_eva')->nullable();
    $table->boolean('check_kondensor')->default(false);
    $table->string('keterangan_kondensor')->nullable();
    $table->string('hasil_pekerjaan')->nullable();
    $table->string('tanda_tangan_pelanggan')->nullable();
    $table->string('status')->nullable()->default('open');
    $table->uuid('workorder_id')->nullable();
    $table->timestamps();

    // Foreign key untuk customer
    $table->foreign('customer_asset_id')->references('id')->on('customer_assets')->onDelete('cascade');
    $table->foreign('teknisi_id')->references('id')->on('pegawai')->onDelete('cascade');
    $table->foreign('workorder_id')->references('id')->on('workorders')->onDelete('cascade');
});
echo "Table workorder_penjualan was created.\n";

Capsule::schema()->create('workorder_penyewaan', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->uuid('rental_asset_id');
    $table->uuid('customer_id');
    $table->uuid('teknisi_id')->nullable();
    $table->string('tanda_tangan_teknisi')->nullable();
    $table->string('tanda_tangan_pelanggan')->nullable();
    $table->string('hasil_pekerjaan')->nullable();
    $table->boolean('checkIndoor')->default(false);
    $table->string('keteranganIndoor')->nullable();
    $table->boolean('checkOutdoor')->default(false);
    $table->string('keteranganOutdoor')->nullable();
    $table->boolean('checkPipa')->default(false);
    $table->string('keteranganPipa')->nullable();
    $table->boolean('checkSelang')->default(false);
    $table->string('keteranganSelang')->nullable();
    $table->boolean('checkKabel')->default(false);
    $table->string('keteranganKabel')->nullable();
    $table->boolean('checkInstIndoor')->default(false);
    $table->string('keteranganInstIndoor')->nullable();
    $table->boolean('checkInstOutdoor')->default(false);
    $table->string('keteranganInstOutdoor')->nullable();
    $table->boolean('checkInstListrik')->default(false);
    $table->string('keteranganInstListrik')->nullable();
    $table->boolean('checkInstPipa')->default(false);
    $table->string('keteranganInstPipa')->nullable();
    $table->boolean('checkBuangan')->default(false);
    $table->string('keteranganBuangan')->nullable();
    $table->boolean('checkVaccum')->default(false);
    $table->string('keteranganVaccum')->nullable();
    $table->boolean('checkFreon')->default(false);
    $table->string('keteranganFreon')->nullable();
    $table->boolean('checkArus')->default(false);
    $table->string('keteranganArus')->nullable();
    $table->boolean('checkEva')->default(false);
    $table->string('keteranganEva')->nullable();
    $table->boolean('checkKondensor')->default(false);
    $table->string('keteranganKondensor')->nullable();
    $table->boolean('checkIndoorB')->default(false);
    $table->string('keteranganIndoorB')->nullable();
    $table->boolean('checkOutdoorB')->default(false);
    $table->string('keteranganOutdoorB')->nullable();
    $table->boolean('checkPipaB')->default(false);
    $table->string('keteranganPipaB')->nullable();
    $table->boolean('checkSelangB')->default(false);
    $table->string('keteranganSelangB')->nullable();
    $table->boolean('checkKabelB')->default(false);
    $table->string('keteranganKabelB')->nullable();
    $table->string('status')->nullable()->default('open');
    $table->uuid('workorder_id')->nullable();
    $table->timestamps();

    // Foreign key untuk customer_asset_id
    $table->foreign('rental_asset_id')->references('id')->on('rental_assets')->onDelete('cascade');
    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
    // Foreign key untuk teknisi_id jika ada teknisi
    $table->foreign('teknisi_id')->references('id')->on('pegawai')->onDelete('set null');
    $table->foreign('workorder_id')->references('id')->on('workorders')->onDelete('cascade');
});
echo "Tabel workorder_penyewaan dibuat.\n";


// Drop child tables first
Capsule::schema()->dropIfExists('workorder_salejasaorderline');
Capsule::schema()->dropIfExists('workorder_salebarangorderline');
Capsule::schema()->dropIfExists('saleorder_salejasaorderline');
Capsule::schema()->dropIfExists('saleorder_salebarangorderline');
Capsule::schema()->dropIfExists('saleorderjasaline');
Capsule::schema()->dropIfExists('saleorderbarangline');

// 1. Buat tabel utama/detail dulu
Capsule::schema()->create('saleorderbarangline', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->integer('qty')->nullable();
    $table->bigInteger('harga')->nullable();
    $table->bigInteger('total')->nullable();
    $table->string('keterangan')->nullable();
    $table->uuid('product_id');
    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    $table->timestamps();
});
echo "Tabel saleorderbarangline dibuat.\n";

Capsule::schema()->create('saleorderjasaline', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->integer('qty')->nullable();
    $table->bigInteger('harga')->nullable();
    $table->bigInteger('total')->nullable();
    $table->string('keterangan')->nullable();
    $table->uuid('product_id');
    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    $table->timestamps();
});
echo "Tabel saleorderjasaline dibuat.\n";

// 2. Baru buat tabel pivot/relasi
Capsule::schema()->create('saleorder_salebarangorderline', function (Blueprint $table) {
    $table->engine = 'InnoDB';

    $table->uuid('saleorder_id');
    $table->uuid('saleorderbarangline_id');

    $table->primary(['saleorder_id', 'saleorderbarangline_id'], 'pk_so_sobarline');

    $table->foreign('saleorder_id')
        ->references('id')->on('saleorder')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->foreign('saleorderbarangline_id')
        ->references('id')->on('saleorderbarangline')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->index('saleorder_id', 'idx_so_sobarline_so');
    $table->index('saleorderbarangline_id', 'idx_so_sobarline_line');

    $table->timestamps();
});
echo "Tabel saleorder_salebarangorderline dibuat.\n";

// SaleOrder <-> SaleOrderJasaLine
Capsule::schema()->create('saleorder_salejasaorderline', function (Blueprint $table) {
    $table->engine = 'InnoDB';

    $table->uuid('saleorder_id');
    $table->uuid('saleorderjasaline_id');

    $table->primary(['saleorder_id', 'saleorderjasaline_id'], 'pk_so_sojasaline');

    $table->foreign('saleorder_id')
        ->references('id')->on('saleorder')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->foreign('saleorderjasaline_id')
        ->references('id')->on('saleorderjasaline')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->index('saleorder_id', 'idx_so_sojasaline_so');
    $table->index('saleorderjasaline_id', 'idx_so_sojasaline_line');

    $table->timestamps();
});
echo "Tabel saleorder_salejasaorderline dibuat.\n";
