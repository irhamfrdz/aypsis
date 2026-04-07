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
            ['name' => 'master-pricelist-meratus-view', 'description' => 'Melihat data Pricelist Meratus'],
            ['name' => 'master-pricelist-meratus-create', 'description' => 'Menambah data Pricelist Meratus'],
            ['name' => 'master-pricelist-meratus-update', 'description' => 'Mengubah data Pricelist Meratus'],
            ['name' => 'master-pricelist-meratus-delete', 'description' => 'Menghapus data Pricelist Meratus'],
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
            'master-pricelist-meratus-view',
            'master-pricelist-meratus-create',
            'master-pricelist-meratus-update',
            'master-pricelist-meratus-delete',
        ])->delete();
    }
};
