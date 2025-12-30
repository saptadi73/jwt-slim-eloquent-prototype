<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create workorders table
        if (!Schema::hasTable('workorders')) {
            Schema::create('workorders', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nowo')->unique();
                $table->date('tanggal');
                $table->string('jenis')->nullable();
                $table->timestamps();

                $table->index('tanggal');
                $table->index('jenis');
            });
        }

        // Create workorder_ac_services table
        if (!Schema::hasTable('workorder_ac_services')) {
            Schema::create('workorder_ac_services', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('workorder_id');
                $table->uuid('pegawai_id')->nullable();
                $table->text('deskripsi')->nullable();
                $table->text('hasil_perbaikan')->nullable();
                $table->decimal('harga', 12, 2)->default(0);
                $table->string('status')->default('pending'); // pending, done, cancel
                $table->timestamps();

                $table->foreign('workorder_id')->references('id')->on('workorders')->onDelete('cascade');
                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('set null');
                $table->index('workorder_id');
                $table->index('pegawai_id');
            });
        }

        // Create workorder_penjualans table
        if (!Schema::hasTable('workorder_penjualans')) {
            Schema::create('workorder_penjualans', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('workorder_id');
                $table->uuid('sale_order_id')->nullable();
                $table->text('catatan')->nullable();
                $table->string('status')->default('pending'); // pending, done, cancel
                $table->timestamps();

                $table->foreign('workorder_id')->references('id')->on('workorders')->onDelete('cascade');
                $table->foreign('sale_order_id')->references('id')->on('sale_orders')->onDelete('set null');
                $table->index('workorder_id');
                $table->index('sale_order_id');
            });
        }

        // Create workorder_penyewaans table
        if (!Schema::hasTable('workorder_penyewaans')) {
            Schema::create('workorder_penyewaans', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('workorder_id');
                $table->uuid('customer_asset_id')->nullable();
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai')->nullable();
                $table->text('catatan')->nullable();
                $table->string('status')->default('pending'); // pending, ongoing, done, cancel
                $table->timestamps();

                $table->foreign('workorder_id')->references('id')->on('workorders')->onDelete('cascade');
                $table->foreign('customer_asset_id')->references('id')->on('customer_assets')->onDelete('set null');
                $table->index('workorder_id');
                $table->index('customer_asset_id');
            });
        }

        // Create workorder_salebarangorderlines table (pivot table for many-to-many)
        if (!Schema::hasTable('workorder_salebarangorderlines')) {
            Schema::create('workorder_salebarangorderlines', function (Blueprint $table) {
                $table->id();
                $table->uuid('workorder_id');
                $table->uuid('product_order_line_id');
                $table->timestamps();

                $table->foreign('workorder_id')->references('id')->on('workorders')->onDelete('cascade');
                $table->foreign('product_order_line_id')->references('id')->on('product_order_lines')->onDelete('cascade');
                $table->index('workorder_id');
                $table->index('product_order_line_id');
                $table->unique(['workorder_id', 'product_order_line_id']);
            });
        }

        // Create workorder_salejasaorderlines table (pivot table for many-to-many)
        if (!Schema::hasTable('workorder_salejasaorderlines')) {
            Schema::create('workorder_salejasaorderlines', function (Blueprint $table) {
                $table->id();
                $table->uuid('workorder_id');
                $table->uuid('service_order_line_id');
                $table->timestamps();

                $table->foreign('workorder_id')->references('id')->on('workorders')->onDelete('cascade');
                $table->foreign('service_order_line_id')->references('id')->on('service_order_lines')->onDelete('cascade');
                $table->index('workorder_id');
                $table->index('service_order_line_id');
                $table->unique(['workorder_id', 'service_order_line_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workorder_salejasaorderlines');
        Schema::dropIfExists('workorder_salebarangorderlines');
        Schema::dropIfExists('workorder_penyewaans');
        Schema::dropIfExists('workorder_penjualans');
        Schema::dropIfExists('workorder_ac_services');
        Schema::dropIfExists('workorders');
    }
};
