<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['name' => 'permohonan-amprahan-approve'],
            [
                'description' => 'Approve Permohonan Amprahan',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            $permission = DB::table('permissions')->where('name', 'permohonan-amprahan-approve')->first();
            if ($permission) {
                DB::table('permission_role')->updateOrInsert([
                    'permission_id' => $permission->id,
                    'role_id' => $adminRole->id
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = DB::table('permissions')->where('name', 'permohonan-amprahan-approve')->first();
        if ($permission) {
            DB::table('permission_role')->where('permission_id', $permission->id)->delete();
            DB::table('permissions')->where('id', $permission->id)->delete();
        }
    }
};
