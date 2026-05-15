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
        Schema::create('master_pricelist_tujuan_kontainer_sewas', function (Blueprint $table) {
            $table->id();
            $table->string('tujuan');
            $table->decimal('ongkos_truk_20ft', 15, 2)->default(0);
            $table->decimal('ongkos_truk_40ft', 15, 2)->default(0);
            $table->string('keterangan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pricelist_tujuan_kontainer_sewas');
    }
};
