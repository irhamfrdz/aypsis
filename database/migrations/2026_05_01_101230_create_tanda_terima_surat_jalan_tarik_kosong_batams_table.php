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
        Schema::create('tanda_terima_surat_jalan_tarik_kosong_batams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_jalan_tarik_kosong_batam_id')->nullable();
            $table->string('no_tanda_terima')->unique();
            $table->date('tanggal_tanda_terima');
            
            // Redundant fields for easier reporting/viewing (pattern used in this repo)
            $table->string('no_surat_jalan')->nullable();
            $table->date('tanggal_surat_jalan')->nullable();
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('size')->nullable();
            
            $table->string('penerima')->nullable();
            $table->text('catatan')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('surat_jalan_tarik_kosong_batam_id', 'tt_sjtkb_sj_foreign')
                  ->references('id')
                  ->on('surat_jalan_tarik_kosong_batams')
                  ->onDelete('set null');
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_surat_jalan_tarik_kosong_batams');
    }
};
