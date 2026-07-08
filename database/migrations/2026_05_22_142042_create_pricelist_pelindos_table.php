<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create table
        Schema::create('pricelist_pelindos', function (Blueprint $table) {
            $table->id();
            $table->string('kegiatan');
            $table->string('ukuran')->nullable();
            $table->decimal('tarif', 15, 2);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            $table->index('status');
            $table->index('kegiatan');
        });

        // 2. Add permissions
        $permissions = [
            [
                'name' => 'master-pricelist-pelindo-view',
                'description' => 'Melihat data Master Pricelist Pelindo',
            ],
            [
                'name' => 'master-pricelist-pelindo-create',
                'description' => 'Menambah data Master Pricelist Pelindo',
            ],
            [
                'name' => 'master-pricelist-pelindo-update',
                'description' => 'Mengubah data Master Pricelist Pelindo',
            ],
            [
                'name' => 'master-pricelist-pelindo-delete',
                'description' => 'Menghapus data Master Pricelist Pelindo',
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Give permission to admin (user_id = 1)
            $permissionId = DB::table('permissions')->where('name', $permission['name'])->value('id');
            if ($permissionId && DB::table('users')->where('id', 1)->exists()) {
                DB::table('user_permissions')->updateOrInsert(
                    ['user_id' => 1, 'permission_id' => $permissionId],
                    []
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop table
        Schema::dropIfExists('pricelist_pelindos');

        // 2. Remove permissions
        $permissionNames = [
            'master-pricelist-pelindo-view',
            'master-pricelist-pelindo-create',
            'master-pricelist-pelindo-update',
            'master-pricelist-pelindo-delete',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table('user_permissions')
                ->whereIn('permission_id', $permissionIds)
                ->delete();

            DB::table('permissions')
                ->whereIn('id', $permissionIds)
                ->delete();
        }
    }
};
