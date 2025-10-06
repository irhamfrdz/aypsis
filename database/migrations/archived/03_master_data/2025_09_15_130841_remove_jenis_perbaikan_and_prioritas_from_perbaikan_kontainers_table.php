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
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->dropColumn(['jenis_perbaikan', 'prioritas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perbaikan_kontainers', function (Blueprint $table) {
            $table->string('jenis_perbaikan')->after('tanggal_perbaikan'); // maintenance, repair, inspection
            $table->string('prioritas')->default('normal')->after('tanggal_selesai'); // low, normal, high, urgent
        });
    }
};
