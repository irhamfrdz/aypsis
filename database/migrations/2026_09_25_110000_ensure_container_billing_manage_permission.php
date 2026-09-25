<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->insertOrIgnore([
            'name' => 'container-billing-manage',
            'description' => 'Akses dan kelola Container Billing Control, termasuk restore dan reset data',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Permission is also owned by the original module migration.
        // Preserve it and existing user grants when rolling back this repair.
    }
};
