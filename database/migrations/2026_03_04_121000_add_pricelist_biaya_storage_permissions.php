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
            'master-pricelist-biaya-storage-view',
            'master-pricelist-biaya-storage-create',
            'master-pricelist-biaya-storage-update',
            'master-pricelist-biaya-storage-delete',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission],
                [
                    'name' => $permission,
                    'description' => 'Permission for ' . str_replace('-', ' ', $permission),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'master-pricelist-biaya-storage-view',
            'master-pricelist-biaya-storage-create',
            'master-pricelist-biaya-storage-update',
            'master-pricelist-biaya-storage-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
