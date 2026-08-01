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
        $permissions = [
            ['name' => 'pranota-uang-makan-view', 'description' => 'View Pranota Uang Makan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-uang-makan-create', 'description' => 'Buat Pranota Uang Makan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-uang-makan-edit', 'description' => 'Edit Pranota Uang Makan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-uang-makan-delete', 'description' => 'Hapus Pranota Uang Makan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-uang-makan-print', 'description' => 'Cetak Pranota Uang Makan', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('permissions')->insert($permissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $names = [
            'pranota-uang-makan-view',
            'pranota-uang-makan-create',
            'pranota-uang-makan-edit',
            'pranota-uang-makan-delete',
            'pranota-uang-makan-print'
        ];

        DB::table('permissions')->whereIn('name', $names)->delete();
    }
};
