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
        Schema::table('tanda_terima_surat_jalan_kontainer_sewas', function (Blueprint $table) {
            $table->date('tanggal_mulai_sewa')->nullable()->after('tanggal_tanda_terima');
            if (Schema::hasColumn('tanda_terima_surat_jalan_kontainer_sewas', 'no_seal')) {
                $table->dropColumn('no_seal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_surat_jalan_kontainer_sewas', function (Blueprint $table) {
            $table->dropColumn('tanggal_mulai_sewa');
            $table->string('no_seal')->nullable()->after('nomor_kontainer');
        });
    }
};
