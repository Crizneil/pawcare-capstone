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
        Schema::create('vaccinations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pet_id')->constrained()->onDelete('cascade');
        $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
        $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
        $table->string('vaccine_name');
        $table->string('batch_no')->nullable();
        $table->string('status')->nullable();
        $table->date('date_administered');
        $table->date('next_due_date')->nullable();
        $table->text('remarks')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccinations');
    }
};
