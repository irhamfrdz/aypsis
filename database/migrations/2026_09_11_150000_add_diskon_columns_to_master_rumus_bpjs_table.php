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
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->string('diskon_status', 20)->default('tidak_ada')->after('keterangan_custom');
            $table->string('diskon_tipe', 20)->default('persen')->after('diskon_status');
            $table->decimal('diskon_nilai', 15, 2)->default(0)->after('diskon_tipe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->dropColumn(['diskon_status', 'diskon_tipe', 'diskon_nilai']);
        });
    }
};
