<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Buat tabel untuk menyimpan hutang dan tabungan per supir per pranota
        Schema::create('pranota_uang_rit_supir_details', function (Blueprint $table) {
            $table->id();
            $table->string('no_pranota'); // Nomor pranota (group identifier)
            $table->string('supir_nama'); // Nama supir
            $table->decimal('total_uang_supir', 15, 2)->default(0); // Total uang supir dari semua surat jalan
            $table->decimal('hutang', 15, 2)->default(0); // Hutang per supir
            $table->decimal('tabungan', 15, 2)->default(0); // Tabungan per supir
            $table->decimal('grand_total', 15, 2)->default(0); // Grand total (uang_supir - hutang - tabungan)
            $table->timestamps();

            // Indexes
            $table->index('no_pranota');
            $table->index('supir_nama');
            $table->unique(['no_pranota', 'supir_nama']); // Satu record per supir per pranota
        });

        // Tambahkan kolom ke tabel pranota_uang_rits untuk menyimpan total keseluruhan
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            $table->decimal('total_hutang', 15, 2)->default(0)->after('total_uang'); // Total hutang keseluruhan
            $table->decimal('total_tabungan', 15, 2)->default(0)->after('total_hutang'); // Total tabungan keseluruhan
            $table->decimal('grand_total_bersih', 15, 2)->default(0)->after('total_tabungan'); // Grand total bersih
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tabel pranota_uang_rit_supir_details
        Schema::dropIfExists('pranota_uang_rit_supir_details');

        // Hapus kolom yang ditambahkan
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            $table->dropColumn(['total_hutang', 'total_tabungan', 'grand_total_bersih']);
        });
    }
};
