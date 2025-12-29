<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'product')) {
                $table->renameColumn('product', 'jenis');
            } else {
                $table->string('jenis')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'jenis')) {
                $table->renameColumn('jenis', 'product');
            } else {
                $table->string('product')->nullable();
            }
        });
    }
};
