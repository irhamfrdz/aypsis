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
        Schema::table('sertifikat_kapals', function (Blueprint $table) {
            $table->boolean('has_masa_berlaku')->default(true)->after('nickname');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sertifikat_kapals', function (Blueprint $table) {
            $table->dropColumn('has_masa_berlaku');
        });
    }
};
