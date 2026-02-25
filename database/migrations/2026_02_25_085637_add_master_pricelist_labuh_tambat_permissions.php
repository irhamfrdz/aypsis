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
            'master-pricelist-labuh-tambat-view',
            'master-pricelist-labuh-tambat-create',
            'master-pricelist-labuh-tambat-update',
            'master-pricelist-labuh-tambat-delete',
        ];

        foreach ($permissions as $permission) {
            \Illuminate\Support\Facades\DB::table('permissions')->insertOrIgnore([
                'name' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'master-pricelist-labuh-tambat-view',
            'master-pricelist-labuh-tambat-create',
            'master-pricelist-labuh-tambat-update',
            'master-pricelist-labuh-tambat-delete',
        ];

        \Illuminate\Support\Facades\DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
