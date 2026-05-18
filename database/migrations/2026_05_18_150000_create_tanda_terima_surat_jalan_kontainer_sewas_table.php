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
        Schema::create('tanda_terima_surat_jalan_kontainer_sewas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tanda_terima');
            $table->unique('nomor_tanda_terima', 'tt_sj_ks_no_unique');
            $table->date('tanggal_tanda_terima');
            
            // Specify custom foreign key name to avoid length limits
            $table->foreignId('surat_jalan_kontainer_sewa_id')
                  ->constrained('surat_jalan_kontainer_sewas', 'id', 'tt_sj_ks_sj_ks_id_fk')
                  ->onDelete('cascade');

            $table->string('nomor_surat_jalan')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('kegiatan')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('completed');
            $table->text('keterangan')->nullable();
            $table->boolean('lembur')->default(false);
            $table->boolean('nginap')->default(false);
            $table->boolean('tidak_lembur_nginap')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_surat_jalan_kontainer_sewas');
    }
};
