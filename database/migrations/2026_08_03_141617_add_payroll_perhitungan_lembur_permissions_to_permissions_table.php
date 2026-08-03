<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            ['name' => 'payroll-perhitungan-lembur-view', 'description' => 'View Perhitungan Lembur'],
            ['name' => 'payroll-perhitungan-lembur-create', 'description' => 'Buat Perhitungan Lembur'],
            ['name' => 'payroll-perhitungan-lembur-edit', 'description' => 'Edit Perhitungan Lembur'],
            ['name' => 'payroll-perhitungan-lembur-delete', 'description' => 'Hapus Perhitungan Lembur'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
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
        $permissions = [
            'payroll-perhitungan-lembur-view',
            'payroll-perhitungan-lembur-create',
            'payroll-perhitungan-lembur-edit',
            'payroll-perhitungan-lembur-delete',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
