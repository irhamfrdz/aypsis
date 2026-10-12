<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jam_kerjas')) {
            return;
        }

        Schema::table('jam_kerjas', function (Blueprint $table) {
            if (! Schema::hasColumn('jam_kerjas', 'jam_masuk_sabtu')) {
                $table->time('jam_masuk_sabtu')->nullable();
            }
            if (! Schema::hasColumn('jam_kerjas', 'jam_keluar_sabtu')) {
                $table->time('jam_keluar_sabtu')->nullable();
            }
            if (! Schema::hasColumn('jam_kerjas', 'toleransi_keterlambatan_sabtu')) {
                $table->integer('toleransi_keterlambatan_sabtu')->default(0);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('jam_kerjas')) {
            return;
        }

        Schema::table('jam_kerjas', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('jam_kerjas', 'jam_masuk_sabtu') ? 'jam_masuk_sabtu' : null,
                Schema::hasColumn('jam_kerjas', 'jam_keluar_sabtu') ? 'jam_keluar_sabtu' : null,
                Schema::hasColumn('jam_kerjas', 'toleransi_keterlambatan_sabtu') ? 'toleransi_keterlambatan_sabtu' : null,
            ]);

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
