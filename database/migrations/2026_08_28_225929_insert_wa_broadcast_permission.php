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
            'master-wa-broadcast-view',
            'master-wa-broadcast-create',
            'master-wa-broadcast-update',
            'master-wa-broadcast-delete',
        ];

        foreach ($permissions as $permission) {
            // Use insertOrIgnore to avoid error if already exists
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
            'master-wa-broadcast-view',
            'master-wa-broadcast-create',
            'master-wa-broadcast-update',
            'master-wa-broadcast-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};

