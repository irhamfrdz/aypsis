<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPembayaranAktivitasLainyaItemsTables extends Migration
{
    /**
     * Run the migrations.
     * Drop specified tables if they exist.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('aktivitas');
        Schema::dropIfExists('lainya');
        Schema::dropIfExists('items');
    }

    /**
     * Reverse the migrations.
     * You can recreate the tables here if needed.
     *
     * @return void
     */
    public function down()
    {
        // TODO: Define schema to recreate tables if necessary.
    }
}
