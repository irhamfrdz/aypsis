<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('checkpoints', 'bukti_muat')) {
            Schema::table('checkpoints', function (Blueprint $table) {
                $table->text('bukti_muat')->nullable()->after('gambar')->comment('JSON array of uploaded proof of loading images');
            });
        }

        if (! Schema::hasColumn('surat_jalans', 'bukti_muat')) {
            Schema::table('surat_jalans', function (Blueprint $table) {
                $table->text('bukti_muat')->nullable()->after('gambar_checkpoint')->comment('JSON array of uploaded proof of loading images');
            });
        }

        if (! Schema::hasColumn('surat_jalan_bongkarans', 'bukti_muat')) {
            Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
                $table->text('bukti_muat')->nullable()->after('gambar_checkpoint')->comment('JSON array of uploaded proof of loading images');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('checkpoints', 'bukti_muat')) {
            Schema::table('checkpoints', function (Blueprint $table) {
                $table->dropColumn('bukti_muat');
            });
        }

        if (Schema::hasColumn('surat_jalans', 'bukti_muat')) {
            Schema::table('surat_jalans', function (Blueprint $table) {
                $table->dropColumn('bukti_muat');
            });
        }

        if (Schema::hasColumn('surat_jalan_bongkarans', 'bukti_muat')) {
            Schema::table('surat_jalan_bongkarans', function (Blueprint $table) {
                $table->dropColumn('bukti_muat');
            });
        }
    }
};
