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
        // Create absen (attendance legacy) table if not exists
        if (!Schema::hasTable('absen')) {
            Schema::create('absen', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->date('tanggal');
                $table->uuid('pegawai_id');
                $table->timestamps();

                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
                $table->index(['pegawai_id', 'tanggal']);
            });
        }

        // Create cuti (leave) table if not exists
        if (!Schema::hasTable('cuti')) {
            Schema::create('cuti', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->date('tanggal_start');
                $table->date('tanggal_end');
                $table->text('alasan')->nullable();
                $table->uuid('pegawai_id');
                $table->timestamps();

                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
                $table->index(['pegawai_id', 'tanggal_start']);
            });
        }

        // Create ijin (permission) table if not exists
        if (!Schema::hasTable('ijin')) {
            Schema::create('ijin', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->date('tanggal');
                $table->text('alasan')->nullable();
                $table->uuid('pegawai_id');
                $table->timestamps();

                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
                $table->index(['pegawai_id', 'tanggal']);
            });
        }

        // Create lembur (overtime) table if not exists
        if (!Schema::hasTable('lembur')) {
            Schema::create('lembur', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->date('tanggal');
                $table->decimal('jam', 5, 2)->nullable();
                $table->text('keterangan')->nullable();
                $table->uuid('pegawai_id');
                $table->timestamps();

                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
                $table->index(['pegawai_id', 'tanggal']);
            });
        }

        // Create gaji (salary) table if not exists
        if (!Schema::hasTable('gaji')) {
            Schema::create('gaji', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->date('tanggal_gaji');
                $table->decimal('jumlah', 12, 2)->nullable();
                $table->text('keterangan')->nullable();
                $table->uuid('pegawai_id');
                $table->timestamps();

                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
                $table->index(['pegawai_id', 'tanggal_gaji']);
            });
        }

        // Create jatah_cuti (leave quota) table if not exists
        if (!Schema::hasTable('jatah_cuti')) {
            Schema::create('jatah_cuti', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->integer('tahun');
                $table->integer('jumlah_hari')->default(0);
                $table->integer('dipakai')->default(0);
                $table->integer('sisa')->default(0);
                $table->uuid('pegawai_id');
                $table->timestamps();

                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
                $table->unique(['pegawai_id', 'tahun']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jatah_cuti');
        Schema::dropIfExists('gaji');
        Schema::dropIfExists('lembur');
        Schema::dropIfExists('ijin');
        Schema::dropIfExists('cuti');
        Schema::dropIfExists('absen');
    }
};
