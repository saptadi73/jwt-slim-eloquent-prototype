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
        // Create products table if not exists
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('kode')->unique();
                $table->string('nama');
                $table->text('deskripsi')->nullable();
                $table->uuid('kategori_id')->nullable();
                $table->uuid('satuan_id')->nullable();
                $table->uuid('brand_id')->nullable();
                $table->string('model')->nullable();
                $table->string('tipe')->nullable();
                $table->decimal('harga', 12, 2)->nullable();
                $table->decimal('hpp', 12, 2)->nullable();
                $table->integer('stok')->default(0);
                $table->boolean('is_sealable')->default(false);
                $table->string('gambar')->nullable();
                $table->timestamps();

                $table->foreign('kategori_id')->references('id')->on('kategoris')->onDelete('set null');
                $table->foreign('satuan_id')->references('id')->on('satuans')->onDelete('set null');
                $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
                $table->index('kode');
            });
        }

        // Create customer_assets table if not exists
        if (!Schema::hasTable('customer_assets')) {
            Schema::create('customer_assets', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('customer_id');
                $table->string('merk')->nullable();
                $table->string('model')->nullable();
                $table->string('serial_number')->nullable();
                $table->text('deskripsi')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            });
        }

        // Create purchase_orders table if not exists
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('no_po')->unique();
                $table->date('tanggal_po');
                $table->uuid('vendor_id');
                $table->decimal('total_amount', 12, 2)->nullable();
                $table->enum('status', ['draft', 'pending', 'received', 'cancelled'])->default('draft');
                $table->text('keterangan')->nullable();
                $table->timestamps();

                $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('restrict');
                $table->index('no_po');
            });
        }

        // Create purchase_order_lines table if not exists
        if (!Schema::hasTable('purchase_order_lines')) {
            Schema::create('purchase_order_lines', function (Blueprint $table) {
                $table->id();
                $table->uuid('purchase_order_id');
                $table->uuid('product_id');
                $table->integer('quantity');
                $table->decimal('unit_price', 12, 2);
                $table->decimal('total_price', 12, 2)->nullable();
                $table->decimal('tax', 12, 2)->nullable();
                $table->timestamps();

                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            });
        }

        // Create sale_orders table if not exists
        if (!Schema::hasTable('sale_orders')) {
            Schema::create('sale_orders', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('no_so')->unique();
                $table->date('tanggal_so');
                $table->uuid('customer_id');
                $table->decimal('total_amount', 12, 2)->nullable();
                $table->enum('status', ['draft', 'pending', 'completed', 'cancelled'])->default('draft');
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
                $table->index('no_so');
            });
        }

        // Create product_order_lines table if not exists
        if (!Schema::hasTable('product_order_lines')) {
            Schema::create('product_order_lines', function (Blueprint $table) {
                $table->id();
                $table->uuid('sale_order_id');
                $table->uuid('product_id');
                $table->integer('quantity');
                $table->decimal('unit_price', 12, 2);
                $table->decimal('total_price', 12, 2)->nullable();
                $table->decimal('hpp', 12, 2)->nullable();
                $table->timestamps();

                $table->foreign('sale_order_id')->references('id')->on('sale_orders')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            });
        }

        // Create service_order_lines table if not exists
        if (!Schema::hasTable('service_order_lines')) {
            Schema::create('service_order_lines', function (Blueprint $table) {
                $table->id();
                $table->uuid('sale_order_id');
                $table->uuid('service_id');
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 12, 2);
                $table->decimal('total_price', 12, 2)->nullable();
                $table->timestamps();

                $table->foreign('sale_order_id')->references('id')->on('sale_orders')->onDelete('cascade');
                $table->foreign('service_id')->references('id')->on('services')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_lines');
        Schema::dropIfExists('product_order_lines');
        Schema::dropIfExists('sale_orders');
        Schema::dropIfExists('purchase_order_lines');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('customer_assets');
        Schema::dropIfExists('products');
    }
};
