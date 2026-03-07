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
            ['name' => 'pranota-stock-amprahan-view', 'description' => 'View Pranota Stock Amprahan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-stock-amprahan-create', 'description' => 'Create Pranota Stock Amprahan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-stock-amprahan-edit', 'description' => 'Edit Pranota Stock Amprahan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-stock-amprahan-delete', 'description' => 'Delete Pranota Stock Amprahan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-stock-amprahan-export', 'description' => 'Export Pranota Stock Amprahan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-stock-amprahan-print', 'description' => 'Print Pranota Stock Amprahan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pranota-stock-amprahan-approval', 'description' => 'Approval Pranota Stock Amprahan', 'created_at' => now(), 'updated_at' => now()],
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
            'pranota-stock-amprahan-view',
            'pranota-stock-amprahan-create',
            'pranota-stock-amprahan-edit',
            'pranota-stock-amprahan-delete',
            'pranota-stock-amprahan-export',
            'pranota-stock-amprahan-print',
            'pranota-stock-amprahan-approval',
        ];

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
