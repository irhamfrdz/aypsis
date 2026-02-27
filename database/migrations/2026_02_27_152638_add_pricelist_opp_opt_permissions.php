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
            'master-pricelist-opp-opt-view',
            'master-pricelist-opp-opt-create',
            'master-pricelist-opp-opt-update',
            'master-pricelist-opp-opt-delete',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore([
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
            'master-pricelist-opp-opt-view',
            'master-pricelist-opp-opt-create',
            'master-pricelist-opp-opt-update',
            'master-pricelist-opp-opt-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
