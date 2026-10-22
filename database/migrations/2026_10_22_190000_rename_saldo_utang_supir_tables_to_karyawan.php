<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('saldo_utang_supirs') && ! Schema::hasTable('saldo_utang_karyawans')) {
            Schema::rename('saldo_utang_supirs', 'saldo_utang_karyawans');
        }

        if (Schema::hasTable('riwayat_utang_supirs') && ! Schema::hasTable('riwayat_utang_karyawans')) {
            Schema::rename('riwayat_utang_supirs', 'riwayat_utang_karyawans');
        }

        if (Schema::hasTable('permissions')) {
            DB::table('permissions')->where('name', 'saldo-utang-supir-view')->update([
                'name' => 'saldo-utang-karyawan-view',
                'description' => 'Melihat Saldo Utang Karyawan',
            ]);
            DB::table('permissions')->where('name', 'saldo-utang-supir-create')->update([
                'name' => 'saldo-utang-karyawan-create',
                'description' => 'Menambah Transaksi Saldo Utang Karyawan',
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permissions')) {
            DB::table('permissions')->where('name', 'saldo-utang-karyawan-view')->update([
                'name' => 'saldo-utang-supir-view',
                'description' => 'Melihat Saldo Utang Supir',
            ]);
            DB::table('permissions')->where('name', 'saldo-utang-karyawan-create')->update([
                'name' => 'saldo-utang-supir-create',
                'description' => 'Menambah Transaksi Saldo Utang Supir',
            ]);
        }

        if (Schema::hasTable('riwayat_utang_karyawans') && ! Schema::hasTable('riwayat_utang_supirs')) {
            Schema::rename('riwayat_utang_karyawans', 'riwayat_utang_supirs');
        }

        if (Schema::hasTable('saldo_utang_karyawans') && ! Schema::hasTable('saldo_utang_supirs')) {
            Schema::rename('saldo_utang_karyawans', 'saldo_utang_supirs');
        }
    }
};
