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
        Schema::create('biaya_kapal_perijinan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_perijinan_id')->constrained('biaya_kapal_perijinan')->onDelete('cascade');
            $table->foreignId('pricelist_perijinan_id')->nullable()->constrained('pricelist_perijinans')->onDelete('set null');
            $table->string('nama_perijinan')->nullable();
            $table->decimal('tarif', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_perijinan_details');
    }
};
