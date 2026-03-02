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
        Schema::create('master_pricelist_lolos', function (Blueprint $table) {
            $table->id();
            $table->string('terminal');
            $table->string('size'); // 20, 40, 45, etc.
            $table->string('kategori'); // Full, Empty
            $table->string('tipe_aktivitas'); // Lift On, Lift Off
            $table->decimal('tarif', 15, 2)->default(0);
            $table->string('status')->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pricelist_lolos');
    }
};
