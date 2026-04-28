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
        Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table) {
            if (Schema::hasColumn('surat_jalan_tarik_kosong_batams', 'no_tiket_do')) {
                $table->dropColumn('no_tiket_do');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table) {
            $table->string('no_tiket_do')->nullable();
        });
    }
};
