<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Backfill existing rows to have `masa` as an Indonesian date-range string like
     * "21 januari 2025 - 20 februari 2025" and ensure the column is VARCHAR.
     */
    public function up()
    {
        // Ensure column is at least varchar(255)
        try {
            DB::statement('ALTER TABLE daftar_tagihan_kontainer_sewa MODIFY masa VARCHAR(255) NULL');
        } catch (\Exception $e) {
            try {
                Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
                    $table->string('masa')->nullable()->change();
                });
            } catch (\Exception $e2) {
                // ignore if cannot change
            }
        }

        // Backfill rows: compute period end = tanggal_awal +1 month -1 day, cap by tanggal_akhir
        try {
            $rows = DB::table('daftar_tagihan_kontainer_sewa')->select('id', 'tanggal_awal', 'tanggal_akhir')->get();
            foreach ($rows as $r) {
                if (empty($r->tanggal_awal)) continue;
                try {
                    $start = Carbon::parse($r->tanggal_awal);
                    $endCandidate = $start->copy()->addMonthsNoOverflow(1)->subDay();
                    if (!empty($r->tanggal_akhir)) {
                        $tk = Carbon::parse($r->tanggal_akhir);
                        $end = $tk->lessThan($endCandidate) ? $tk : $endCandidate;
                    } else {
                        $end = $endCandidate;
                    }
                    $startStr = strtolower($start->locale('id')->isoFormat('D MMMM YYYY'));
                    $endStr = strtolower($end->locale('id')->isoFormat('D MMMM YYYY'));
                    $masaStr = $startStr . ' - ' . $endStr;
                    DB::table('daftar_tagihan_kontainer_sewa')->where('id', $r->id)->update(['masa' => $masaStr]);
                } catch (\Exception $e) {
                    // ignore row-level parse errors
                }
            }
        } catch (\Exception $e) {
            // ignore
        }
    }

    public function down()
    {
        // No reliable reverse; leave as-is
        try {
            // Optionally change back to varchar as already ensured
            DB::statement('ALTER TABLE daftar_tagihan_kontainer_sewa MODIFY masa VARCHAR(255) NULL');
        } catch (\Exception $e) {
            try {
                Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
                    $table->string('masa')->nullable()->change();
                });
            } catch (\Exception $e2) {
                // ignore
            }
        }
    }
};
