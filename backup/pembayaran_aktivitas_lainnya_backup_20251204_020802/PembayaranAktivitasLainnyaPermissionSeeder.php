<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PembayaranAktivitasLainnyaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permissions untuk Pembayaran Aktivitas Lainnya
        $permissions = [
            // Basic CRUD permissions
            [
                'name' => 'pembayaran-aktivitas-lainnya-view',
                'description' => 'Melihat daftar pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-create',
                'description' => 'Membuat pembayaran aktivitas lainnya baru'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-update',
                'description' => 'Mengedit pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-delete',
                'description' => 'Menghapus pembayaran aktivitas lainnya'
            ],

            // Additional permissions based on routes
            [
                'name' => 'pembayaran-aktivitas-lainnya-export',
                'description' => 'Mengekspor data pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-print',
                'description' => 'Mencetak pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-approve',
                'description' => 'Menyetujui pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-reject',
                'description' => 'Menolak pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-generate-nomor',
                'description' => 'Generate nomor pembayaran aktivitas lainnya'
            ],
            [
                'name' => 'pembayaran-aktivitas-lainnya-payment-form',
                'description' => 'Akses form pembayaran aktivitas lainnya'
            ]
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $existingPermission = Permission::where('name', $permission['name'])->first();

            if (!$existingPermission) {
                Permission::create([
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("✅ Created permission: {$permission['name']}");
            } else {
                $this->command->warn("⚠️  Permission already exists: {$permission['name']}");
            }
        }

        $this->command->info("🎉 Pembayaran Aktivitas Lainnya permissions seeding completed!");
    }
}
