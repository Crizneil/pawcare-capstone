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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('pet_id')->unique();
            $table->string('name');
            $table->string('species');
            $table->string('gender');
            $table->date('birthday');
            $table->string('breed');
            $table->string('owner');
            $table->date('last_date')->nullable();
            $table->date('next_date')->nullable();
            $table->string('vaccine_type')->nullable();
            $table->longText('image_url')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
