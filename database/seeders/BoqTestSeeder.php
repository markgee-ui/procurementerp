<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;        // Required for the BoQ user_id foreign key
use App\Models\Boq;
use App\Models\BoqActivity;
use App\Models\BoqMaterial;

class BoqTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure a Test User Exists for the foreign key
        $testUser = User::firstOrCreate(
            ['email' => 'pm@erp.com'],
            [
                'name' => 'Project Manager',
                'password' => bcrypt('password'), // Use a safe default
                 'role' => 'pm',
            ]
        );

        // 2. Create the Main BOQ (Project)
        $boq = Boq::firstOrCreate(
            ['project_name' => 'Test Residential Tower Phase I'],
            [
                'project_budget' => 2500000.00,
                'user_id' => $testUser->id,
            ]
        );

        // 3. Create BOQ Activities, linked to the new BOQ ID
        $activityData = [
            ['name' => 'Foundation Works', 'budget' => 300000.00],
            ['name' => 'Structural Blockwork', 'budget' => 500000.00],
        ];

        $activities = [];
        foreach ($activityData as $data) {
            $activities[] = BoqActivity::firstOrCreate(
                ['boq_id' => $boq->id, 'name' => $data['name']],
                $data
            );
        }

        // --- 4. Create BOQ Materials, linked to the newly created Activity IDs ---
        
        $foundationActivity = collect($activities)->firstWhere('name', 'Foundation Works');
        $blockworkActivity = collect($activities)->firstWhere('name', 'Structural Blockwork');


        if ($foundationActivity) {
            BoqMaterial::insert([
                [
                    'boq_activity_id' => $foundationActivity->id, 
                    'item' => 'Reinforcing Steel Bar',
                    'specs' => 'Y12mm (BS 4449)',
                    'unit' => 'KG',
                    'qty' => 5500.00,
                    'rate' => 1.25,
                    'remarks' => 'High yield structural steel.',
                    'created_at' => now(), 'updated_at' => now(),
                ],
                [
                    'boq_activity_id' => $foundationActivity->id, 
                    'item' => 'Concrete',
                    'specs' => 'Grade 30 (M30)',
                    'unit' => 'M3',
                    'qty' => 45.00,
                    'rate' => 150.00,
                    'remarks' => 'Ready-mix concrete for slab.',
                    'created_at' => now(), 'updated_at' => now(),
                ],
            ]);
        }

        if ($blockworkActivity) {
            BoqMaterial::insert([
                [
                    'boq_activity_id' => $blockworkActivity->id, 
                    'item' => 'Hollow Blocks',
                    'specs' => '6-inch (150mm)',
                    'unit' => 'PCS',
                    'qty' => 2500.00,
                    'rate' => 0.85,
                    'remarks' => 'Exterior load-bearing walls.',
                    'created_at' => now(), 'updated_at' => now(),
                ],
                [
                    'boq_activity_id' => $blockworkActivity->id, 
                    'item' => 'Cement',
                    'specs' => 'Portland Type I (50kg bag)',
                    'unit' => 'BAG',
                    'qty' => 200.00,
                    'rate' => 8.50,
                    'remarks' => 'For mortar and plastering.',
                    'created_at' => now(), 'updated_at' => now(),
                ],
            ]);
        }
    }
}