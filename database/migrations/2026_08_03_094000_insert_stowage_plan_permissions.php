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
            'stowage-plan-view',
            'stowage-plan-create',
            'stowage-plan-edit',
            'stowage-plan-delete',
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
            'stowage-plan-view',
            'stowage-plan-create',
            'stowage-plan-edit',
            'stowage-plan-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
