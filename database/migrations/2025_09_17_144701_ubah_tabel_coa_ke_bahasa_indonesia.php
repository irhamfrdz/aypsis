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
        // Buat tabel baru dengan nama bahasa Indonesia
        Schema::create('akun_coa', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_akun', 20)->unique();
            $table->string('nama_akun', 255);
            $table->enum('tipe_akun', ['Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban']);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });

        // Migrasi data dari tabel lama ke tabel baru
        DB::statement("
            INSERT INTO akun_coa (nomor_akun, nama_akun, tipe_akun, saldo, created_at, updated_at)
            SELECT
                account_number,
                account_name,
                CASE
                    WHEN account_type = 'Asset' THEN 'Aset'
                    WHEN account_type = 'Liability' THEN 'Kewajiban'
                    WHEN account_type = 'Equity' THEN 'Ekuitas'
                    WHEN account_type = 'Revenue' THEN 'Pendapatan'
                    WHEN account_type = 'Expense' THEN 'Beban'
                    ELSE account_type
                END,
                balance,
                created_at,
                updated_at
            FROM coas
        ");

        // Hapus tabel lama
        Schema::dropIfExists('coas');
    }

    public function down(): void
    {
        // Buat tabel lama kembali
        Schema::create('coas', function (Blueprint $table) {
            $table->id();
            $table->string('account_number', 20)->unique();
            $table->string('account_name', 255);
            $table->enum('account_type', ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense']);
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
        });

        // Migrasi data kembali ke tabel lama
        DB::statement("
            INSERT INTO coas (account_number, account_name, account_type, balance, created_at, updated_at)
            SELECT
                nomor_akun,
                nama_akun,
                CASE
                    WHEN tipe_akun = 'Aset' THEN 'Asset'
                    WHEN tipe_akun = 'Kewajiban' THEN 'Liability'
                    WHEN tipe_akun = 'Ekuitas' THEN 'Equity'
                    WHEN tipe_akun = 'Pendapatan' THEN 'Revenue'
                    WHEN tipe_akun = 'Beban' THEN 'Expense'
                    ELSE tipe_akun
                END,
                saldo,
                created_at,
                updated_at
            FROM akun_coa
        ");

        // Hapus tabel baru
        Schema::dropIfExists('akun_coa');
    }
};
