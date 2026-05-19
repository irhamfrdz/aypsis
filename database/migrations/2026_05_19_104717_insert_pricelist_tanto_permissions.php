<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            ['name' => 'master-pricelist-tanto-view', 'description' => 'Melihat data Pricelist Tanto'],
            ['name' => 'master-pricelist-tanto-create', 'description' => 'Menambah data Pricelist Tanto'],
            ['name' => 'master-pricelist-tanto-update', 'description' => 'Mengubah data Pricelist Tanto'],
            ['name' => 'master-pricelist-tanto-delete', 'description' => 'Menghapus data Pricelist Tanto'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                ['description' => $permission['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'master-pricelist-tanto-view',
            'master-pricelist-tanto-create',
            'master-pricelist-tanto-update',
            'master-pricelist-tanto-delete',
        ])->delete();
    }
};
