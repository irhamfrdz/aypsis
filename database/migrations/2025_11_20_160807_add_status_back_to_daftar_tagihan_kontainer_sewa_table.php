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
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // Tambahkan kembali kolom status setelah kolom tarif
            if (!Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'status')) {
                $table->string('status')->nullable()->after('tarif');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // Hapus kolom status
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
