<?php

/**
 * database_setup.php
 * Rebuild skema: drop dengan aman, lalu create semua tabel dengan FK rapi.
 */

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

/* -------------------------------------------------------------
| 0) DROP SEMUA TABEL DENGAN AMAN
|    Matikan FK checks, drop dari tabel paling dependen → induk
| ------------------------------------------------------------- */
Capsule::statement('SET FOREIGN_KEY_CHECKS=0');

$tables = [
    // Pivot
    'workorder_salebarangorderline',
    'workorder_salejasaorderline',
    'saleorder_salebarangorderline',
    'saleorder_salejasaorderline',
    // Child transaksi
    'saleorderbarangline',
    'saleorderjasaline',
    'workorders',
    'customer_assets',
    'saleorder',
    // HR
    'absen',
    'cuti',
    'lembur',
    'ijin',
    'gaji',
    'jatah_cuti',
    // Master dengan FK keluar
    'products',
    'pegawai',
    // Master dasar
    'departemen',
    'kategori',
    'groups',
    'satuan',
    'customers',
    'vendors',
    'purchaseorder',
    'purchaseorderbarangline',
    'purchaseorderjasaline',
    'stock_history',
    'manual_transfer',
    'checklist',
    'checklist_template',
    'coa',
    'jurnal',
    'expense',
];

foreach ($tables as $t) {
    Capsule::schema()->dropIfExists($t);
}

Capsule::statement('SET FOREIGN_KEY_CHECKS=1');

echo "Semua tabel lama dihapus (jika ada).\n";

/* -------------------------------------------------------------
| 1) MASTER / PARENT TABLES (tanpa ketergantungan)
| ------------------------------------------------------------- */

// customers
Capsule::schema()->create('customers', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('kode_pelanggan')->unique();
    $table->string('nama');
    $table->string('alamat');
    $table->string('email')->unique()->nullable();
    $table->string('gambar')->nullable();
    $table->string('hp');
    $table->timestamps();
});
echo "Tabel customers dibuat.\n";

// vendors
Capsule::schema()->create('vendors', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama');
    $table->string('alamat');
    $table->string('email')->unique()->nullable();
    $table->string('gambar')->nullable();
    $table->string('hp');
    $table->timestamps();
});
echo "Tabel vendors dibuat.\n";

// satuan (milik product)
Capsule::schema()->create('satuan', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama')->nullable();
    $table->timestamps();
});
echo "Tabel satuan dibuat.\n";

// groups
Capsule::schema()->create('groups', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama')->nullable();
    $table->timestamps();
});
echo "Tabel groups dibuat.\n";

// kategori
Capsule::schema()->create('kategori', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama')->nullable();
    $table->timestamps();
});
echo "Tabel kategori dibuat.\n";

// products
Capsule::schema()->create('products', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama');
    $table->string('gambar')->nullable();
    $table->string('deskripsi')->nullable();
    $table->string('kode')->nullable();
    $table->string('tipe'); // 'jasa' atau 'barang'
    $table->bigInteger('harga')->nullable();
    $table->bigInteger('hpp')->nullable();
    $table->integer('stok')->nullable();
    $table->string('brand')->nullable();
    $table->string('model')->nullable();

    $table->uuid('kategori_id')->nullable();
    $table->foreign('kategori_id')->references('id')->on('kategori')->onDelete('set null');

    // FIX: kolom harus nullable jika onDelete set null
    $table->uuid('satuan')->nullable();
    $table->foreign('satuan')->references('id')->on('satuan')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel products dibuat.\n";

// departemen
Capsule::schema()->create('departemen', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nama')->nullable();
    $table->timestamps();
});
echo "Tabel departemen dibuat.\n";

// pegawai
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
echo "Tabel pegawai dibuat.\n";

/* -------------------------------------------------------------
| 2) CHILD TABLES (memiliki FK ke tabel di atas)
| ------------------------------------------------------------- */

// asset milik Customer
Capsule::schema()->create('customer_assets', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('tipe');
    $table->string('keterangan')->nullable();
    $table->string('gambar')->nullable();
    $table->string('lokasi')->nullable();
    $table->string('brand');
    $table->string('model')->nullable();
    $table->string('freon')->nullable();
    $table->string('kapasitas')->nullable();

    $table->uuid('customer_id');
    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

    $table->timestamps();
});
echo "Tabel customer_assets dibuat.\n";

