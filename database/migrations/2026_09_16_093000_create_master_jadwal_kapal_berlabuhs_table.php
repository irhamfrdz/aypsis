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
        Schema::create('master_jadwal_kapal_berlabuhs', function (Blueprint $table) {
            $table->id();
            $table->string('pelabuhan')->comment('Nama Pelabuhan / Rute Tujuan (misal: TANJUNG PINANG)');
            $table->foreignId('master_pelabuhan_id')->nullable()->constrained('master_pelabuhans')->nullOnDelete();
            $table->string('nama_kapal')->comment('Nama Kapal (misal: KM SEKAR PERMATA)');
            $table->foreignId('master_kapal_id')->nullable()->constrained('master_kapals')->nullOnDelete();
            $table->string('no_voyage')->nullable()->comment('Nomor Voyage');
            $table->date('tanggal_closing')->nullable()->comment('Tanggal Closing');
            $table->date('tanggal_etd')->nullable()->comment('Estimated Time of Departure');
            $table->date('tanggal_eta')->nullable()->comment('Estimated Time of Arrival / Labuh');
            $table->string('status')->default('aktif')->comment('aktif, selesai, batal');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['pelabuhan', 'status']);
            $table->index('tanggal_etd');
            $table->index('tanggal_eta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_jadwal_kapal_berlabuhs');
    }
};
