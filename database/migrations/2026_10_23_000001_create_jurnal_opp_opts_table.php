<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_opp_opts', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nomor_referensi', 100)->nullable();
            $table->text('keterangan');
            $table->decimal('saldo', 18, 2)->default(0)->comment('Penambahan saldo');
            $table->decimal('kredit', 18, 2)->default(0)->comment('Pengurangan saldo');
            $table->decimal('saldo_berjalan', 18, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tanggal', 'id']);
            $table->index('nomor_referensi');
        });

        $permissions = [
            'jurnal-opp-opt-view' => 'Melihat Jurnal OPT/OPP',
            'jurnal-opp-opt-create' => 'Menambah transaksi Jurnal OPT/OPP',
            'jurnal-opp-opt-update' => 'Mengubah transaksi Jurnal OPT/OPP',
            'jurnal-opp-opt-delete' => 'Menghapus transaksi Jurnal OPT/OPP',
        ];

        foreach ($permissions as $name => $description) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                ['description' => $description, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_keys($permissions))
            ->pluck('id');

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
            'jurnal-opp-opt-view',
            'jurnal-opp-opt-create',
            'jurnal-opp-opt-update',
            'jurnal-opp-opt-delete',
        ];
        $permissionIds = DB::table('permissions')->whereIn('name', $permissionNames)->pluck('id');

        if (Schema::hasTable('user_permissions')) {
            DB::table('user_permissions')->whereIn('permission_id', $permissionIds)->delete();
        }
        if (Schema::hasTable('permission_role')) {
            DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        }

        DB::table('permissions')->whereIn('name', $permissionNames)->delete();
        Schema::dropIfExists('jurnal_opp_opts');
    }
};
