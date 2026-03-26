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
                'name' => 'prospek-batam-view',
                'description' => 'Akses Menu Prospek Batam'
            ],
            [
                'name' => 'prospek-batam-edit',
                'description' => 'Mengedit Data Prospek Batam'
            ],
            [
                'name' => 'prospek-batam-delete',
                'description' => 'Menghapus Data Prospek Batam'
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
            'prospek-batam-view',
            'prospek-batam-edit',
            'prospek-batam-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
