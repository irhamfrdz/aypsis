<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->string('nomor_kontainer_kedua')->nullable()->after('nomor_kontainer');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->dropColumn('nomor_kontainer_kedua');
        });
    }
};
