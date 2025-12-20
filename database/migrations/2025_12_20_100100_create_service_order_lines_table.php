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
        Schema::create('service_order_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sale_order_id');
            $table->uuid('service_id');
            $table->integer('line_number');
            $table->string('description')->nullable();
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('sale_order_id')
                ->references('id')
                ->on('sale_orders')
                ->onDelete('cascade');
            
            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('restrict');

            // Indexes
            $table->index('sale_order_id');
            $table->index('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_lines');
    }
};
