<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPembayaranAktivitasLainnyaItemsTable extends Migration
{
    /**
     * Run the migrations.
     * Drop pembayaran_aktivitas_lainnya_items table.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('pembayaran_aktivitas_lainnya_items');
    }

    /**
     * Reverse the migrations.
     * Recreate the table if needed (optional).
     *
     * @return void
     */
    public function down()
    {
        // Uncomment below if you want to recreate table on rollback
        /*
        Schema::create('pembayaran_aktivitas_lainnya_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayaran_aktivitas_lainnya')->onDelete('cascade');
            $table->foreignId('aktivitas_id')->constrained('aktivitas_lainnya')->onDelete('cascade');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
        */
    }
}
