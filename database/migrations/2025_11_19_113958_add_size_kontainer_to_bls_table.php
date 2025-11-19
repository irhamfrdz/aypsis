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
        Schema::table('bls', function (Blueprint $table) {
            $table->string('size_kontainer', 20)->nullable()->after('tipe_kontainer')->comment('Size kontainer seperti 20ft, 40ft, 40hc, 45ft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bls', function (Blueprint $table) {
            $table->dropColumn('size_kontainer');
        });
    }
};
