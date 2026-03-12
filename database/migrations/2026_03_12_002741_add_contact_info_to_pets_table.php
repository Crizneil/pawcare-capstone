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
        Schema::table('pets', function (Blueprint $table) {
            $table->string('owner_phone')->nullable()->after('owner');
            $table->string('owner_gender')->nullable()->after('owner_phone');
            $table->string('house_no')->nullable()->after('owner_gender');
            $table->string('street')->nullable()->after('house_no');
            $table->string('barangay')->nullable()->after('street');
            $table->string('city')->default('Meycauayan City')->after('barangay');
            $table->string('province')->default('Bulacan')->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['owner_phone', 'owner_gender', 'house_no', 'street', 'barangay', 'city', 'province']);
        });
    }
};
