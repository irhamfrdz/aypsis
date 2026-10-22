<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_pinjaman_karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permohonan', 40)->unique();
            $table->foreignId('karyawan_id')->constrained('karyawans')->restrictOnDelete();
            $table->date('tanggal_permohonan');
            $table->date('tanggal_dibutuhkan')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->unsignedSmallInteger('tenor_bulan')->default(1);
            $table->decimal('rencana_cicilan', 15, 2)->nullable();
            $table->text('tujuan_pinjaman');
            $table->text('catatan_pemohon')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->decimal('nominal_disetujui', 15, 2)->nullable();
            $table->unsignedSmallInteger('tenor_disetujui')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('saldo_recorded_at')->nullable();
            $table->timestamps();

            $table->index(['karyawan_id', 'status'], 'permohonan_pinjaman_karyawan_status_idx');
            $table->index('tanggal_permohonan');
        });

        $permissions = [
            'permohonan-pinjaman-karyawan-view' => 'Melihat permohonan pinjaman karyawan',
            'permohonan-pinjaman-karyawan-create' => 'Membuat permohonan pinjaman karyawan',
            'permohonan-pinjaman-karyawan-approve' => 'Menyetujui atau menolak permohonan pinjaman karyawan',
        ];

        foreach ($permissions as $name => $description) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                [
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_keys($permissions))
            ->pluck('id', 'name');

        if (Schema::hasTable('user_permissions')) {
            $saldoViewId = DB::table('permissions')->where('name', 'saldo-utang-karyawan-view')->value('id');
            $saldoCreateId = DB::table('permissions')->where('name', 'saldo-utang-karyawan-create')->value('id');

            $viewUserIds = $saldoViewId
                ? DB::table('user_permissions')->where('permission_id', $saldoViewId)->pluck('user_id')
                : collect();
            $financeUserIds = $saldoCreateId
                ? DB::table('user_permissions')->where('permission_id', $saldoCreateId)->pluck('user_id')
                : collect();

            foreach ($viewUserIds as $userId) {
                DB::table('user_permissions')->insertOrIgnore([
                    'user_id' => $userId,
                    'permission_id' => $permissionIds['permohonan-pinjaman-karyawan-view'],
                ]);
            }

            foreach ($financeUserIds as $userId) {
                foreach (['view', 'create', 'approve'] as $action) {
                    DB::table('user_permissions')->insertOrIgnore([
                        'user_id' => $userId,
                        'permission_id' => $permissionIds["permohonan-pinjaman-karyawan-{$action}"],
                    ]);
                }
            }
        }

        if (Schema::hasTable('roles') && Schema::hasTable('permission_role')) {
            $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
            if ($adminRoleId) {
                foreach ($permissionIds as $permissionId) {
                    DB::table('permission_role')->insertOrIgnore([
                        'role_id' => $adminRoleId,
                        'permission_id' => $permissionId,
                    ]);
                }

                if (Schema::hasTable('role_user') && Schema::hasTable('user_permissions')) {
                    $adminUserIds = DB::table('role_user')->where('role_id', $adminRoleId)->pluck('user_id');
                    foreach ($adminUserIds as $userId) {
                        foreach ($permissionIds as $permissionId) {
                            DB::table('user_permissions')->insertOrIgnore([
                                'user_id' => $userId,
                                'permission_id' => $permissionId,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function down(): void
    {
        $permissionNames = [
            'permohonan-pinjaman-karyawan-view',
            'permohonan-pinjaman-karyawan-create',
            'permohonan-pinjaman-karyawan-approve',
        ];
        $permissionIds = DB::table('permissions')->whereIn('name', $permissionNames)->pluck('id');

        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')->whereIn('permission_id', $permissionIds)->delete();
        }
        if (Schema::hasTable('permission_role')) {
            DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        }
        DB::table('permissions')->whereIn('name', $permissionNames)->delete();

        Schema::dropIfExists('permohonan_pinjaman_karyawans');
    }
};
