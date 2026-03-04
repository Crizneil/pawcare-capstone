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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('pet_id')->nullable()->constrained()->onDelete('cascade'); //  for linking

            $table->string('pet_name');
            $table->string('species');

            $table->date('appointment_date');
            $table->time('appointment_time');

            $table->string('service_type');
            $table->enum('status', ['pending', 'approved', 'completed', 'cancelled', 'rejected',
            'rescheduled', 'Done', 'Missed'])->default('pending');

            $table->string('administered_by')->nullable();
            $table->string('batch_no')->nullable();
            $table->date('next_due_date')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
