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
        Schema::table('uang_jalan_adjustments', function (Blueprint $table) {
            $table->enum('debit_kredit', ['debit', 'kredit'])->after('jenis_penyesuaian')->default('debit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_jalan_adjustments', function (Blueprint $table) {
            $table->dropColumn('debit_kredit');
        });
    }
};
