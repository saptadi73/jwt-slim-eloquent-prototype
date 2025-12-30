<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create customers table if not exists
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('kode_pelanggan')->unique();
                $table->string('nama');
                $table->text('alamat')->nullable();
                $table->string('hp')->nullable();
                $table->string('email')->nullable();
                $table->string('gambar')->nullable();
                $table->enum('jenis', ['individu', 'perusahaan'])->default('perusahaan');
                $table->timestamps();

                $table->index('kode_pelanggan');
            });
        }

        // Create vendors table if not exists
        if (!Schema::hasTable('vendors')) {
            Schema::create('vendors', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama');
                $table->text('alamat')->nullable();
                $table->string('hp')->nullable();
                $table->string('email')->nullable();
                $table->string('gambar')->nullable();
                $table->timestamps();
            });
        }

        // Create kategoris table if not exists
        if (!Schema::hasTable('kategoris')) {
            Schema::create('kategoris', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama');
                $table->text('deskripsi')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create satuans table if not exists
        if (!Schema::hasTable('satuans')) {
            Schema::create('satuans', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama');
                $table->text('deskripsi')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create brands table if not exists
        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama');
                $table->text('deskripsi')->nullable();
                $table->string('logo')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create tipes table if not exists
        if (!Schema::hasTable('tipes')) {
            Schema::create('tipes', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama');
                $table->text('deskripsi')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create services table if not exists
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama');
                $table->text('deskripsi')->nullable();
                $table->decimal('harga', 12, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('tipes');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('satuans');
        Schema::dropIfExists('kategoris');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('customers');
    }
};
