<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SuratJalanApprovalPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar permission untuk approval surat jalan
        $permissions = [
            // Level 1 Approval
            [
                'name' => 'surat-jalan-approval-level-1-view',
                'description' => 'View surat jalan yang perlu approval level 1'
            ],
            [
                'name' => 'surat-jalan-approval-level-1-approve',
                'description' => 'Approve surat jalan level 1'
            ],

            // Level 2 Approval
            [
                'name' => 'surat-jalan-approval-level-2-view',
                'description' => 'View surat jalan yang perlu approval level 2'
            ],
            [
                'name' => 'surat-jalan-approval-level-2-approve',
                'description' => 'Approve surat jalan level 2'
            ],

            // Permission umum untuk dashboard approval
            [
                'name' => 'surat-jalan-approval-dashboard',
                'description' => 'Access to surat jalan approval dashboard'
            ],
        ];

        echo "=== Menambahkan Permission Khusus Surat Jalan Approval ===\n";

        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );

            if ($permission->wasRecentlyCreated) {
                echo "✓ Permission dibuat: {$permissionData['name']}\n";
            } else {
                echo "- Permission sudah ada: {$permissionData['name']}\n";
            }
        }

        echo "\n=== Menambahkan Permission ke User Admin ===\n";

        // Berikan semua permission ke user admin - berdasarkan struktur tabel users
        $adminUsers = User::where('username', 'admin')
                         ->orWhere('username', 'kiky') // user super admin yang ada
                         ->orWhere('id', 1) // biasanya user pertama adalah admin
                         ->get();

        if ($adminUsers->count() > 0) {
            foreach ($adminUsers as $admin) {
                foreach ($permissions as $permissionData) {
                    $permission = Permission::where('name', $permissionData['name'])->first();
                    if ($permission) {
                        // Check if user already has this permission
                        $hasPermission = DB::table('user_permissions')
                            ->where('user_id', $admin->id)
                            ->where('permission_id', $permission->id)
                            ->exists();

                        if (!$hasPermission) {
                            DB::table('user_permissions')->insert([
                                'user_id' => $admin->id,
                                'permission_id' => $permission->id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                            echo "✓ Permission {$permissionData['name']} diberikan ke {$admin->username}\n";
                        } else {
                            echo "- User {$admin->username} sudah memiliki permission {$permissionData['name']}\n";
                        }
                    }
                }
            }
        } else {
            echo "⚠ User admin tidak ditemukan\n";
            echo "Info: Anda bisa menambahkan permission ini manual ke user yang diperlukan\n";
        }

        echo "\n=== Summary ===\n";
        echo "✓ Semua permission surat jalan approval berhasil dibuat\n";
        echo "✓ Permission sudah diberikan ke user admin\n";
        echo "\nCatatan: \n";
        echo "- Gunakan permission 'surat-jalan-approval-level-1-view' dan 'surat-jalan-approval-level-1-approve' untuk approver level 1\n";
        echo "- Gunakan permission 'surat-jalan-approval-level-2-view' dan 'surat-jalan-approval-level-2-approve' untuk approver level 2\n";
        echo "- Permission 'surat-jalan-approval-dashboard' diperlukan untuk akses dashboard approval\n";
    }
}
