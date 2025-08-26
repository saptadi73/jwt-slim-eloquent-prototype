<?php

require __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

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

/*
|----------------------------------------------------------------------
| 1) MASTER / PARENT TABLES (tanpa ketergantungan)
|----------------------------------------------------------------------
*/

// customers
Capsule::schema()->dropIfExists('customers');
Capsule::schema()->create('customers', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama');
    $table->string('alamat');
    $table->string('hp');
    $table->string('lokasi')->nullable();
    $table->string('brand')->nullable();
    $table->string('model')->nullable();
    $table->string('freon')->nullable();
    $table->string('kapasitas')->nullable();
    $table->timestamps();
});
echo "Tabel customers berhasil dibuat!\n";

// groups
Capsule::schema()->dropIfExists('groups');
Capsule::schema()->create('groups', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama')->nullable();
    $table->timestamps();
});
echo "Tabel groups berhasil dibuat!\n";

// kategori
Capsule::schema()->dropIfExists('kategori');
Capsule::schema()->create('kategori', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama')->nullable();
    $table->timestamps();
});
echo "Tabel kategori berhasil dibuat!\n";

// products
Capsule::schema()->dropIfExists('products');
Capsule::schema()->create('products', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama');
    $table->string('satuan');
    $table->string('deskripsi')->nullable();
    $table->string('kode')->nullable();
    $table->string('type'); // 'jasa' atau 'barang'
    $table->bigInteger('harga')->nullable();
    $table->integer('stok')->nullable();
    $table->string('brand')->nullable();
    $table->string('model')->nullable();

    $table->uuid('kategori_id')->nullable();
    $table->foreign('kategori_id')->references('id')->on('kategori')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel products berhasil dibuat!\n";

// departemen
Capsule::schema()->dropIfExists('departemen');
Capsule::schema()->create('departemen', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama')->nullable();
    $table->timestamps();
});
echo "Tabel departemen berhasil dibuat!\n";

// pegawai
Capsule::schema()->dropIfExists('pegawai');
Capsule::schema()->create('pegawai', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama');
    $table->string('alamat');
    $table->string('hp');

    $table->uuid('departemen_id')->nullable();
    $table->foreign('departemen_id')->references('id')->on('departemen')->onDelete('set null');

    $table->uuid('group_id')->nullable();
    $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel pegawai berhasil dibuat!\n";

/*
|----------------------------------------------------------------------
| 2) CHILD TABLES (memiliki FK ke tabel di atas)
|----------------------------------------------------------------------
*/

// workorders
Capsule::schema()->dropIfExists('workorders');
Capsule::schema()->create('workorders', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nowo')->unique();
    $table->date('tanggal');
    $table->string('keluhan');
    $table->string('pengecekan')->nullable();
    $table->string('service')->nullable();
    $table->string('tambahfreon')->nullable();
    $table->string('thermis')->nullable();
    $table->string('bongkar')->nullable();
    $table->string('pasang')->nullable();
    $table->string('bongkarpasang')->nullable();
    $table->string('perbaikan')->nullable();
    $table->string('hasil')->nullable();

    $table->uuid('customer_id');
    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

    $table->uuid('group_id')->nullable();
    $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel workorders berhasil dibuat!\n";

// biayaworkorders
Capsule::schema()->dropIfExists('biayaworkorders');
Capsule::schema()->create('biayaworkorders', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->integer('jumlah')->nullable();
    $table->bigInteger('harga')->nullable();
    $table->bigInteger('total')->nullable();

    $table->uuid('workorder_id');
    $table->foreign('workorder_id')->references('id')->on('workorders')->onDelete('cascade');

    $table->uuid('product_id')->nullable();
    $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel biayaworkorders berhasil dibuat!\n";

// absen
Capsule::schema()->dropIfExists('absen');
Capsule::schema()->create('absen', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('tanggal');

    $table->uuid('pegawai_id')->nullable();
    $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel absen berhasil dibuat!\n";

// cuti
Capsule::schema()->dropIfExists('cuti');
Capsule::schema()->create('cuti', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('tanggal_start');
    $table->date('tanggal_end');
    $table->string('alasan')->nullable();

    $table->uuid('pegawai_id')->nullable();
    $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel cuti berhasil dibuat!\n";

// lembur
Capsule::schema()->dropIfExists('lembur');
Capsule::schema()->create('lembur', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('tanggal');
    $table->time('jam_mulai')->nullable();
    $table->time('jam_selesai')->nullable();
    $table->string('keterangan')->nullable();

    $table->uuid('pegawai_id')->nullable();
    $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel lembur berhasil dibuat!\n";

// ijin
Capsule::schema()->dropIfExists('ijin');
Capsule::schema()->create('ijin', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('tanggal');
    $table->time('jam_mulai')->nullable();
    $table->time('jam_selesai')->nullable();
    $table->string('keterangan')->nullable();

    $table->uuid('pegawai_id')->nullable();
    $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel ijin berhasil dibuat!\n";

// gaji
Capsule::schema()->dropIfExists('gaji');
Capsule::schema()->create('gaji', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('periode');
    $table->bigInteger('jumlah')->nullable();

    $table->uuid('pegawai_id')->nullable();
    $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel gaji berhasil dibuat!\n";

// jatah_cuti
Capsule::schema()->dropIfExists('jatah_cuti');
Capsule::schema()->create('jatah_cuti', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('periode');
    $table->bigInteger('jumlah')->nullable();

    $table->uuid('pegawai_id')->nullable();
    $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel jatah_cuti berhasil dibuat!\n";
