<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_pricelist_ob_antar_gudang', function (Blueprint $table) {
            $table->id();
            $table->string('size_kontainer');
            $table->enum('status_kontainer', ['full', 'empty']);
            $table->enum('status_service', ['service', 'non_service'])->default('non_service');
            $table->decimal('biaya', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->unique(['size_kontainer', 'status_kontainer', 'status_service'], 'ob_antar_gudang_pricelist_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_pricelist_ob_antar_gudang');
    }
};
