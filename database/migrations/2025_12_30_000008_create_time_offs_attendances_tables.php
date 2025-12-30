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
        // Create time_offs table if not exists
        if (!Schema::hasTable('time_offs')) {
            Schema::create('time_offs', function (Blueprint $table) {
                $table->id();
                $table->uuid('pegawai_id')->nullable();
                $table->uuid('employee_id')->nullable();
                $table->enum('type', ['annual_leave', 'sick_leave', 'unpaid_leave', 'maternity_leave', 'paternity_leave', 'other'])->default('annual_leave');
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('total_days')->nullable();
                $table->text('reason')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
                $table->uuid('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                // Foreign keys
                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                $table->foreign('approved_by')->references('id')->on('pegawai')->onDelete('set null');
            });
        }

        // Create attendances table if not exists
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->uuid('pegawai_id')->nullable();
                $table->uuid('employee_id')->nullable();
                $table->date('date');
                $table->timestamp('check_in')->nullable();
                $table->timestamp('check_out')->nullable();
                $table->enum('status', ['present', 'absent', 'late', 'half_day', 'on_leave', 'sick', 'holiday'])->default('absent');
                $table->decimal('work_hours', 5, 2)->nullable();
                $table->decimal('overtime_hours', 5, 2)->nullable();
                $table->text('notes')->nullable();
                $table->string('location')->nullable();
                $table->string('check_in_photo')->nullable();
                $table->string('check_out_photo')->nullable();
                $table->timestamps();

                // Indexes
                $table->index(['pegawai_id', 'date']);
                $table->index(['employee_id', 'date']);
                $table->index('date');

                // Foreign keys
                $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('time_offs');
    }
};
