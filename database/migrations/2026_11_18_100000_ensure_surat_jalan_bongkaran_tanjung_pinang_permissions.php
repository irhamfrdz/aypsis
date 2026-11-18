<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $permissions = [
        'surat-jalan-bongkaran-tanjung-pinang-view' => 'Lihat dan cetak Surat Jalan Bongkaran Tanjung Pinang',
        'surat-jalan-bongkaran-tanjung-pinang-create' => 'Buat Surat Jalan Bongkaran Tanjung Pinang',
        'surat-jalan-bongkaran-tanjung-pinang-update' => 'Ubah Surat Jalan Bongkaran Tanjung Pinang',
        'surat-jalan-bongkaran-tanjung-pinang-delete' => 'Hapus Surat Jalan Bongkaran Tanjung Pinang',
    ];

    public function up(): void
    {
        foreach ($this->permissions as $name => $description) {
            $permissionExists = DB::table('permissions')->where('name', $name)->exists();

            if ($permissionExists) {
                DB::table('permissions')->where('name', $name)->update([
                    'description' => $description,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('permissions')->insert([
                    'name' => $name,
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (! Schema::hasTable('roles') || ! Schema::hasTable('permission_role')) {
            return;
        }

        $adminRoleIds = DB::table('roles')
            ->whereRaw('LOWER(name) LIKE ?', ['%admin%'])
            ->pluck('id');
        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_keys($this->permissions))
            ->pluck('id');

        foreach ($adminRoleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('roles') || ! Schema::hasTable('permission_role')) {
            return;
        }

        $adminRoleIds = DB::table('roles')
            ->whereRaw('LOWER(name) LIKE ?', ['%admin%'])
            ->pluck('id');
        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_keys($this->permissions))
            ->pluck('id');

        DB::table('permission_role')
            ->whereIn('role_id', $adminRoleIds)
            ->whereIn('permission_id', $permissionIds)
            ->delete();
    }
};
