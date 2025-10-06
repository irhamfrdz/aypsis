<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert master pajak permissions
        DB::table('permissions')->insert([
            [
                'name' => 'master-pajak.view',
                'description' => 'Melihat daftar pajak',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-pajak.create',
                'description' => 'Membuat pajak baru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-pajak.update',
                'description' => 'Mengupdate data pajak',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-pajak.delete',
                'description' => 'Menghapus data pajak',
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
        // Delete master pajak permissions
        DB::table('permissions')->whereIn('name', [
            'master-pajak.view',
            'master-pajak.create',
            'master-pajak.update',
            'master-pajak.delete',
        ])->delete();
    }
};
