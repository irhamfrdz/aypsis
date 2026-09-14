<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['view' => 'View', 'create' => 'Buat', 'update' => 'Edit', 'delete' => 'Hapus'] as $action => $label) {
            Permission::firstOrCreate([
                'name' => "master-type-bon-amprahan-{$action}",
            ], [
                'description' => "{$label} Master Type Bon Amprahan",
            ]);
        }
    }

    public function down(): void
    {
        Permission::whereIn('name', [
            'master-type-bon-amprahan-view',
            'master-type-bon-amprahan-create',
            'master-type-bon-amprahan-update',
            'master-type-bon-amprahan-delete',
        ])->delete();
    }
};
