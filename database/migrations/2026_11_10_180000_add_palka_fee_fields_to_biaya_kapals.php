<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->unsignedInteger('pelindo_jumlah_palka')->nullable();
            $table->decimal('pelindo_biaya_palka', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->dropColumn(['pelindo_jumlah_palka', 'pelindo_biaya_palka']);
        });
    }
};
