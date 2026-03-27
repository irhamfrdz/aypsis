<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignPembayaranPranotaUangJalanBatamPermissionsToAdminSeeder extends Seeder
{
    public function run(): void
    {
        $permissionsToAssign = Permission::whereIn('name', [
            'pembayaran-pranota-uang-jalan-batam-view',
            'pembayaran-pranota-uang-jalan-batam-create',
            'pembayaran-pranota-uang-jalan-batam-edit',
            'pembayaran-pranota-uang-jalan-batam-delete',
            'pembayaran-pranota-uang-jalan-batam-approve',
            'pembayaran-pranota-uang-jalan-batam-print',
            'pembayaran-pranota-uang-jalan-batam-export'
        ])->get();

        if ($permissionsToAssign->isEmpty()) {
            $this->command->error("❌ Pembayaran Pranota Uang Jalan Batam permissions not found.");
            return;
        }

        $adminUsers = User::whereIn('username', ['admin', 'administrator', 'superadmin'])->get();

        if ($adminUsers->isEmpty()) {
            $this->command->warn("⚠️ No admin users found.");
            return;
        }

        DB::transaction(function () use ($adminUsers, $permissionsToAssign) {
            foreach ($adminUsers as $admin) {
                // Assuming direct assignment or roles. In this project it seems admin->permissions() is a relationship.
                $existingIds = DB::table('user_permissions')
                    ->where('user_id', $admin->id)
                    ->pluck('permission_id')
                    ->toArray();

                foreach ($permissionsToAssign as $perm) {
                    if (!in_array($perm->id, $existingIds)) {
                        DB::table('user_permissions')->insert([
                            'user_id' => $admin->id,
                            'permission_id' => $perm->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
                $this->command->info("✅ Assigned permissions to user: {$admin->username}");
            }
        });
    }
}
