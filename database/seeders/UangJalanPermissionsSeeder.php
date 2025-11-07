<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class UangJalanPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            echo "=== Menambahkan Permissions Uang Jalan ke User Admin ===\n";

            // 1. Buat permissions baru untuk uang jalan
            $permissionNames = [
                'uang-jalan-view',
                'uang-jalan-create', 
                'uang-jalan-update',
                'uang-jalan-delete'
            ];

            echo "Membuat permissions uang jalan...\n";
            
            foreach ($permissionNames as $permissionName) {
                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]);
                
                if ($permission->wasRecentlyCreated) {
                    echo "✅ Permission '{$permissionName}' berhasil dibuat\n";
                } else {
                    echo "ℹ️ Permission '{$permissionName}' sudah ada\n";
                }
            }

            // 2. Ambil user admin
            $adminUser = User::where('username', 'admin')->first();

            if (!$adminUser) {
                echo "❌ User 'admin' tidak ditemukan\n";
                return;
            }

            echo "\n✅ User admin ditemukan: {$adminUser->username} (ID: {$adminUser->id})\n";

            // 3. Berikan semua permissions uang jalan ke user admin
            echo "\nMemberikan permissions ke user admin...\n";
            
            foreach ($permissionNames as $permissionName) {
                if (!$adminUser->hasPermissionTo($permissionName)) {
                    $adminUser->givePermissionTo($permissionName);
                    echo "✅ Permission '{$permissionName}' diberikan ke user admin\n";
                } else {
                    echo "ℹ️ User admin sudah memiliki permission '{$permissionName}'\n";
                }
            }

            echo "\n=== Verifikasi ===\n";
            $userPermissions = $adminUser->permissions->where('name', 'like', 'uang-jalan%')->pluck('name');
            echo "Permissions uang jalan yang dimiliki user admin:\n";
            foreach ($userPermissions as $permission) {
                echo "   - {$permission}\n";
            }
            
            echo "\n🎉 Semua permissions uang jalan berhasil diberikan ke user admin!\n";
            
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
}