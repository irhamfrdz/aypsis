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
            ['name' => 'pranota-uang-rit-kenek-view', 'description' => 'Melihat daftar pranota uang rit kenek'],
            ['name' => 'pranota-uang-rit-kenek-create', 'description' => 'Membuat pranota uang rit kenek'],
            ['name' => 'pranota-uang-rit-kenek-update', 'description' => 'Mengubah data pranota uang rit kenek'],
            ['name' => 'pranota-uang-rit-kenek-delete', 'description' => 'Menghapus pranota uang rit kenek'],
            ['name' => 'pranota-uang-rit-kenek-approve', 'description' => 'Menyetujui pranota uang rit kenek'],
            ['name' => 'pranota-uang-rit-kenek-mark-paid', 'description' => 'Menandai paid pranota uang rit kenek'],
            ['name' => 'pranota-uang-rit-kenek-print', 'description' => 'Mencetak pranota uang rit kenek'],
            ['name' => 'pranota-uang-rit-kenek-export', 'description' => 'Export data pranota uang rit kenek'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->where('name', 'like', 'pranota-uang-rit-kenek-%')->delete();
    }
};
