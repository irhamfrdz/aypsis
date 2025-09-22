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
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->string('jenis_catatan')->nullable()->after('catatan');
            $table->string('teknisi')->nullable()->after('jenis_catatan');
            $table->string('prioritas')->default('normal')->after('teknisi');
            $table->text('sparepart_dibutuhkan')->nullable()->after('prioritas');
            $table->date('tanggal_catatan')->nullable()->after('sparepart_dibutuhkan');
            $table->string('estimasi_waktu')->nullable()->after('tanggal_catatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->dropColumn(['jenis_catatan', 'teknisi', 'prioritas', 'sparepart_dibutuhkan', 'tanggal_catatan', 'estimasi_waktu']);
        });
    }
};
