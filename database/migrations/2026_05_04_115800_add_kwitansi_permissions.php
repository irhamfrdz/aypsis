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
            'kwitansi-view',
            'kwitansi-create',
            'kwitansi-update',
            'kwitansi-delete',
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
            'kwitansi-view',
            'kwitansi-create',
            'kwitansi-update',
            'kwitansi-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
