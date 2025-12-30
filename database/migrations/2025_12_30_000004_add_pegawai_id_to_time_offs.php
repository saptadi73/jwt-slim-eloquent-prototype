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
        Schema::table('time_offs', function (Blueprint $table) {
            if (!Schema::hasColumn('time_offs', 'pegawai_id')) {
                $table->uuid('pegawai_id')->nullable()->after('employee_id');
                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_offs', function (Blueprint $table) {
            if (Schema::hasColumn('time_offs', 'pegawai_id')) {
                $table->dropForeign(['pegawai_id']);
                $table->dropColumn('pegawai_id');
            }
        });
    }
};
