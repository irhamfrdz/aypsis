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
                'name' => 'order-batam-view',
                'description' => 'Melihat data Order Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'order-batam-create',
                'description' => 'Membuat data Order Batam baru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'order-batam-update',
                'description' => 'Mengubah data Order Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'order-batam-delete',
                'description' => 'Menghapus data Order Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('permissions')->insertOrIgnore($permissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'order-batam-view',
            'order-batam-create',
            'order-batam-update',
            'order-batam-delete'
        ])->delete();
    }
};
