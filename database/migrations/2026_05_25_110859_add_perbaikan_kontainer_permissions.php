<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            [
                'name' => 'perbaikan-kontainer-view',
                'description' => 'Melihat menu dan daftar perbaikan kontainer',
            ],
            [
                'name' => 'perbaikan-kontainer-create',
                'description' => 'Menambah perbaikan kontainer',
            ],
            [
                'name' => 'perbaikan-kontainer-update',
                'description' => 'Mengedit perbaikan kontainer',
            ],
            [
                'name' => 'perbaikan-kontainer-delete',
                'description' => 'Menghapus perbaikan kontainer',
            ],
        ];

        foreach ($permissions as $permissionData) {
            $existing = Permission::where('name', $permissionData['name'])->first();

            if (! $existing) {
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
            'perbaikan-kontainer-view',
            'perbaikan-kontainer-create',
            'perbaikan-kontainer-update',
            'perbaikan-kontainer-delete',
        ];

        foreach ($permissionNames as $name) {
            Permission::where('name', $name)->delete();
        }
    }
};
