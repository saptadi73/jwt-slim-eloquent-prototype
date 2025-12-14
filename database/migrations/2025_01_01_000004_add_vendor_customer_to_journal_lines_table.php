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
        Schema::table('journal_lines', function (Blueprint $table) {
            $table->uuid('vendor_id')->nullable()->after('credit');
            $table->uuid('customer_id')->nullable()->after('vendor_id');

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_lines', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['customer_id']);
            $table->dropColumn(['vendor_id', 'customer_id']);
        });
    }
};
