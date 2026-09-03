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
            ['name' => 'master-group-bp-jamsostek-view', 'description' => 'Melihat Master Group BP Jamsostek'],
            ['name' => 'master-group-bp-jamsostek-create', 'description' => 'Menambah Master Group BP Jamsostek'],
            ['name' => 'master-group-bp-jamsostek-update', 'description' => 'Mengubah Master Group BP Jamsostek'],
            ['name' => 'master-group-bp-jamsostek-delete', 'description' => 'Menghapus Master Group BP Jamsostek'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'master-group-bp-jamsostek-view',
            'master-group-bp-jamsostek-create',
            'master-group-bp-jamsostek-update',
            'master-group-bp-jamsostek-delete',
        ])->delete();
    }
};
