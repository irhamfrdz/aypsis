<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_kapal_temas_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id')->constrained('biaya_kapals')->cascadeOnDelete();
            $table->string('kapal');
            $table->string('voyage');
            $table->string('payment_mode', 20)->default('lunas');
            $table->foreignId('dp_stage_id')->nullable()->constrained('biaya_kapal_temas_stages')->restrictOnDelete();
            $table->decimal('nilai_tagihan', 15, 2)->default(0);
            $table->decimal('dp_diperhitungkan', 15, 2)->default(0);
            $table->decimal('nominal_dibayar', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::table('biaya_kapal_temas', function (Blueprint $table) {
            $table->foreignId('temas_stage_id')->nullable()->constrained('biaya_kapal_temas_stages')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_temas', fn (Blueprint $table) => $table->dropConstrainedForeignId('temas_stage_id'));
        Schema::dropIfExists('biaya_kapal_temas_stages');
    }
};
