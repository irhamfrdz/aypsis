<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tagihan_kontainer_sewa_kontainers')) {
            Schema::dropIfExists('tagihan_kontainer_sewa_kontainers');
        }

        Schema::create('tagihan_kontainer_sewa_kontainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan_kontainer_sewa')->onDelete('cascade');
            $table->foreignId('kontainer_id')->constrained('kontainers')->onDelete('cascade');
            $table->timestamps();

            // short unique name
            $table->unique(['tagihan_id', 'kontainer_id'], 'tagihan_kontainer_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tagihan_kontainer_sewa_kontainers');
    }
};
