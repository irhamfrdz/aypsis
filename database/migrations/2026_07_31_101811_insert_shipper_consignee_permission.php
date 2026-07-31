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
            'master-shipper-consignee-view',
            'master-shipper-consignee-create',
            'master-shipper-consignee-edit',
            'master-shipper-consignee-delete',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
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
            'master-shipper-consignee-view',
            'master-shipper-consignee-create',
            'master-shipper-consignee-edit',
            'master-shipper-consignee-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
