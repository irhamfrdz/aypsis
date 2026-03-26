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
            ['name' => 'pranota-uang-jalan-batam-view', 'description' => 'Melihat list pranota uang jalan batam'],
            ['name' => 'pranota-uang-jalan-batam-create', 'description' => 'Membuat pranota uang jalan batam'],
            ['name' => 'pranota-uang-jalan-batam-update', 'description' => 'Mengubah pranota uang jalan batam'],
            ['name' => 'pranota-uang-jalan-batam-delete', 'description' => 'Menghapus pranota uang jalan batam'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->where('name', 'like', 'pranota-uang-jalan-batam-%')->delete();
    }
};
