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
            ['name' => 'master-gudang-ban-view', 'description' => 'View Master Gudang Ban', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-gudang-ban-create', 'description' => 'Create Master Gudang Ban', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-gudang-ban-edit', 'description' => 'Edit Master Gudang Ban', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-gudang-ban-delete', 'description' => 'Delete Master Gudang Ban', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-gudang-ban-export', 'description' => 'Export Master Gudang Ban', 'created_at' => now(), 'updated_at' => now()],
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
            'master-gudang-ban-view',
            'master-gudang-ban-create',
            'master-gudang-ban-edit',
            'master-gudang-ban-delete',
            'master-gudang-ban-export',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
