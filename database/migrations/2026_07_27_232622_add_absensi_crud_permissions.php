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
            'absensi-create',
            'absensi-edit',
            'absensi-delete',
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

        \Illuminate\Support\Facades\DB::table('permissions')->insertOrIgnore($permissionData);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'absensi-create',
            'absensi-edit',
            'absensi-delete',
        ];

        \Illuminate\Support\Facades\DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
