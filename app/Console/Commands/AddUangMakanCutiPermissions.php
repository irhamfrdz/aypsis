<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddUangMakanCutiPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:add-uang-makan-cuti';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menambahkan izin akses untuk fitur Uang Makan dan Cuti';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $permissions = [
            ['name' => 'data-uang-makan-view', 'description' => 'View Uang Makan'],
            ['name' => 'data-uang-makan-create', 'description' => 'Buat Uang Makan'],
            ['name' => 'data-uang-makan-edit', 'description' => 'Edit Uang Makan'],
            ['name' => 'data-uang-makan-delete', 'description' => 'Hapus Uang Makan'],
            ['name' => 'data-cuti-view', 'description' => 'View Cuti'],
            ['name' => 'data-cuti-create', 'description' => 'Buat Cuti'],
            ['name' => 'data-cuti-edit', 'description' => 'Edit Cuti'],
            ['name' => 'data-cuti-delete', 'description' => 'Hapus Cuti'],
        ];

        $this->info('Membuat permissions Uang Makan dan Cuti...');

        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
            $this->info("✓ Permission '{$permissionData['name']}' berhasil dibuat.");
        }

        // Assign to admin user otomatis
        $admin = User::where('username', 'admin')->first();
        if (! $admin) {
            $admin = User::where('role', 'admin')->first();
        }

        if ($admin) {
            foreach ($permissions as $permissionData) {
                $permission = Permission::where('name', $permissionData['name'])->first();

                // Check if user already has this permission
                $hasPermission = DB::table('user_permissions')
                    ->where('user_id', $admin->id)
                    ->where('permission_id', $permission->id)
                    ->exists();

                if (! $hasPermission) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $admin->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            $this->info('✓ Semua permission telah diberikan kepada user Admin.');
        } else {
            $this->warn('User Admin tidak ditemukan, silakan assign permission secara manual melalui antarmuka (UI).');
        }

        $this->info('Selesai!');
    }
}
