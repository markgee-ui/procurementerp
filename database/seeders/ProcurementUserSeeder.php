<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProcurementUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the user already exists to prevent duplicates
        $userExists = DB::table('users')->where('email', 'procurement@erp.com')->exists();

        if (!$userExists) {
            DB::table('users')->insert([
                'name' => 'Procurement Officer',
                'email' => 'procurement@erp.com',
                // IMPORTANT: The password must always be hashed.
                // The plaintext password for testing is 'secret123'
                'password' => Hash::make('password'),
                'role' => 'procurement', // The new role we added in the migration
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Optional: Add a standard employee user for testing roles
        $employeeExists = DB::table('users')->where('email', 'employee@erp.com')->exists();
        if (!$employeeExists) {
             DB::table('users')->insert([
                'name' => 'Standard Employee',
                'email' => 'employee@erp.com',
                'password' => Hash::make('password'),
                'role' => 'user', 
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}