<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'master-pricelist-ob-antar-gudang-view' => 'Melihat pricelist OB Antar Gudang',
            'master-pricelist-ob-antar-gudang-create' => 'Menambah pricelist OB Antar Gudang',
            'master-pricelist-ob-antar-gudang-update' => 'Mengubah pricelist OB Antar Gudang',
            'master-pricelist-ob-antar-gudang-delete' => 'Menghapus pricelist OB Antar Gudang',
        ] as $name => $description) {
            Permission::firstOrCreate(['name' => $name], ['description' => $description]);
        }
    }

    public function down(): void
    {
        Permission::whereIn('name', [
            'master-pricelist-ob-antar-gudang-view',
            'master-pricelist-ob-antar-gudang-create',
            'master-pricelist-ob-antar-gudang-update',
            'master-pricelist-ob-antar-gudang-delete',
        ])->delete();
    }
};