// workorders
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
echo "Tabel workorders dibuat.\n";

/*------------------------------------------------------------
Check List Work Order
-------------------------------------------------------------*/

/*------------------------------------------------------------
Check List Work Order
-------------------------------------------------------------*/

// 1) Buat template dulu
Capsule::schema()->create('checklist_template', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->integer('no_urut');
    $table->string('kode_checklist')->unique();
    $table->string('title')->nullable();
    $table->text('checklist')->nullable();
    $table->string('pic')->nullable();
    $table->string('jenis_workorder')->nullable();
    $table->timestamps();
});
echo "Tabel checklist_template dibuat.\n";

// 2) Baru tabel checklist yang punya FK ke template
Capsule::schema()->create('checklist', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->boolean('jawaban');
    $table->string('keterangan')->nullable();

    $table->uuid('workorder_id');
    $table->foreign('workorder_id')->references('id')->on('workorders')->onDelete('cascade');

    $table->uuid('pegawai_id')->nullable();
    $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');

    // ganti nama biar jelas
    $table->uuid('checklist_template_id')->nullable();
    $table->foreign('checklist_template_id')
        ->references('id')->on('checklist_template')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel checklist dibuat.\n";

// saleorder (header)
Capsule::schema()->create('saleorder', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('noso')->unique();
    $table->date('tanggal');
    $table->bigInteger('total')->nullable();
    $table->bigInteger('diskon')->nullable();
    $table->bigInteger('grandtotal')->nullable();
    $table->string('status')->nullable(); // received, pending
    $table->string('bukti')->nullable();

    $table->uuid('customer_id');
    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

    $table->timestamps();
});
echo "Tabel saleorder dibuat.\n";

//Expense
Capsule::schema()->create('expense', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('tanggal');
    $table->string('nomor')->unique();
    $table->bigInteger('jumlah')->nullable();
    $table->string('keterangan')->nullable();
    $table->string('status')->nullable(); // received, pending
    $table->string('bukti')->nullable();

    $table->uuid('product_id');
    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

    $table->timestamps();
});
echo "Tabel expense dibuat.\n";

// saleorderbarangline (detail barang)
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

// saleorderjasaline (detail jasa)
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

/*--------------------------------------------------------------
Purchase Order
---------------------------------------------------------------*/
// saleorder (header)
Capsule::schema()->create('purchaseorder', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('nopo')->unique();
    $table->date('tanggal');
    $table->bigInteger('total')->nullable();
    $table->bigInteger('diskon')->nullable();
    $table->bigInteger('grandtotal')->nullable();
    $table->string('status')->nullable(); // received, pending
    $table->string('bukti')->nullable();

    $table->uuid('vendor_id');
    $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');

    $table->timestamps();
});
echo "Tabel purchaseorder dibuat.\n";

