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
                'name' => 'surat-jalan-bongkaran-batam-view',
                'description' => 'View Surat Jalan Bongkaran Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'surat-jalan-bongkaran-batam-create',
                'description' => 'Create Surat Jalan Bongkaran Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'surat-jalan-bongkaran-batam-update',
                'description' => 'Update Surat Jalan Bongkaran Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'surat-jalan-bongkaran-batam-delete',
                'description' => 'Delete Surat Jalan Bongkaran Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'surat-jalan-bongkaran-batam-print',
                'description' => 'Print Surat Jalan Bongkaran Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'surat-jalan-bongkaran-batam-export',
                'description' => 'Export Surat Jalan Bongkaran Batam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                $permission
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = [
            'surat-jalan-bongkaran-batam-view',
            'surat-jalan-bongkaran-batam-create',
            'surat-jalan-bongkaran-batam-update',
            'surat-jalan-bongkaran-batam-delete',
            'surat-jalan-bongkaran-batam-print',
            'surat-jalan-bongkaran-batam-export',
        ];

        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
