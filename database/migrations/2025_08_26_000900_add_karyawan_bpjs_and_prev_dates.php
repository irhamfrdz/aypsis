<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds bpjs_jamsostek and previous date columns to karyawans table.
     *
     * Note: adjust column types if your project uses different conventions.
     */
    public function up()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            if (!Schema::hasColumn('karyawans', 'bpjs_jamsostek')) {
                $table->string('bpjs_jamsostek')->nullable()->after('jkn');
            }
            if (!Schema::hasColumn('karyawans', 'tanggal_masuk_sebelumnya')) {
                $table->date('tanggal_masuk_sebelumnya')->nullable()->after('tanggal_masuk');
            }
            if (!Schema::hasColumn('karyawans', 'tanggal_berhenti_sebelumnya')) {
                $table->date('tanggal_berhenti_sebelumnya')->nullable()->after('tanggal_berhenti');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            if (Schema::hasColumn('karyawans', 'bpjs_jamsostek')) {
                $table->dropColumn('bpjs_jamsostek');
            }
            if (Schema::hasColumn('karyawans', 'tanggal_masuk_sebelumnya')) {
                $table->dropColumn('tanggal_masuk_sebelumnya');
            }
            if (Schema::hasColumn('karyawans', 'tanggal_berhenti_sebelumnya')) {
                $table->dropColumn('tanggal_berhenti_sebelumnya');
            }
        });
    }
};
