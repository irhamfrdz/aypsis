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
        // Drop tables dalam urutan yang benar (child table dulu, parent table kemudian)
        Schema::dropIfExists('prospek_kapal_kontainers');
        Schema::dropIfExists('prospek_kapals');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate tables jika perlu rollback (opsional)
        // Kita tidak akan implement ini karena tabelnya sudah tidak digunakan
    }
};
