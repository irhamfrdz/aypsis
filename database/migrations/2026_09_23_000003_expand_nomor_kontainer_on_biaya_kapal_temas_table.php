<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_temas', function (Blueprint $table) {
            $table->text('nomor_kontainer')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_temas', function (Blueprint $table) {
            $table->string('nomor_kontainer')->nullable()->change();
        });
    }
};
