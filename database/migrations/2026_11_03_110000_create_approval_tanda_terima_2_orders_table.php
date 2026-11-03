<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_tanda_terima_2_orders', function (Blueprint $table) {
            $table->string('source_type', 4);
            $table->unsignedBigInteger('source_id');
            $table->unsignedBigInteger('position');
            $table->timestamps();
            $table->primary(['source_type', 'source_id']);
            $table->index(['source_type', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_tanda_terima_2_orders');
    }
};
