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
        Schema::table('biaya_kapal_perijinan', function (Blueprint $table) {
            $table->dropColumn('nama_perijinan');
            $table->string('nomor_referensi')->nullable()->after('no_voyage');
            $table->string('vendor')->nullable()->after('nomor_referensi');
            $table->decimal('biaya_insa', 15, 2)->nullable()->after('vendor');
            $table->decimal('biaya_pbni', 15, 2)->nullable()->after('biaya_insa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_perijinan', function (Blueprint $table) {
            $table->string('nama_perijinan')->nullable()->after('no_voyage');
            $table->dropColumn(['nomor_referensi', 'vendor', 'biaya_insa', 'biaya_pbni']);
        });
    }
};
