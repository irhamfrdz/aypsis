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
        DB::table('permissions')->insertOrIgnore([
            ['name' => 'perincian-view', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'perincian-create', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'perincian-edit', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'perincian-delete', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'perincian-view',
            'perincian-create',
            'perincian-edit',
            'perincian-delete'
        ])->delete();
    }
};
