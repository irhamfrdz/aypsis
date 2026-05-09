<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'langsir-batam-view',
            'langsir-batam-create',
            'langsir-batam-update',
            'langsir-batam-delete',
        ];

        foreach ($permissions as $name) {
            \App\Models\Permission::firstOrCreate(['name' => $name]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'langsir-batam-view',
            'langsir-batam-create',
            'langsir-batam-update',
            'langsir-batam-delete',
        ];

        \App\Models\Permission::whereIn('name', $permissions)->delete();
    }
};
