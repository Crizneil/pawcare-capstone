<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Main Admin
        User::updateOrCreate(
            ['email' => 'admin@pawcare.com'],
            [
                'name' => 'Main Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'house_no' => 'Clinic',
                'street' => 'Main Street',
                'barangay' => 'Poblacion',
                'city' => 'City of Meycauayan',
                'province' => 'Bulacan',
            ]
        );

        // Test Pet Owner
        User::updateOrCreate(
            ['email' => 'owner@gmail.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'house_no' => '91',
                'street' => 'Main Street',
                'barangay' => 'Banga',
                'city' => 'Meycauayan',
                'province' => 'Bulacan',
            ]
        );

    }
}
