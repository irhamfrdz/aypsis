<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('permissions')->insert([
            ['name' => 'biaya-bensin-view', 'description' => 'Melihat data biaya bensin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'biaya-bensin-create', 'description' => 'Menambah data biaya bensin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'biaya-bensin-update', 'description' => 'Mengubah data biaya bensin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'biaya-bensin-delete', 'description' => 'Menghapus data biaya bensin', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'biaya-bensin-view',
            'biaya-bensin-create',
            'biaya-bensin-update',
            'biaya-bensin-delete',
        ])->delete();
    }
};
