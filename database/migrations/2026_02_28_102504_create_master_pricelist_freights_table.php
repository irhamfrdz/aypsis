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
        Schema::create('master_pricelist_freights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelabuhan_asal_id');
            $table->unsignedBigInteger('pelabuhan_tujuan_id');
            $table->string('size_kontainer')->comment('20ft, 40ft, etc');
            $table->decimal('biaya', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('pelabuhan_asal_id')->references('id')->on('master_pelabuhans')->onDelete('cascade');
            $table->foreign('pelabuhan_tujuan_id')->references('id')->on('master_pelabuhans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pricelist_freights');
    }
};
