<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->string('nomor_kontainer')->nullable();
            $table->decimal('dpp', 15, 2)->nullable();
            $table->decimal('ppn', 15, 2)->nullable();
            $table->decimal('pph', 15, 2)->nullable();
            $table->decimal('grand_total', 15, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->dropColumn(['nomor_kontainer', 'dpp', 'ppn', 'pph', 'grand_total']);
        });
    }
};
