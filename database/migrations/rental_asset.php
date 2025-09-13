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
Capsule::schema()->dropIfExists('rental_assets'); // Hapus tabel jika ada, untuk rebuild
Capsule::schema()->create('rental_assets', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();

    $table->uuid('tipe_id');
    $table->foreign('tipe_id')->references('id')->on('tipe')->onDelete('cascade');

    $table->string('keterangan')->nullable();
    $table->string('gambar')->nullable();
    $table->string('lokasi')->nullable();
    $table->string('status')->nullable(); // aktif, non-aktif
    $table->bigInteger('harga_perolehan')->nullable();
    $table->bigInteger('harga_sewa')->nullable();
    $table->bigInteger('sisa_harga_sekarang')->nullable();

    $table->uuid('brand_id');
    $table->foreign('brand_id')->references('id')->on('brand')->onDelete('cascade');

    $table->string('model')->nullable();
    $table->string('freon')->nullable();
    $table->string('kapasitas')->nullable();

    $table->timestamps();
});
echo "Tabel rental_assets dibuat.\n";

Capsule::schema()->dropIfExists('workorder_service');
Capsule::schema()->create('workorder_service', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->uuid('customer_asset_id');
    $table->uuid('teknisi_id')->nullable();
    $table->string('keluhan');
    $table->string('keterangan');
    $table->string('pengecekan');
    $table->string('service');
    $table->boolean('tambah_freon')->default(false);
    $table->boolean('isi_freon')->default(false);
    $table->boolean('bongkar')->default(false);
    $table->boolean('pasang')->default(false);
    $table->boolean('bongkar_pasang')->default(false);
    $table->boolean('perbaikan')->default(false);
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
    $table->timestamps();

    // Foreign key untuk customer
    $table->foreign('customer_asset_id')->references('id')->on('customer_assets')->onDelete('cascade');
    $table->foreign('teknisi_id')->references('id')->on('pegawai')->onDelete('cascade');
}); // Hapus tabel jika ada, untuk rebuild
echo "Tabel workorder_service dibuat.\n";

Capsule::schema()->dropIfExists('workorder_penjualan');
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
    $table->timestamps();

    // Foreign key untuk customer
    $table->foreign('customer_asset_id')->references('id')->on('customer_assets')->onDelete('cascade');
    $table->foreign('teknisi_id')->references('id')->on('pegawai')->onDelete('cascade');
});
echo "Workorder Penjualan was created.\n";

Capsule::schema()->dropIfExists('workorder_penyewaan');
Capsule::schema()->create('workorder_penyewaan', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->uuid('customer_asset_id');
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
    $table->string('status')->nullable()->default('open');
    $table->timestamps();

    // Foreign key untuk customer_asset_id
    $table->foreign('customer_asset_id')->references('id')->on('customer_assets')->onDelete('cascade');
    // Foreign key untuk teknisi_id jika ada teknisi
    $table->foreign('teknisi_id')->references('id')->on('pegawai')->onDelete('set null');
});
echo "Tabel workorder_penyewaan dibuat.\n";
