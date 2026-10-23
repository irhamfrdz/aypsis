<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_opp_opt_manifest_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_opp_opt_id')->constrained('jurnal_opp_opts')->cascadeOnDelete();
            $table->string('nomor_kontainer')->nullable();
            $table->string('nomor_bl')->nullable();
            $table->decimal('saldo', 18, 2)->default(0);
            $table->decimal('kredit', 18, 2)->default(0);
            $table->timestamps();
            $table->index(['nomor_kontainer', 'nomor_bl'], 'jurnal_opp_manifest_ref_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_opp_opt_manifest_items');
    }
};
