<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_pelindos', function (Blueprint $table) {
            $table->unsignedInteger('jumlah')->nullable();
            $table->decimal('biaya', 15, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_pelindos', function (Blueprint $table) {
            $table->dropColumn(['jumlah', 'biaya']);
        });
    }
};
