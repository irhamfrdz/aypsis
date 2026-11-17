<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('surat_jalan_bongkaran_tanjung_pinangs', 'manifest_id')) {
            Schema::table('surat_jalan_bongkaran_tanjung_pinangs', function (Blueprint $table) {
                $table->unsignedBigInteger('manifest_id')->nullable()->index();
            });
        }

        if (Schema::hasTable('surat_jalan_bongkarans') && Schema::hasColumn('surat_jalan_bongkarans', 'manifest_id')) {
            DB::table('surat_jalan_bongkaran_tanjung_pinangs as tp')
                ->join('surat_jalan_bongkarans as old', 'tp.id', '=', 'old.id')
                ->where('old.lokasi', 'tanjung_pinang')
                ->whereNull('tp.manifest_id')
                ->update(['tp.manifest_id' => DB::raw('old.manifest_id')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('surat_jalan_bongkaran_tanjung_pinangs', 'manifest_id')) {
            Schema::table('surat_jalan_bongkaran_tanjung_pinangs', function (Blueprint $table) {
                $table->dropIndex(['manifest_id']);
                $table->dropColumn('manifest_id');
            });
        }
    }
};
