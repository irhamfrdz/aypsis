<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_kapal_meratus_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id')->constrained('biaya_kapals')->cascadeOnDelete();
            $table->string('kapal');
            $table->string('voyage');
            $table->string('payment_mode', 20)->default('lunas');
            $table->foreignId('dp_stage_id')->nullable()->constrained('biaya_kapal_meratus_stages')->restrictOnDelete();
            $table->date('tanggal_dp')->nullable();
            $table->string('nama_bank')->nullable();
            $table->text('keterangan_dp')->nullable();
            $table->decimal('nilai_tagihan', 15, 2)->default(0);
            $table->decimal('dp_diperhitungkan', 15, 2)->default(0);
            $table->decimal('nominal_dibayar', 15, 2)->default(0);
            $table->timestamps();
        });
        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->foreignId('meratus_stage_id')->nullable()->constrained('biaya_kapal_meratus_stages')->cascadeOnDelete();
            $table->string('nomor_bl')->nullable();
            $table->boolean('is_per_container')->nullable();
            $table->text('nomor_kontainer')->nullable()->change();
        });
        Schema::create('meratus_dp_references', function (Blueprint $table) {
            $table->foreignId('settlement_stage_id')->constrained('biaya_kapal_meratus_stages')->cascadeOnDelete();
            $table->foreignId('dp_stage_id')->constrained('biaya_kapal_meratus_stages')->restrictOnDelete();
            $table->decimal('nominal_digunakan', 15, 2)->default(0);
            $table->primary(['settlement_stage_id', 'dp_stage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meratus_dp_references');
        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('meratus_stage_id');
            $table->dropColumn(['nomor_bl', 'is_per_container']);
            $table->string('nomor_kontainer')->nullable()->change();
        });
        Schema::dropIfExists('biaya_kapal_meratus_stages');
    }
};
