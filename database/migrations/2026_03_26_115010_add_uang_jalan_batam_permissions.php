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
            'uang-jalan-batam-view',
            'uang-jalan-batam-create',
            'uang-jalan-batam-update',
            'uang-jalan-batam-delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'uang-jalan-batam-view',
            'uang-jalan-batam-create',
            'uang-jalan-batam-update',
            'uang-jalan-batam-delete',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
