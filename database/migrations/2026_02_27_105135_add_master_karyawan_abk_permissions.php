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
        $permissions = [
            'master-karyawan-abk',
            'master-karyawan-abk-view',
            'master-karyawan-abk-create',
            'master-karyawan-abk-update',
            'master-karyawan-abk-delete',
            'master-karyawan-abk-export',
            'master-karyawan-abk-print',
        ];

        $timestamp = now();
        $permissionData = [];

        foreach ($permissions as $permission) {
            $permissionData[] = [
                'name' => $permission,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        DB::table('permissions')->insertOrIgnore($permissionData);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'master-karyawan-abk',
            'master-karyawan-abk-view',
            'master-karyawan-abk-create',
            'master-karyawan-abk-update',
            'master-karyawan-abk-delete',
            'master-karyawan-abk-export',
            'master-karyawan-abk-print',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
