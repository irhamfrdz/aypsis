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
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->string('nomor_tanda_terima')->nullable()->after('no_surat_jalan');
            $table->unique('nomor_tanda_terima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->dropUnique('tanda_terimas_nomor_tanda_terima_unique');
            $table->dropColumn('nomor_tanda_terima');
        });
    }
};
