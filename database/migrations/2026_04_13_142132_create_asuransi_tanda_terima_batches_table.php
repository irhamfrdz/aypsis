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
        Schema::create('asuransi_tanda_terima_batches', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_polis')->nullable();
            $table->date('tanggal_polis');
            $table->unsignedBigInteger('vendor_asuransi_id');
            $table->decimal('total_nilai_pertanggungan', 20, 2)->default(0);
            $table->decimal('asuransi_rate', 10, 5)->default(0);
            $table->decimal('premi', 20, 2)->default(0);
            $table->decimal('biaya_admin', 20, 2)->default(0);
            $table->decimal('grand_total', 20, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->string('asuransi_path')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vendor_asuransi_id')->references('id')->on('vendor_asuransi');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });

        Schema::create('asuransi_tanda_terima_batch_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_id');
            $table->string('receipt_type'); // tt, tttsj, lcl
            $table->unsignedBigInteger('tanda_terima_id')->nullable();
            $table->unsignedBigInteger('tanda_terima_tanpa_sj_id')->nullable();
            $table->unsignedBigInteger('tanda_terima_lcl_id')->nullable();
            $table->decimal('nilai_pertanggungan', 20, 2)->default(0);
            $table->timestamps();

            $table->foreign('batch_id', 'attbi_batch_id_fk')->references('id')->on('asuransi_tanda_terima_batches')->onDelete('cascade');
            $table->foreign('tanda_terima_id', 'attbi_tt_id_fk')->references('id')->on('tanda_terimas')->onDelete('cascade');
            $table->foreign('tanda_terima_tanpa_sj_id', 'attbi_tttsj_id_fk')->references('id')->on('tanda_terima_tanpa_surat_jalan')->onDelete('cascade');
            $table->foreign('tanda_terima_lcl_id', 'attbi_lcl_id_fk')->references('id')->on('tanda_terimas_lcl')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asuransi_tanda_terima_batch_items');
        Schema::dropIfExists('asuransi_tanda_terima_batches');
    }
};
