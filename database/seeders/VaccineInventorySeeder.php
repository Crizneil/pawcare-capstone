<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\VaccineInventory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VaccineInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vaccines = [
            [
                'name' => 'Anti-Rabies (Rabisin)',
                'description' => 'Annual rabies vaccination for dogs and cats.',
                'stock' => 50,
                'expiry_date' => Carbon::now()->addYear(),
                'low_stock_threshold' => 10,
            ],
            [
                'name' => '5-in-1 Vaccine (DHPP)',
                'description' => 'Core vaccine for canine distemper, hepatitis, and parvovirus.',
                'stock' => 8, // This will trigger the "Low Stock" alert
                'expiry_date' => Carbon::now()->addMonths(6),
                'low_stock_threshold' => 10,
            ],
            [
                'name' => 'Bordetella (Kennel Cough)',
                'description' => 'Protection against infectious tracheobronchitis.',
                'stock' => 15,
                'expiry_date' => Carbon::now()->subDays(5), // This is already expired
                'low_stock_threshold' => 5,
            ],
            [
                'name' => 'Feline 4-Way Vaccine',
                'description' => 'Core protection for indoor and outdoor cats.',
                'stock' => 3, // Very low stock
                'expiry_date' => Carbon::now()->addMonths(2),
                'low_stock_threshold' => 10,
            ],
        ];

        foreach ($vaccines as $vaccine) {
            VaccineInventory::create($vaccine);
        }
    }
}
