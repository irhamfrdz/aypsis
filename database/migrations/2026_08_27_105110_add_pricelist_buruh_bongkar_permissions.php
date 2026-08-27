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
            ['name' => 'master-pricelist-buruh-bongkar-view', 'description' => 'View Pricelist Buruh Bongkar'],
            ['name' => 'master-pricelist-buruh-bongkar-create', 'description' => 'Buat Pricelist Buruh Bongkar'],
            ['name' => 'master-pricelist-buruh-bongkar-update', 'description' => 'Edit Pricelist Buruh Bongkar'],
            ['name' => 'master-pricelist-buruh-bongkar-delete', 'description' => 'Hapus Pricelist Buruh Bongkar'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'master-pricelist-buruh-bongkar-view',
            'master-pricelist-buruh-bongkar-create',
            'master-pricelist-buruh-bongkar-update',
            'master-pricelist-buruh-bongkar-delete',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
