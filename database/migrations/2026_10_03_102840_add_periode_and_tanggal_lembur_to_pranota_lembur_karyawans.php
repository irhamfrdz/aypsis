<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pranota_lembur_karyawan_headers')) {
            Schema::table('pranota_lembur_karyawan_headers', function (Blueprint $table) {
                if (! Schema::hasColumn('pranota_lembur_karyawan_headers', 'periode_mulai')) {
                    $table->date('periode_mulai')->nullable()->after('tanggal_pranota');
                }
                if (! Schema::hasColumn('pranota_lembur_karyawan_headers', 'periode_selesai')) {
                    $table->date('periode_selesai')->nullable()->after('periode_mulai');
                }
            });
        }

        if (Schema::hasTable('pranota_lembur_karyawans')) {
            Schema::table('pranota_lembur_karyawans', function (Blueprint $table) {
                if (! Schema::hasColumn('pranota_lembur_karyawans', 'periode_mulai')) {
                    $table->date('periode_mulai')->nullable()->after('karyawan_id');
                }
                if (! Schema::hasColumn('pranota_lembur_karyawans', 'periode_selesai')) {
                    $table->date('periode_selesai')->nullable()->after('periode_mulai');
                }
                if (! Schema::hasColumn('pranota_lembur_karyawans', 'tanggal_lembur')) {
                    $table->json('tanggal_lembur')->nullable()->after('periode_selesai');
                }
            });
        }

        // Backfill existing pranota headers & items if empty
        try {
            $headers = DB::table('pranota_lembur_karyawan_headers')->whereNull('deleted_at')->get();
            foreach ($headers as $h) {
                if (! empty($h->tanggal_pranota)) {
                    $date = \Carbon\Carbon::parse($h->tanggal_pranota);
                    $startOfMonth = $date->copy()->startOfMonth()->toDateString();
                    $endOfMonth = $date->copy()->endOfMonth()->toDateString();

                    DB::table('pranota_lembur_karyawan_headers')
                        ->where('id', $h->id)
                        ->whereNull('periode_mulai')
                        ->update([
                            'periode_mulai' => $startOfMonth,
                            'periode_selesai' => $endOfMonth,
                        ]);

                    DB::table('pranota_lembur_karyawans')
                        ->where('pranota_lembur_karyawan_header_id', $h->id)
                        ->whereNull('periode_mulai')
                        ->update([
                            'periode_mulai' => $startOfMonth,
                            'periode_selesai' => $endOfMonth,
                        ]);
                }
            }
        } catch (\Throwable $e) {
            // Ignore backfill error on fresh or empty db
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pranota_lembur_karyawan_headers')) {
            Schema::table('pranota_lembur_karyawan_headers', function (Blueprint $table) {
                if (Schema::hasColumn('pranota_lembur_karyawan_headers', 'periode_mulai')) {
                    $table->dropColumn('periode_mulai');
                }
                if (Schema::hasColumn('pranota_lembur_karyawan_headers', 'periode_selesai')) {
                    $table->dropColumn('periode_selesai');
                }
            });
        }

        if (Schema::hasTable('pranota_lembur_karyawans')) {
            Schema::table('pranota_lembur_karyawans', function (Blueprint $table) {
                if (Schema::hasColumn('pranota_lembur_karyawans', 'periode_mulai')) {
                    $table->dropColumn('periode_mulai');
                }
                if (Schema::hasColumn('pranota_lembur_karyawans', 'periode_selesai')) {
                    $table->dropColumn('periode_selesai');
                }
                if (Schema::hasColumn('pranota_lembur_karyawans', 'tanggal_lembur')) {
                    $table->dropColumn('tanggal_lembur');
                }
            });
        }
    }
};
