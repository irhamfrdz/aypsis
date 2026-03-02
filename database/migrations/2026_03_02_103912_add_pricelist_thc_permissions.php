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
            ['name' => 'master-pricelist-thc-view', 'description' => 'View Pricelist THC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-pricelist-thc-create', 'description' => 'Create Pricelist THC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-pricelist-thc-update', 'description' => 'Update Pricelist THC', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-pricelist-thc-delete', 'description' => 'Delete Pricelist THC', 'created_at' => now(), 'updated_at' => now()],
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
            'master-pricelist-thc-view',
            'master-pricelist-thc-create',
            'master-pricelist-thc-update',
            'master-pricelist-thc-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
