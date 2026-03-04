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
        Schema::table('appointments', function (Blueprint $table) {
            // Check if column exists before adding it to avoid errors if some exist
            if (!Schema::hasColumn('appointments', 'pet_id')) {
                $table->foreignId('pet_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('appointments', 'administered_by')) {
                $table->string('administered_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('appointments', 'batch_no')) {
                $table->string('batch_no')->nullable()->after('administered_by');
            }
            if (!Schema::hasColumn('appointments', 'next_due_date')) {
                $table->date('next_due_date')->nullable()->after('batch_no');
            }
            if (!Schema::hasColumn('appointments', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('next_due_date');
            }
            if (!Schema::hasColumn('appointments', 'vaccine_name')) {
                $table->string('vaccine_name')->nullable()->after('rejection_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['pet_id', 'administered_by', 'batch_no', 'next_due_date', 'rejection_reason', 'vaccine_name']);
        });
    }
};
