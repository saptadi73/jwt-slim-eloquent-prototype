
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

    $table->uuid('brand_id');
    $table->foreign('brand_id')->references('id')->on('brand')->onDelete('cascade');

    $table->string('model')->nullable();
    $table->string('freon')->nullable();
    $table->string('kapasitas')->nullable();

    $table->uuid('customer_id');
    $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

    $table->timestamps();
});
echo "Tabel rental_assets dibuat.\n";