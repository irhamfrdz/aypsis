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
            [
                'name' => 'master-jam-kerja-view',
                'description' => 'Melihat daftar jam kerja / shift',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-jam-kerja-create',
                'description' => 'Membuat jam kerja / shift baru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-jam-kerja-update',
                'description' => 'Mengupdate data jam kerja / shift',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-jam-kerja-delete',
                'description' => 'Menghapus data jam kerja / shift',
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
        DB::table('permissions')->whereIn('name', [
            'master-jam-kerja-view',
            'master-jam-kerja-create',
            'master-jam-kerja-update',
            'master-jam-kerja-delete',
        ])->delete();
    }
};
