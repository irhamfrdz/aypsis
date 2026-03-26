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
