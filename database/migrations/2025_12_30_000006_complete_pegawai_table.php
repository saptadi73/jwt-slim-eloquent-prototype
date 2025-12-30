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
        Schema::table('pegawai', function (Blueprint $table) {
            // Add position_id if not exists
            if (!Schema::hasColumn('pegawai', 'position_id')) {
                $table->uuid('position_id')->nullable()->after('group_id');
                $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null');
            }

            // Add hire_date if not exists
            if (!Schema::hasColumn('pegawai', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('tanda_tangan');
            }

            // Add is_active if not exists
            if (!Schema::hasColumn('pegawai', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('hire_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (Schema::hasColumn('pegawai', 'position_id')) {
                $table->dropForeign(['position_id']);
                $table->dropColumn('position_id');
            }

            if (Schema::hasColumn('pegawai', 'hire_date')) {
                $table->dropColumn('hire_date');
            }

            if (Schema::hasColumn('pegawai', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
