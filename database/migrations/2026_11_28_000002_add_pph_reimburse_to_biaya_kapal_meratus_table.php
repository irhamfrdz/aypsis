<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->boolean('pph_reimburse')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_meratus', function (Blueprint $table) {
            $table->dropColumn('pph_reimburse');
        });
    }
};
