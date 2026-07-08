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
        // Insert mesin permissions
        DB::table('permissions')->insert([
            [
                'name' => 'mesin-view',
                'description' => 'Melihat menu kelola mesin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'mesin-create',
                'description' => 'Membuat data mesin baru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'mesin-update',
                'description' => 'Mengubah data mesin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'mesin-delete',
                'description' => 'Menghapus data mesin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete mesin permissions
        DB::table('permissions')->whereIn('name', [
            'mesin-view',
            'mesin-create',
            'mesin-update',
            'mesin-delete',
        ])->delete();
    }
};
