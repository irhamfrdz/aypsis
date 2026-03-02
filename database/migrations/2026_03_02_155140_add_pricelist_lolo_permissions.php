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
            ['name' => 'master-pricelist-lolo-read', 'description' => 'View Pricelist LOLO', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-pricelist-lolo-create', 'description' => 'Create Pricelist LOLO', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-pricelist-lolo-update', 'description' => 'Update Pricelist LOLO', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-pricelist-lolo-delete', 'description' => 'Delete Pricelist LOLO', 'created_at' => now(), 'updated_at' => now()],
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
            'master-pricelist-lolo-read',
            'master-pricelist-lolo-create',
            'master-pricelist-lolo-update',
            'master-pricelist-lolo-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
