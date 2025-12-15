<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode')->nullable();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('type')->nullable();
            $table->decimal('harga', 12, 2)->nullable();
            $table->decimal('hpp', 12, 2)->nullable();
            $table->integer('stok')->default(0);
            $table->string('model')->nullable();
            $table->boolean('is_sealable')->default(false);
            $table->string('gambar')->nullable();
            
            // Foreign Keys
            $table->uuid('kategori_id')->nullable();
            $table->uuid('satuan_id')->nullable();
            $table->uuid('brand_id')->nullable();
            
            $table->foreign('kategori_id')->references('id')->on('kategoris')->onDelete('set null');
            $table->foreign('satuan_id')->references('id')->on('satuans')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
