<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['view', 'create'] as $action) {
            DB::table('permissions')->insertOrIgnore([
                'name' => 'pembayaran-pranota-bpjs-'.$action,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $permissionId = DB::table('permissions')->where('name', 'pembayaran-pranota-bpjs-'.$action)->value('id');
            $adminId = DB::table('roles')->where('name', 'admin')->value('id');
            if ($adminId) {
                DB::table('permission_role')->insertOrIgnore(['permission_id' => $permissionId, 'role_id' => $adminId]);
            }
        }
    }

    public function down(): void
    {
        $ids = DB::table('permissions')->whereIn('name', ['pembayaran-pranota-bpjs-view', 'pembayaran-pranota-bpjs-create'])->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('user_permissions')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('id', $ids)->delete();
    }
};
