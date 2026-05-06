<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btm_sewa_permohonan_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->date('tanggal');
            $table->string('vendor_name'); 
            $table->string('bank')->nullable();
            $table->string('jenis_transaksi')->default('Kredit');
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('total_penyesuaian', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->text('alasan_penyesuaian')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status')->default('PENDING'); // PENDING, PAID, REJECTED
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('btm_sewa_permohonan_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('btm_sewa_permohonan_transfers')->onDelete('cascade');
            $table->foreignId('btm_sewa_pranota_id')->constrained('btm_sewa_pranotas');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        Schema::table('btm_sewa_payments', function (Blueprint $table) {
            $table->foreignId('btm_sewa_permohonan_transfer_id')->after('id')->nullable()->constrained('btm_sewa_permohonan_transfers');
        });
    }

    public function down(): void
    {
        Schema::table('btm_sewa_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('btm_sewa_permohonan_transfer_id');
        });
        Schema::dropIfExists('btm_sewa_permohonan_transfer_details');
        Schema::dropIfExists('btm_sewa_permohonan_transfers');
    }
};
