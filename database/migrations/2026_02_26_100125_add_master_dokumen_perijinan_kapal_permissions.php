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
        $permissions = [
            'master-dokumen-perijinan-kapal-view',
            'master-dokumen-perijinan-kapal-create',
            'master-dokumen-perijinan-kapal-edit',
            'master-dokumen-perijinan-kapal-update',
            'master-dokumen-perijinan-kapal-delete',
        ];

        foreach ($permissions as $permission) {
            \Illuminate\Support\Facades\DB::table('permissions')->insertOrIgnore([
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
            'master-dokumen-perijinan-kapal-view',
            'master-dokumen-perijinan-kapal-create',
            'master-dokumen-perijinan-kapal-edit',
            'master-dokumen-perijinan-kapal-update',
            'master-dokumen-perijinan-kapal-delete',
        ];

        \Illuminate\Support\Facades\DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
