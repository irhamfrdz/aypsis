<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->boolean('is_ckls_mobil_panjang')->default(false)->after('is_combo');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_ob', function (Blueprint $table) {
            $table->dropColumn('is_ckls_mobil_panjang');
        });
    }
};