// purchaseorderbarangline (detail barang)
Capsule::schema()->create('purchaseorderbarangline', function (Blueprint $table) {
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
echo "Tabel purchaseorderbarangline dibuat.\n";

// purchaseorderjasaline (detail jasa)
Capsule::schema()->create('purchaseorderjasaline', function (Blueprint $table) {
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
echo "Tabel purchaseorderjasaline dibuat.\n";

Capsule::schema()->create('stock_history', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();

    $table->integer('qty');
    $table->string('satuan');
    $table->string('type');  // 'penjualan', 'pembelian','manual keluar','manual masuk'
    $table->uuid('order_id')->nullable();
    $table->timestamps();

    $table->uuid('product_id');
    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
});
echo "Tabel stock_history dibuat.\n";

Capsule::schema()->create('manual_transfer', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('tanggal');
    $table->integer('qty');
    $table->string('satuan');
    $table->string('keterangan')->nullable();
    $table->string('type');  // 'masuk' atau 'keluar'
    $table->timestamps();

    $table->uuid('product_id');
    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
});
echo "Tabel manual_transfer dibuat.\n";

/* -------------------------------------------------------------
| 3) PIVOT TABLES (many-to-many)
| ------------------------------------------------------------- */

// WO <-> SaleOrderBarangLine
Capsule::schema()->create('workorder_salebarangorderline', function (Blueprint $table) {
    $table->engine = 'InnoDB';

    $table->uuid('workorder_id');
    $table->uuid('saleorderbarangline_id');

    $table->primary(['workorder_id', 'saleorderbarangline_id'], 'pk_wo_sobarline');

    $table->foreign('workorder_id')
        ->references('id')->on('workorders')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->foreign('saleorderbarangline_id')
        ->references('id')->on('saleorderbarangline')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->index('workorder_id', 'idx_wo_sobarline_wo');
    $table->index('saleorderbarangline_id', 'idx_wo_sobarline_line');

    $table->timestamps();
});
echo "Tabel workorder_salebarangorderline dibuat.\n";

// WO <-> SaleOrderJasaLine
Capsule::schema()->create('workorder_salejasaorderline', function (Blueprint $table) {
    $table->engine = 'InnoDB';

    $table->uuid('workorder_id');
    $table->uuid('saleorderjasaline_id');

    $table->primary(['workorder_id', 'saleorderjasaline_id'], 'pk_wo_sojasaline');

    $table->foreign('workorder_id')
        ->references('id')->on('workorders')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->foreign('saleorderjasaline_id')
        ->references('id')->on('saleorderjasaline')
        ->onDelete('cascade')->onUpdate('cascade');

    $table->index('workorder_id', 'idx_wo_sojasaline_wo');
    $table->index('saleorderjasaline_id', 'idx_wo_sojasaline_line');

    $table->timestamps();
});
echo "Tabel workorder_salejasaorderline dibuat.\n";

// SaleOrder <-> SaleOrderBarangLine
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



/* -------------------------------------------------------------
| 4) HR TABLES
| ------------------------------------------------------------- */

// absen
Capsule::schema()->create('absen', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('tanggal');

    $table->uuid('pegawai_id')->nullable();
    $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel absen dibuat.\n";

// cuti
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
echo "Tabel cuti dibuat.\n";

// lembur
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
echo "Tabel lembur dibuat.\n";

// ijin
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
echo "Tabel ijin dibuat.\n";

// gaji
Capsule::schema()->create('gaji', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('periode');
    $table->bigInteger('jumlah')->nullable();

    $table->uuid('pegawai_id')->nullable();
    $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel gaji dibuat.\n";

// jatah_cuti
Capsule::schema()->create('jatah_cuti', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('periode');
    $table->bigInteger('jumlah')->nullable();

    $table->uuid('pegawai_id')->nullable();
    $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');

    $table->timestamps();
});
echo "Tabel jatah_cuti dibuat.\n";

/*--------------------------------------------------------------
Accounting
----------------------------------------------------------------*/

//Table Chart of Account
Capsule::schema()->create('coa', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->string('kode')->unique();
    $table->string('nama');
    $table->string('tipe'); // Asset, Liability, Equity, Revenue, Expense
    $table->string('kategori')->nullable(); // Current Asset, Fixed Asset, Current Liability, Long-term Liability, dll
    $table->timestamps();
});
echo "Tabel coa dibuat.\n";

Capsule::schema()->create('jurnal', function (Blueprint $table) {
    $table->engine = 'InnoDB';
    $table->uuid('id')->primary();
    $table->date('tanggal');
    $table->string('keterangan')->nullable();
    $table->bigInteger('debit')->nullable();
    $table->bigInteger('kredit')->nullable();

    $table->uuid('account_id')->nullable();
    $table->foreign('account_id')
        ->references('id')->on('coa')
        ->onDelete('set null');

    $table->uuid('vendor_id')->nullable();
    $table->foreign('vendor_id')
        ->references('id')->on('vendors')   // <— perbaiki: vendors
        ->onDelete('set null');

    $table->uuid('customer_id')->nullable();
    $table->foreign('customer_id')
        ->references('id')->on('customers') // <— perbaiki: customers
        ->onDelete('set null');

    $table->timestamps();
});

echo "Tabel jurnal dibuat.\n";

echo "Selesai. Skema database siap dipakai.\n";
