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
        Schema::table('buruhs', function (Blueprint $table) {
            $table->string('status_bpjs')->default('tidak aktif')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buruhs', function (Blueprint $table) {
            $table->dropColumn('status_bpjs');
        });
    }
};
