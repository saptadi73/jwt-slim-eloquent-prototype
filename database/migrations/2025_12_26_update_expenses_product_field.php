<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // drop foreign key if it exists then remove product_id
            if (Schema::hasColumn('expenses', 'product_id')) {
                try {
                    $table->dropForeign(['product_id']);
                } catch (\Throwable $th) {
                    // ignore if constraint name differs or not exists
                }
                $table->dropColumn('product_id');
            }
            $table->string('product')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'product')) {
                $table->dropColumn('product');
            }
            $table->uuid('product_id')->nullable();
        });
    }
};
