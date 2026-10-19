<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_buruh_bongkars', function (Blueprint $table) {
            $table->foreignId('penerima_bukti_potong_pph_id')->nullable()
                ->constrained('master_penerima_bukti_potong_pph')->nullOnDelete();
            $table->string('npwp', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_buruh_bongkars', function (Blueprint $table) {
            $table->dropConstrainedForeignId('penerima_bukti_potong_pph_id');
            $table->dropColumn('npwp');
        });
    }
};
