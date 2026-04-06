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
            ['name' => 'gerak-kontainer-view', 'description' => 'Akses Halaman Gerak Kontainer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'gerak-kontainer-create', 'description' => 'Tambah Pergerakan Kontainer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'gerak-kontainer-update', 'description' => 'Edit Pergerakan Kontainer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'gerak-kontainer-delete', 'description' => 'Hapus Pergerakan Kontainer', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('permissions')->insert($permissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'gerak-kontainer-view',
            'gerak-kontainer-create',
            'gerak-kontainer-update',
            'gerak-kontainer-delete',
        ])->delete();
    }
};
