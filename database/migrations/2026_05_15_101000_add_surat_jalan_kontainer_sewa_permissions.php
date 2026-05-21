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
            'surat-jalan-kontainer-sewa-view',
            'surat-jalan-kontainer-sewa-create',
            'surat-jalan-kontainer-sewa-update',
            'surat-jalan-kontainer-sewa-delete',
            'surat-jalan-kontainer-sewa-print',
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
            'surat-jalan-kontainer-sewa-view',
            'surat-jalan-kontainer-sewa-create',
            'surat-jalan-kontainer-sewa-update',
            'surat-jalan-kontainer-sewa-delete',
            'surat-jalan-kontainer-sewa-print',
        ])->delete();
    }
};
