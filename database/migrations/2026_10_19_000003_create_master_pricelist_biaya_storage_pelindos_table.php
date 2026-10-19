<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_pricelist_biaya_storage_pelindos', function (Blueprint $table) {
            $table->id();
            $table->string('vendor');
            $table->string('lokasi');
            $table->string('size_kontainer', 50);
            $table->decimal('tarif_massa_1', 15, 2)->default(0);
            $table->decimal('tarif_massa_2', 15, 2)->nullable();
            $table->string('status', 20)->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['vendor', 'lokasi', 'size_kontainer'], 'storage_pelindo_lookup_index');
            $table->index('status');
        });

        $permissions = [
            'master-pricelist-biaya-storage-pelindo-view' => 'Melihat Pricelist Biaya Storage Pelindo',
            'master-pricelist-biaya-storage-pelindo-create' => 'Menambah Pricelist Biaya Storage Pelindo',
            'master-pricelist-biaya-storage-pelindo-update' => 'Mengubah Pricelist Biaya Storage Pelindo',
            'master-pricelist-biaya-storage-pelindo-delete' => 'Menghapus Pricelist Biaya Storage Pelindo',
        ];

        foreach ($permissions as $name => $description) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $permissionId = DB::table('permissions')->where('name', $name)->value('id');
            if ($permissionId && DB::table('users')->where('id', 1)->exists()) {
                DB::table('user_permissions')->updateOrInsert(
                    ['user_id' => 1, 'permission_id' => $permissionId],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('master_pricelist_biaya_storage_pelindos');

        $names = [
            'master-pricelist-biaya-storage-pelindo-view',
            'master-pricelist-biaya-storage-pelindo-create',
            'master-pricelist-biaya-storage-pelindo-update',
            'master-pricelist-biaya-storage-pelindo-delete',
        ];
        $permissionIds = DB::table('permissions')->whereIn('name', $names)->pluck('id');

        DB::table('user_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
