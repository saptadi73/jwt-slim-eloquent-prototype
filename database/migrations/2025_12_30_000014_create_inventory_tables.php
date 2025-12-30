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
        // Create rental_assets table
        if (!Schema::hasTable('rental_assets')) {
            Schema::create('rental_assets', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('customer_asset_id');
                $table->uuid('sale_order_id')->nullable();
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai')->nullable();
                $table->decimal('harga_sewa_hari', 12, 2)->default(0);
                $table->decimal('total_harga', 12, 2)->default(0);
                $table->string('status')->default('active'); // active, returned, cancelled
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->foreign('customer_asset_id')->references('id')->on('customer_assets')->onDelete('cascade');
                $table->foreign('sale_order_id')->references('id')->on('sale_orders')->onDelete('set null');
                $table->index('tanggal_mulai');
                $table->index('status');
                $table->index('customer_asset_id');
            });
        }

        // Create product_move_histories table
        if (!Schema::hasTable('product_move_histories')) {
            Schema::create('product_move_histories', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('product_id');
                $table->enum('tipe_pergerakan', ['in', 'out', 'adjustment']); // Tipe: masuk, keluar, atau adjustment
                $table->integer('quantity');
                $table->decimal('harga_satuan', 12, 2)->default(0);
                $table->decimal('total_harga', 15, 2)->default(0);
                $table->string('keterangan')->nullable(); // e.g., dari PO, ke SO, stock opname
                $table->uuid('referensi_id')->nullable(); // ID dari Purchase Order / Sale Order
                $table->string('referensi_tipe')->nullable(); // e.g., purchase_order, sale_order
                $table->timestamps();

                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->index('product_id');
                $table->index('tipe_pergerakan');
                $table->index('created_at');
            });
        }

        // Create stock_histories table
        if (!Schema::hasTable('stock_histories')) {
            Schema::create('stock_histories', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('product_id');
                $table->integer('stok_sebelum');
                $table->integer('stok_sesudah');
                $table->integer('selisih');
                $table->enum('tipe', ['in', 'out', 'adjustment', 'correction']); // in = masuk, out = keluar, adjustment = penyesuaian, correction = koreksi
                $table->string('referensi')->nullable(); // PO number, SO number, etc.
                $table->text('alasan')->nullable();
                $table->uuid('created_by');
                $table->timestamps();

                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
                $table->index('product_id');
                $table->index('tipe');
                $table->index('created_at');
            });
        }

        // Create manual_transfers table
        if (!Schema::hasTable('manual_transfers')) {
            Schema::create('manual_transfers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nomor_transfer')->unique();
                $table->date('tanggal_transfer');
                $table->uuid('dari_pegawai_id');
                $table->uuid('ke_pegawai_id')->nullable();
                $table->text('catatan')->nullable();
                $table->string('status')->default('pending'); // pending, approved, rejected
                $table->uuid('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();

                $table->foreign('dari_pegawai_id')->references('id')->on('pegawai')->onDelete('restrict');
                $table->foreign('ke_pegawai_id')->references('id')->on('pegawai')->onDelete('set null');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                $table->index('tanggal_transfer');
                $table->index('status');
                $table->index('dari_pegawai_id');
            });
        }

        // Create manual_transfer_details table (line items)
        if (!Schema::hasTable('manual_transfer_details')) {
            Schema::create('manual_transfer_details', function (Blueprint $table) {
                $table->id();
                $table->uuid('manual_transfer_id');
                $table->uuid('product_id');
                $table->integer('quantity');
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->foreign('manual_transfer_id')->references('id')->on('manual_transfers')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
                $table->index('manual_transfer_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_transfer_details');
        Schema::dropIfExists('manual_transfers');
        Schema::dropIfExists('stock_histories');
        Schema::dropIfExists('product_move_histories');
        Schema::dropIfExists('rental_assets');
    }
};
