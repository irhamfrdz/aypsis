<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropTagihanKontainerSewaKontainersTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('tagihan_kontainer_sewa_kontainers')) {
            Schema::dropIfExists('tagihan_kontainer_sewa_kontainers');
        }
    }

    public function down()
    {
        Schema::create('tagihan_kontainer_sewa_kontainers', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tagihan_id');
            $table->unsignedBigInteger('kontainer_id');
            $table->timestamps();
            $table->unique(['tagihan_id','kontainer_id']);
        });
    }
}
