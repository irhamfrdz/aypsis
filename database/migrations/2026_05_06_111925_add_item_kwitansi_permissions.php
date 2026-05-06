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
            'master-item-kwitansi-view',
            'master-item-kwitansi-create',
            'master-item-kwitansi-update',
            'master-item-kwitansi-delete',
        ];

        $timestamp = now();
        $data = [];
        foreach ($permissions as $permission) {
            $data[] = [
                'name' => $permission,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        DB::table('permissions')->insertOrIgnore($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'master-item-kwitansi-view',
            'master-item-kwitansi-create',
            'master-item-kwitansi-update',
            'master-item-kwitansi-delete',
        ])->delete();
    }
};
