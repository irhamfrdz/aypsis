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
                'name' => 'tanda-terima-bongkaran-batam-view',
                'description' => 'Akses Menu Tanda Terima Bongkaran Batam'
            ],
            [
                'name' => 'tanda-terima-bongkaran-batam-create',
                'description' => 'Membuat Tanda Terima Bongkaran Batam'
            ],
            [
                'name' => 'tanda-terima-bongkaran-batam-update',
                'description' => 'Mengupdate Tanda Terima Bongkaran Batam'
            ],
            [
                'name' => 'tanda-terima-bongkaran-batam-delete',
                'description' => 'Menghapus Tanda Terima Bongkaran Batam'
            ],
            [
                'name' => 'tanda-terima-bongkaran-batam-print',
                'description' => 'Mencetak Tanda Terima Bongkaran Batam'
            ],
            [
                'name' => 'tanda-terima-bongkaran-batam-export',
                'description' => 'Export Tanda Terima Bongkaran Batam'
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
            'tanda-terima-bongkaran-batam-view',
            'tanda-terima-bongkaran-batam-create',
            'tanda-terima-bongkaran-batam-update',
            'tanda-terima-bongkaran-batam-delete',
            'tanda-terima-bongkaran-batam-print',
            'tanda-terima-bongkaran-batam-export',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
