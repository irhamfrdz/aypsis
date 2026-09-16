<?php

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
            'master-jadwal-kapal-berlabuh-view',
            'master-jadwal-kapal-berlabuh-create',
            'master-jadwal-kapal-berlabuh-update',
            'master-jadwal-kapal-berlabuh-delete',
            'master-jadwal-kapal-berlabuh-export',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'master-jadwal-kapal-berlabuh-view',
            'master-jadwal-kapal-berlabuh-create',
            'master-jadwal-kapal-berlabuh-update',
            'master-jadwal-kapal-berlabuh-delete',
            'master-jadwal-kapal-berlabuh-export',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
