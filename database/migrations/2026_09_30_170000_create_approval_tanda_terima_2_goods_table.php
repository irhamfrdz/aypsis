<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_tanda_terima_2_goods', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 4);
            $table->unsignedBigInteger('source_id');
            $table->json('goods');
            $table->text('keterangan_barang')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['source_type', 'source_id'], 'approval_tt2_goods_source_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_tanda_terima_2_goods');
    }
};
