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
        Schema::create('vaccine_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the vaccine (e.g., Anti-Rabies)
            $table->string('batch_no')->nullable();
            $table->text('description')->nullable();
            $table->integer('stock')->default(0); // Current quantity in the fridge
            $table->date('expiry_date')->nullable(); // When the current batch expires
            $table->integer('low_stock_threshold')->default(10); // Alert when stock hits this number
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_inventories');
    }
};
