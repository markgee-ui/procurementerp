<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            // Call your new user seeder here
            ProcurementUserSeeder::class, 
            
            // Add other seeders here if they exist
            // Example: SupplierSeeder::class,
        ]);
    }
}
