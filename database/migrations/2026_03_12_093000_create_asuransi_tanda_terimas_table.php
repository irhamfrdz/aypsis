<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('asuransi_tanda_terimas');
        Schema::create('asuransi_tanda_terimas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_asuransi_id')->constrained('vendor_asuransi')->onDelete('cascade');
            
            // Link to the source receipts (at least one should be filled)
            $table->unsignedBigInteger('tanda_terima_id')->nullable();
            $table->unsignedBigInteger('tanda_terima_tanpa_sj_id')->nullable();
            $table->unsignedBigInteger('tanda_terima_lcl_id')->nullable();
            
            $table->string('nomor_polis')->nullable();
            $table->date('tanggal_polis')->nullable();
            $table->decimal('nilai_pertanggungan', 15, 2)->default(0);
            $table->decimal('premi', 15, 2)->default(0);
            $table->string('asuransi_path')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Aktif', 'Selesai', 'Batal'])->default('Aktif');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('tanda_terima_id')->references('id')->on('tanda_terimas')->onDelete('set null');
            $table->foreign('tanda_terima_tanpa_sj_id')->references('id')->on('tanda_terima_tanpa_surat_jalan')->onDelete('set null');
            $table->foreign('tanda_terima_lcl_id')->references('id')->on('tanda_terimas_lcl')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asuransi_tanda_terimas');
    }
};
