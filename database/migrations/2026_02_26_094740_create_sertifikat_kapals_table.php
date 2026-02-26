<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sertifikat_kapals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sertifikat');
            $table->text('keterangan')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert permissions
        $permissions = [
            [
                'name' => 'master-sertifikat-kapal-view',
                'description' => 'Melihat daftar sertifikat kapal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-sertifikat-kapal-create',
                'description' => 'Membuat sertifikat kapal baru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-sertifikat-kapal-update',
                'description' => 'Mengupdate sertifikat kapal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'master-sertifikat-kapal-delete',
                'description' => 'Menghapus sertifikat kapal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('permissions')->insert($permissions);

        // Assign to admin role
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', array_column($permissions, 'name'))
                ->pluck('id');

            foreach ($permissionIds as $pId) {
                DB::table('permission_role')->insertOrIgnore([
                    'permission_id' => $pId,
                    'role_id' => $adminRole->id,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove from permission_role first
        $permissions = [
            'master-sertifikat-kapal-view',
            'master-sertifikat-kapal-create',
            'master-sertifikat-kapal-update',
            'master-sertifikat-kapal-delete',
        ];

        DB::table('permission_role')->whereIn('permission_id', function($query) use ($permissions) {
            $query->select('id')->from('permissions')->whereIn('name', $permissions);
        })->delete();

        Schema::dropIfExists('sertifikat_kapals');

        DB::table('permissions')->whereIn('name', $permissions)->delete();
    }
};
