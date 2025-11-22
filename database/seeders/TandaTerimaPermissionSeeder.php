<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class TandaTerimaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Tanda Terima permissions
            ['name' => 'tanda-terima-view', 'description' => 'Melihat Tanda Terima'],
            ['name' => 'tanda-terima-create', 'description' => 'Membuat Tanda Terima'],
            ['name' => 'tanda-terima-update', 'description' => 'Update Tanda Terima'],
            ['name' => 'tanda-terima-edit', 'description' => 'Edit Tanda Terima'],
            ['name' => 'tanda-terima-delete', 'description' => 'Menghapus Tanda Terima'],
            ['name' => 'tanda-terima-print', 'description' => 'Print Tanda Terima'],
            ['name' => 'tanda-terima-export', 'description' => 'Export Tanda Terima'],
        ];

        $permissionIds = [];
        foreach ($permissions as $permission) {
            $existing = DB::table('permissions')->where('name', $permission['name'])->first();
            
            if (!$existing) {
                DB::table('permissions')->insert([
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "✅ Created permission: {$permission['name']}\n";
            } else {
                echo "⚠️  Permission already exists: {$permission['name']}\n";
            }
            
            // Get the permission ID
            $permissionId = DB::table('permissions')->where('name', $permission['name'])->value('id');
            $permissionIds[] = $permissionId;
        }

        // Assign to admin users
        $adminUsers = User::where('username', 'admin')
                          ->orWhere('id', 1)
                          ->get();

        if ($adminUsers->isEmpty()) {
            echo "❌ No admin users found!\n";
            return;
        }

        foreach ($adminUsers as $admin) {
            echo "👤 Processing admin user: {$admin->username} (ID: {$admin->id})\n";
            $assignedCount = 0;
            
            foreach ($permissionIds as $permissionId) {
                $existing = DB::table('user_permissions')
                    ->where('user_id', $admin->id)
                    ->where('permission_id', $permissionId)
                    ->first();
                
                if (!$existing) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $admin->id,
                        'permission_id' => $permissionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $assignedCount++;
                }
            }
            
            echo "✅ Assigned {$assignedCount} new permissions to {$admin->username}\n";
        }

        echo "\n🎉 Tanda Terima permissions seeded and assigned to admin successfully!\n";
    }
}
