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
            [
                'name' => 'tanda-terima-batam-view',
                'description' => 'Akses Menu Tanda Terima Batam'
            ],
            [
                'name' => 'tanda-terima-batam-create',
                'description' => 'Membuat Tanda Terima Batam'
            ],
            [
                'name' => 'tanda-terima-batam-update',
                'description' => 'Mengupdate Tanda Terima Batam'
            ],
            [
                'name' => 'tanda-terima-batam-delete',
                'description' => 'Menghapus Tanda Terima Batam'
            ],
        ];

        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')->where('name', $permission['name'])->exists();
            if (!$exists) {
                DB::table('permissions')->insert([
                    'name' => $permission['name'],
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'tanda-terima-batam-view',
            'tanda-terima-batam-create',
            'tanda-terima-batam-update',
            'tanda-terima-batam-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
