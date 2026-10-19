<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_perlengkapan', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('no_voyage');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_perlengkapan', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
};
