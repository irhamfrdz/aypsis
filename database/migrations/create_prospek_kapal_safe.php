<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProspekKapalTableSafe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Cek apakah table sudah ada
        if (!Schema::hasTable('prospek_kapal')) {
            Schema::create('prospek_kapal', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pergerakan_kapal_id')->nullable();
                $table->string('voyage');
                $table->string('nama_kapal');
                $table->dateTime('tanggal_loading');
                $table->dateTime('estimasi_departure')->nullable();
                $table->integer('jumlah_kontainer_terjadwal')->default(0);
                $table->integer('jumlah_kontainer_loaded')->default(0);
                $table->enum('status', ['draft', 'scheduled', 'loading', 'completed', 'cancelled'])->default('draft');
                $table->text('keterangan')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prospek_kapal');
    }
}