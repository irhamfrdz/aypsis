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
            ['name' => 'riwayat-pasang-ban-view', 'description' => 'View Riwayat Pasang Ban', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'riwayat-pasang-ban-export', 'description' => 'Export Riwayat Pasang Ban', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'riwayat-pasang-ban-view',
            'riwayat-pasang-ban-export',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
