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
            ['name' => 'pranota-uang-rit-batam-view', 'description' => 'Melihat list pranota uang rit supir batam'],
            ['name' => 'pranota-uang-rit-batam-create', 'description' => 'Membuat pranota uang rit supir batam'],
            ['name' => 'pranota-uang-rit-batam-update', 'description' => 'Mengubah pranota uang rit supir batam'],
            ['name' => 'pranota-uang-rit-batam-delete', 'description' => 'Menghapus pranota uang rit supir batam'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                ['description' => $permission['description'], 'created_at' => now(), 'updated_at' => now()]
            );

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->where('name', 'like', 'pranota-uang-rit-batam-%')->delete();
    }
};
