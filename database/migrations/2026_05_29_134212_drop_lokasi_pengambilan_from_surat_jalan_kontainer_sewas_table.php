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
        Schema::table('surat_jalan_kontainer_sewas', function (Blueprint $table) {
            $table->dropColumn('lokasi_pengambilan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_kontainer_sewas', function (Blueprint $table) {
            $table->string('lokasi_pengambilan')->nullable();
        });
    }
};
