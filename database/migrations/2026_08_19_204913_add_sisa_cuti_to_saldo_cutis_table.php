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
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->integer('sisa_cuti')->default(0)->after('cuti_terpakai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saldo_cutis', function (Blueprint $table) {
            $table->dropColumn('sisa_cuti');
        });
    }
};
