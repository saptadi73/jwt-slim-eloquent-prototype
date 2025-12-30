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
        // Create chart_of_accounts table
        if (!Schema::hasTable('chart_of_accounts')) {
            Schema::create('chart_of_accounts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('code')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']); // Account type
                $table->enum('category', ['current', 'fixed', 'current_liability', 'long_term_liability', 'owner_capital', 'retained_earnings', 'revenue', 'cogs', 'expense'])->nullable(); // Account category
                $table->enum('normal_balance', ['debit', 'credit'])->default('debit'); // Normal balance side
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('type');
                $table->index('category');
                $table->index('is_active');
                $table->index('code');
            });
        }

        // Create journal_entries table
        if (!Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->date('entry_date');
                $table->string('reference_number')->unique(); // e.g., JE-001, JE-002
                $table->text('description')->nullable();
                $table->enum('status', ['draft', 'posted', 'cancelled'])->default('draft');
                $table->uuid('created_by')->nullable();
                $table->timestamps();

                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
                $table->index('entry_date');
                $table->index('status');
                $table->index('created_by');
            });
        }

        // Create journal_lines table
        if (!Schema::hasTable('journal_lines')) {
            Schema::create('journal_lines', function (Blueprint $table) {
                $table->id();
                $table->uuid('journal_entry_id');
                $table->uuid('chart_of_account_id');
                $table->decimal('debit', 15, 2)->default(0);
                $table->decimal('credit', 15, 2)->default(0);
                $table->text('memo')->nullable();
                $table->integer('line_number');
                $table->timestamps();

                $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
                $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts')->onDelete('restrict');
                $table->index('journal_entry_id');
                $table->index('chart_of_account_id');
                $table->unique(['journal_entry_id', 'line_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
    }
};
