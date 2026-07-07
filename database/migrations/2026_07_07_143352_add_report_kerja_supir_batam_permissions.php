<?php

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            [
                'name' => 'report-kerja-supir-batam-view',
                'description' => 'Melihat halaman Report Kerja Supir Batam',
            ],
        ];

        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );

            // Assign to admin user
            $admin = User::where('username', 'admin')->first() ?: User::where('role', 'admin')->first();
            if ($admin) {
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

            // Assign to kiky user
            $kiky = User::where('username', 'kiky')->first();
            if ($kiky) {
                $hasPermission = DB::table('user_permissions')
                    ->where('user_id', $kiky->id)
                    ->where('permission_id', $permission->id)
                    ->exists();

                if (! $hasPermission) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $kiky->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = [
            'report-kerja-supir-batam-view',
        ];

        foreach ($permissionNames as $name) {
            Permission::where('name', $name)->delete();
        }
    }
};
