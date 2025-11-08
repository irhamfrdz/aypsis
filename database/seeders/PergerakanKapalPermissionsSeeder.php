<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PergerakanKapalPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define pergerakan-kapal permissions
        $permissions = [
            [
                'name' => 'pergerakan-kapal-view',
                'description' => 'Lihat data pergerakan kapal',
            ],
            [
                'name' => 'pergerakan-kapal-create',
                'description' => 'Buat data pergerakan kapal baru',
            ],
            [
                'name' => 'pergerakan-kapal-update',
                'description' => 'Edit data pergerakan kapal',
            ],
            [
                'name' => 'pergerakan-kapal-delete',
                'description' => 'Hapus data pergerakan kapal',
            ],
            [
                'name' => 'pergerakan-kapal-approve',
                'description' => 'Approve pergerakan kapal',
            ],
            [
                'name' => 'pergerakan-kapal-print',
                'description' => 'Cetak laporan pergerakan kapal',
            ],
            [
                'name' => 'pergerakan-kapal-export',
                'description' => 'Export data pergerakan kapal',
            ],
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $existingPermission = Permission::where('name', $permission['name'])->first();
            
            if (!$existingPermission) {
                Permission::create([
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $this->command->info("Created permission: " . $permission['name']);
            } else {
                $this->command->info("Permission already exists: " . $permission['name']);
            }
        }

        $this->command->info("Pergerakan Kapal permissions seeder completed successfully!");
    }
}
