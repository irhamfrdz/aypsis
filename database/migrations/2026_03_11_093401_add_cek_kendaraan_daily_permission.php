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
            [
                'name' => 'monitoring-cek-kendaraan-view',
                'description' => 'Melihat list monitoring cek kendaraan'
            ],
            [
                'name' => 'monitoring-cek-kendaraan-daily-view',
                'description' => 'Melihat dashboard cek harian kendaraan'
            ]
        ];

        foreach ($permissions as $permission) {
            \Illuminate\Support\Facades\DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('permissions')
            ->whereIn('name', [
                'monitoring-cek-kendaraan-view',
                'monitoring-cek-kendaraan-daily-view'
            ])->delete();
    }
};
