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
            // Payroll Root
            ['name' => 'payroll-view', 'description' => 'View Data Payroll'],
            ['name' => 'payroll-create', 'description' => 'Buat Data Payroll'],
            ['name' => 'payroll-edit', 'description' => 'Edit Data Payroll'],
            ['name' => 'payroll-delete', 'description' => 'Hapus Data Payroll'],
            
            // Payroll Uang Karyawan (Master Data Uang Lembur)
            ['name' => 'payroll-uang-karyawan-view', 'description' => 'View Data Uang Karyawan'],
            ['name' => 'payroll-uang-karyawan-create', 'description' => 'Buat Data Uang Karyawan'],
            ['name' => 'payroll-uang-karyawan-edit', 'description' => 'Edit Data Uang Karyawan'], // we use edit to be consistent with controller
            ['name' => 'payroll-uang-karyawan-delete', 'description' => 'Hapus Data Uang Karyawan'],
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
            'payroll-view',
            'payroll-create',
            'payroll-edit',
            'payroll-delete',
            'payroll-uang-karyawan-view',
            'payroll-uang-karyawan-create',
            'payroll-uang-karyawan-edit',
            'payroll-uang-karyawan-delete',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
