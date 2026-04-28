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
            $table->dropColumn(['pengirim', 'penerima', 'alamat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_tarik_kosong_batams', function (Blueprint $table) {
            $table->string('pengirim')->nullable();
            $table->string('penerima')->nullable();
            $table->text('alamat')->nullable();
        });
    }
};
