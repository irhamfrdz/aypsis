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
            ['name' => 'master-pricelist-temas-view', 'description' => 'Melihat data Pricelist Temas'],
            ['name' => 'master-pricelist-temas-create', 'description' => 'Menambah data Pricelist Temas'],
            ['name' => 'master-pricelist-temas-update', 'description' => 'Mengubah data Pricelist Temas'],
            ['name' => 'master-pricelist-temas-delete', 'description' => 'Menghapus data Pricelist Temas'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                ['description' => $permission['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'master-pricelist-temas-view',
            'master-pricelist-temas-create',
            'master-pricelist-temas-update',
            'master-pricelist-temas-delete',
        ])->delete();
    }
};
