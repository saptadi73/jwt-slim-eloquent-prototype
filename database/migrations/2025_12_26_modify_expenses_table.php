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
        Schema::table('expenses', function (Blueprint $table) {
            // Make product_id nullable
            $table->uuid('product_id')->nullable()->change();
            // Make nomor nullable (optional)
            $table->string('nomor')->nullable()->change();
            // Make keterangan text type instead of varchar
            $table->text('keterangan')->nullable()->change();
            // Make bukti nullable
            $table->string('bukti')->nullable()->change();
            // Make status with default value
            $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            //
        });
    }
};
