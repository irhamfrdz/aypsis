<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $permissionNames = [
        'master-pricelist-tujuan-kontainer-sewa-view',
        'master-pricelist-tujuan-kontainer-sewa-create',
        'master-pricelist-tujuan-kontainer-sewa-update',
        'master-pricelist-tujuan-kontainer-sewa-delete',
    ];

    public function up(): void
    {
        if (Schema::hasTable('permissions')) {
            $permissionIds = DB::table('permissions')->whereIn('name', $this->permissionNames)->pluck('id');

            if ($permissionIds->isNotEmpty()) {
                if (Schema::hasTable('user_permissions')) {
                    DB::table('user_permissions')->whereIn('permission_id', $permissionIds)->delete();
                }
                if (Schema::hasTable('permission_role')) {
                    DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
                }

                DB::table('permissions')->whereIn('id', $permissionIds)->delete();
            }
        }

        Schema::dropIfExists('master_pricelist_tujuan_kontainer_sewas');
    }

    public function down(): void
    {
        Schema::create('master_pricelist_tujuan_kontainer_sewas', function (Blueprint $table) {
            $table->id();
            $table->string('tujuan');
            $table->decimal('ongkos_truk_20ft', 15, 2)->default(0);
            $table->decimal('ongkos_truk_40ft', 15, 2)->default(0);
            $table->string('keterangan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });

        if (Schema::hasTable('permissions')) {
            foreach ($this->permissionNames as $name) {
                DB::table('permissions')->insertOrIgnore([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
