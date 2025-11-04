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
        // Remove all tarif-batam related permissions
        \App\Models\Permission::where('name', 'LIKE', 'tarif-batam%')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate tarif-batam permissions if needed for rollback
        $permissions = [
            'tarif-batam.view',
            'tarif-batam.create', 
            'tarif-batam.edit',
            'tarif-batam.delete'
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::firstOrCreate(['name' => $permission]);
        }
    }
};
