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
        Schema::create('gaji_supir_batams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('restrict');
            $table->integer('periode_bulan');
            $table->integer('periode_tahun');
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('tunjangan_kehadiran', 15, 2)->default(0);
            $table->decimal('tunjangan_makan', 15, 2)->default(0);
            $table->decimal('tunjangan_lainnya', 15, 2)->default(0);
            $table->decimal('potongan_bpjs', 15, 2)->default(0);
            $table->decimal('potongan_pinjaman', 15, 2)->default(0);
            $table->decimal('potongan_lainnya', 15, 2)->default(0);
            $table->decimal('total_gaji', 15, 2)->default(0);
            $table->enum('status_pembayaran', ['PENDING', 'PAID', 'CANCELLED'])->default('PENDING');
            $table->date('tanggal_dibayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Unique constraint to ensure a driver only gets one salary record per period
            $table->unique(['karyawan_id', 'periode_bulan', 'periode_tahun'], 'unique_gaji_supir_period');
        });

        // Setup permissions
        $permissions = [
            ['name' => 'gaji-supir-batam-view', 'description' => 'Melihat data Gaji Supir Batam'],
            ['name' => 'gaji-supir-batam-create', 'description' => 'Membuat Gaji Supir Batam'],
            ['name' => 'gaji-supir-batam-edit', 'description' => 'Mengubah Gaji Supir Batam'],
            ['name' => 'gaji-supir-batam-delete', 'description' => 'Menghapus Gaji Supir Batam'],
            ['name' => 'gaji-supir-batam-export', 'description' => 'Export data Gaji Supir Batam'],
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
        }

        // Assign to Admin role
        $adminRole = DB::table('roles')->where('name', 'like', '%administrator%')->orWhere('name', 'like', '%admin%')->first();
        if ($adminRole) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', array_column($permissions, 'name'))
                ->pluck('id');

            foreach ($permissionIds as $pId) {
                DB::table('permission_role')->updateOrInsert(
                    ['permission_id' => $pId, 'role_id' => $adminRole->id]
                );
            }
        }

        // Assign directly to admin user
        $adminUser = DB::table('users')->where('username', 'admin')->first();
        if ($adminUser) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', array_column($permissions, 'name'))
                ->pluck('id');

            foreach ($permissionIds as $pId) {
                DB::table('user_permissions')->updateOrInsert(
                    ['permission_id' => $pId, 'user_id' => $adminUser->id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_supir_batams');

        $permissionNames = [
            'gaji-supir-batam-view',
            'gaji-supir-batam-create',
            'gaji-supir-batam-edit',
            'gaji-supir-batam-delete',
            'gaji-supir-batam-export',
        ];

        // Detach from role
        $adminRole = DB::table('roles')->where('name', 'like', '%administrator%')->orWhere('name', 'like', '%admin%')->first();
        if ($adminRole) {
            $permissionIds = DB::table('permissions')->whereIn('name', $permissionNames)->pluck('id');
            DB::table('permission_role')
                ->where('role_id', $adminRole->id)
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }

        // Detach from user
        $adminUser = DB::table('users')->where('username', 'admin')->first();
        if ($adminUser) {
            $permissionIds = DB::table('permissions')->whereIn('name', $permissionNames)->pluck('id');
            DB::table('user_permissions')
                ->where('user_id', $adminUser->id)
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }

        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
    }
};
