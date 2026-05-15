<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $user = DB::table('users')->where('username', 'admin')->first();
        
        if (!$user) {
            return;
        }

        $permissions = DB::table('permissions')->whereIn('name', [
            'surat-jalan-kontainer-sewa-view',
            'surat-jalan-kontainer-sewa-create',
            'surat-jalan-kontainer-sewa-update',
            'surat-jalan-kontainer-sewa-delete',
            'surat-jalan-kontainer-sewa-print',
        ])->pluck('id');

        if ($permissions->isEmpty()) {
            return;
        }

        $data = [];
        foreach ($permissions as $permissionId) {
            $data[] = [
                'user_id' => $user->id,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('user_permissions')->insertOrIgnore($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $user = DB::table('users')->where('username', 'admin')->first();
        $permissions = DB::table('permissions')->whereIn('name', [
            'surat-jalan-kontainer-sewa-view',
            'surat-jalan-kontainer-sewa-create',
            'surat-jalan-kontainer-sewa-update',
            'surat-jalan-kontainer-sewa-delete',
            'surat-jalan-kontainer-sewa-print',
        ])->pluck('id');

        if ($user && $permissions->isNotEmpty()) {
            DB::table('user_permissions')
                ->where('user_id', $user->id)
                ->whereIn('permission_id', $permissions)
                ->delete();
        }
    }
};
