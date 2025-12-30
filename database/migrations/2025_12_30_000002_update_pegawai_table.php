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
            // Add new columns if they don't exist
            if (!Schema::hasColumn('pegawai', 'group_id')) {
                $table->uuid('group_id')->nullable()->after('departemen_id');
                $table->foreign('group_id')->references('id')->on('groups')->onDelete('set null');
            }

            if (!Schema::hasColumn('pegawai', 'url_foto')) {
                $table->string('url_foto')->nullable()->after('email');
            }

            if (!Schema::hasColumn('pegawai', 'tanda_tangan')) {
                $table->string('tanda_tangan')->nullable()->after('url_foto');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            if (Schema::hasColumn('pegawai', 'group_id')) {
                $table->dropForeign(['group_id']);
                $table->dropColumn('group_id');
            }

            if (Schema::hasColumn('pegawai', 'url_foto')) {
                $table->dropColumn('url_foto');
            }

            if (Schema::hasColumn('pegawai', 'tanda_tangan')) {
                $table->dropColumn('tanda_tangan');
            }
        });
    }
};
