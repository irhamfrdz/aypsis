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
            ['name' => 'master-dokumen-kapal-alexindo-view', 'description' => 'View Dokumen Kapal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-dokumen-kapal-alexindo-create', 'description' => 'Create Dokumen Kapal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-dokumen-kapal-alexindo-edit', 'description' => 'Update Dokumen Kapal', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'master-dokumen-kapal-alexindo-delete', 'description' => 'Delete Dokumen Kapal', 'created_at' => now(), 'updated_at' => now()],
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
        $permissions = [
            'master-dokumen-kapal-alexindo-view',
            'master-dokumen-kapal-alexindo-create',
            'master-dokumen-kapal-alexindo-edit',
            'master-dokumen-kapal-alexindo-delete',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
