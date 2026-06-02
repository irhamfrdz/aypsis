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
        Schema::table('pengirims', function (Blueprint $table) {
            $table->string('nickname1')->nullable()->after('nama_pengirim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengirims', function (Blueprint $table) {
            $table->dropColumn('nickname1');
        });
    }
};
