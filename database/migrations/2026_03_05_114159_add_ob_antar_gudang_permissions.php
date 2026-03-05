<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            [
                'name' => 'ob-antar-gudang-view',
                'description' => 'Melihat halaman OB Antar Gudang'
            ],
            [
                'name' => 'ob-antar-gudang-create',
                'description' => 'Menambah data OB Antar Gudang'
            ],
            [
                'name' => 'ob-antar-gudang-update',
                'description' => 'Mengedit data OB Antar Gudang'
            ],
            [
                'name' => 'ob-antar-gudang-delete',
                'description' => 'Menghapus data OB Antar Gudang'
            ],
        ];

        foreach ($permissions as $permissionData) {
            $existing = Permission::where('name', $permissionData['name'])->first();

            if (!$existing) {
                Permission::create($permissionData);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = [
            'ob-antar-gudang-view',
            'ob-antar-gudang-create',
            'ob-antar-gudang-update',
            'ob-antar-gudang-delete',
        ];

        foreach ($permissionNames as $name) {
            Permission::where('name', $name)->delete();
        }
    }
};
