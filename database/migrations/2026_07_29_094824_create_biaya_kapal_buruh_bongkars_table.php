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
        Schema::create('biaya_kapal_buruh_bongkars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->foreign('biaya_kapal_id', 'fk_bkbb_biaya_kapal_id')->references('id')->on('biaya_kapals')->onDelete('cascade');
            $table->string('nama_pengirim')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('biaya_kapal_buruh_bongkar_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('biaya_kapal_buruh_bongkar_id');
            $table->foreign('biaya_kapal_buruh_bongkar_id', 'fk_bkbb_details_bkbb_id')->references('id')->on('biaya_kapal_buruh_bongkars')->onDelete('cascade');
            $table->unsignedBigInteger('manifest_id')->nullable();
            $table->foreign('manifest_id', 'fk_bkbb_details_manifest_id')->references('id')->on('manifests')->onDelete('set null');
            $table->string('surat_jalan_tipe')->nullable()->comment('To distinguish regular SJ and Batam SJ if needed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_buruh_bongkar_details');
        Schema::dropIfExists('biaya_kapal_buruh_bongkars');
    }
};
