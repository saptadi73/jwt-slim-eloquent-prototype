<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Consolidate users and roles table structure to match new schema
     */
    public function up(): void
    {
        // Add columns to users table if it already exists
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'id_string')) {
            Schema::table('users', function (Blueprint $table) {
                // Check and add UUID column
                if (!Schema::hasColumn('users', 'uuid')) {
                    $table->uuid('uuid')->unique()->nullable()->after('id');
                }
                // Check and add pegawai_id
                if (!Schema::hasColumn('users', 'pegawai_id')) {
                    $table->uuid('pegawai_id')->nullable()->after('uuid');
                }
                // Check and add is_active
                if (!Schema::hasColumn('users', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('password');
                }
                // Check and add last_login
                if (!Schema::hasColumn('users', 'last_login')) {
                    $table->timestamp('last_login')->nullable();
                }
            });
        }

        // Add columns to roles table if it exists
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'is_active')) {
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn('roles', 'description')) {
                    $table->text('description')->nullable();
                }
            });
        }

        // Create pegawai table if not exists (base employee table)
        if (!Schema::hasTable('pegawai')) {
            Schema::create('pegawai', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama');
                $table->text('alamat')->nullable();
                $table->string('hp')->nullable();
                $table->string('email')->nullable();
                $table->uuid('departemen_id')->nullable();
                $table->uuid('group_id')->nullable();
                $table->uuid('position_id')->nullable();
                $table->string('url_foto')->nullable();
                $table->string('tanda_tangan')->nullable();
                $table->date('hire_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('nama');
                $table->index('is_active');
            });
        }

        // Create departemen table if not exists
        if (!Schema::hasTable('departemen')) {
            Schema::create('departemen', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama');
                $table->text('deskripsi')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('nama');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departemen');
        
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumnIfExists(['uuid', 'pegawai_id', 'is_active', 'last_login']);
            });
        }

        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumnIfExists(['is_active', 'description']);
            });
        }
    }
};
