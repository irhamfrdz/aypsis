<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class TagihanCatPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah permission sudah ada untuk menghindari duplikasi
        $existingPermissions = Permission::pluck('name')->toArray();
        $newPermissions = [];

        // Tagihan CAT permissions - menggunakan format dash untuk konsistensi
        $tagihanCatPermissions = [
            // Main module permissions
            [
                'name' => 'tagihan-cat',
                'description' => 'Akses modul Tagihan CAT',
            ],

            // CRUD permissions
            [
                'name' => 'tagihan-cat-view',
                'description' => 'Melihat daftar Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-create',
                'description' => 'Membuat Tagihan CAT baru',
            ],
            [
                'name' => 'tagihan-cat-update',
                'description' => 'Mengupdate data Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-delete',
                'description' => 'Menghapus data Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-show',
                'description' => 'Melihat detail Tagihan CAT',
            ],

            // Route-based permissions
            [
                'name' => 'tagihan-cat-index',
                'description' => 'Akses halaman index Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-store',
                'description' => 'Menyimpan Tagihan CAT baru',
            ],
            [
                'name' => 'tagihan-cat-edit',
                'description' => 'Akses halaman edit Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-destroy',
                'description' => 'Menghapus Tagihan CAT',
            ],

            // Additional permissions
            [
                'name' => 'tagihan-cat-print',
                'description' => 'Mencetak Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-export',
                'description' => 'Mengekspor data Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-import',
                'description' => 'Mengimpor data Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-approve',
                'description' => 'Menyetujui Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-reject',
                'description' => 'Menolak Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-history',
                'description' => 'Melihat riwayat Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-template',
                'description' => 'Menggunakan template Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-single',
                'description' => 'Mencetak Tagihan CAT tunggal',
            ],
            [
                'name' => 'tagihan-cat-bulk',
                'description' => 'Operasi bulk untuk Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-mass-approve',
                'description' => 'Persetujuan massal Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-mass-reject',
                'description' => 'Penolakan massal Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-mass-print',
                'description' => 'Pencetakan massal Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-mass-export',
                'description' => 'Ekspor massal Tagihan CAT',
            ],

            // Status-based permissions
            [
                'name' => 'tagihan-cat-status-pending',
                'description' => 'Mengelola Tagihan CAT dengan status pending',
            ],
            [
                'name' => 'tagihan-cat-status-approved',
                'description' => 'Mengelola Tagihan CAT dengan status approved',
            ],
            [
                'name' => 'tagihan-cat-status-rejected',
                'description' => 'Mengelola Tagihan CAT dengan status rejected',
            ],
            [
                'name' => 'tagihan-cat-status-paid',
                'description' => 'Mengelola Tagihan CAT dengan status paid',
            ],
            [
                'name' => 'tagihan-cat-status-cancelled',
                'description' => 'Mengelola Tagihan CAT dengan status cancelled',
            ],

            // Advanced permissions
            [
                'name' => 'tagihan-cat-admin',
                'description' => 'Akses admin untuk Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-supervisor',
                'description' => 'Akses supervisor untuk Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-manager',
                'description' => 'Akses manager untuk Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-audit',
                'description' => 'Akses audit untuk Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-report',
                'description' => 'Akses laporan Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-dashboard',
                'description' => 'Akses dashboard Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-analytics',
                'description' => 'Akses analitik Tagihan CAT',
            ],
            [
                'name' => 'tagihan-cat-settings',
                'description' => 'Akses pengaturan Tagihan CAT',
            ],
        ];

        // Filter permissions yang belum ada
        foreach ($tagihanCatPermissions as $permission) {
            if (!in_array($permission['name'], $existingPermissions)) {
                $newPermissions[] = $permission;
            }
        }

        // Insert permissions baru
        if (!empty($newPermissions)) {
            foreach ($newPermissions as $permission) {
                Permission::create([
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                ]);
            }

            $this->command->info('Created ' . count($newPermissions) . ' new Tagihan CAT permissions:');
            foreach ($newPermissions as $permission) {
                $this->command->line('  - ' . $permission['name'] . ': ' . $permission['description']);
            }
        } else {
            $this->command->info('All Tagihan CAT permissions already exist.');
        }

        // Tampilkan semua permission Tagihan CAT yang ada
        $allTagihanCatPermissions = Permission::where('name', 'like', 'tagihan-cat%')->get();
        $this->command->info('Total Tagihan CAT permissions in database: ' . $allTagihanCatPermissions->count());

        // Assign permissions ke admin user jika ada
        $adminUser = \App\Models\User::where('username', 'admin')->first();
        if ($adminUser) {
            $tagihanCatPermissionIds = Permission::where('name', 'like', 'tagihan-cat%')->pluck('id')->toArray();

            // Sync permissions tanpa menghapus yang sudah ada
            $adminUser->permissions()->syncWithoutDetaching($tagihanCatPermissionIds);

            $this->command->info('Assigned ' . count($tagihanCatPermissionIds) . ' Tagihan CAT permissions to admin user.');
        } else {
            $this->command->warn('Admin user not found. Please assign Tagihan CAT permissions manually.');
        }

        $this->command->info('Tagihan CAT permissions seeder completed successfully!');
    }
}
