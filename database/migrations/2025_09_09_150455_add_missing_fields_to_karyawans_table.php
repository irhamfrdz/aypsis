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
        // Make this migration safe to run multiple times by checking for each column first.
        if (!Schema::hasColumn('karyawans', 'tanggal_masuk_sebelumnya')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->date('tanggal_masuk_sebelumnya')->nullable()->after('tanggal_berhenti');
            });
        }

        if (!Schema::hasColumn('karyawans', 'tanggal_berhenti_sebelumnya')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->date('tanggal_berhenti_sebelumnya')->nullable()->after('tanggal_masuk_sebelumnya');
            });
        }

        if (!Schema::hasColumn('karyawans', 'catatan')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('tanggal_berhenti_sebelumnya');
            });
        }

        if (!Schema::hasColumn('karyawans', 'bank_cabang')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->string('bank_cabang')->nullable()->after('nama_bank');
            });
        }

        if (!Schema::hasColumn('karyawans', 'no_ketenagakerjaan')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->string('no_ketenagakerjaan')->nullable()->after('jkn');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop columns that exist to make rollback safe.
        if (Schema::hasColumn('karyawans', 'no_ketenagakerjaan')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->dropColumn('no_ketenagakerjaan');
            });
        }

        if (Schema::hasColumn('karyawans', 'bank_cabang')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->dropColumn('bank_cabang');
            });
        }

        if (Schema::hasColumn('karyawans', 'catatan')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->dropColumn('catatan');
            });
        }

        if (Schema::hasColumn('karyawans', 'tanggal_berhenti_sebelumnya')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->dropColumn('tanggal_berhenti_sebelumnya');
            });
        }

        if (Schema::hasColumn('karyawans', 'tanggal_masuk_sebelumnya')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->dropColumn('tanggal_masuk_sebelumnya');
            });
        }
    }
};
