<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Strategy:
     * - add `periode_raw` (string, nullable) to keep original values
     * - add `periode_int` (integer, default 1)
     * - copy original periode -> periode_raw
     * - populate periode_int from periode when numeric (floor), otherwise default to 1
     * - drop old `periode` column and rename `periode_int` -> `periode`
     */
    public function up(): void
    {
        // add columns only if they don't already exist (idempotent)
        if (!Schema::hasColumn('tagihan_kontainer_sewa', 'periode_raw')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->string('periode_raw')->nullable()->after('tanggal_harga_awal');
            });
        }
        if (!Schema::hasColumn('tagihan_kontainer_sewa', 'periode_int')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->integer('periode_int')->default(1)->after('periode_raw');
            });
        }

        // copy original values into periode_raw
        // copy original periode into periode_raw if periode_raw is empty
        DB::table('tagihan_kontainer_sewa')
            ->whereNull('periode_raw')
            ->orWhere('periode_raw', '=', '')
            ->update(['periode_raw' => DB::raw('COALESCE(periode, "")')]);

        // Populate periode_int with a safe PHP-driven conversion (handles '2025-07', numeric floats, etc.)
    $rows = DB::table('tagihan_kontainer_sewa')->select('id', 'periode')->get();
        foreach ($rows as $row) {
            $dest = 1;
            if (!is_null($row->periode) && $row->periode !== '') {
                // numeric-like (integer or float) -> floor
                if (is_numeric($row->periode)) {
                    $dest = (int) floor((float) $row->periode);
                    if ($dest < 1) $dest = 1;
                } else {
                    // Pattern YYYY-MM or others -> default 1
                    $dest = 1;
                }
            }
            DB::table('tagihan_kontainer_sewa')->where('id', $row->id)->update(['periode_int' => $dest]);
        }

        // Drop old periode and rename periode_int -> periode if not already done
        if (Schema::hasColumn('tagihan_kontainer_sewa', 'periode')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->dropColumn('periode');
            });
        }
        if (Schema::hasColumn('tagihan_kontainer_sewa', 'periode_int') && !Schema::hasColumn('tagihan_kontainer_sewa', 'periode')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->renameColumn('periode_int', 'periode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // recreate string periode from periode_raw if available, otherwise from integer periode
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->string('periode_old')->nullable()->after('tanggal_harga_awal');
        });

        // prefer periode_raw, else cast integer periode to string
        DB::table('tagihan_kontainer_sewa')->update([
            'periode_old' => DB::raw("COALESCE(periode_raw, CAST(periode AS CHAR))"),
        ]);

        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->dropColumn('periode');
        });

        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->renameColumn('periode_old', 'periode');
            // restore original periode_raw column name if desired, keep it for audit
            // keep periode_raw as-is
        });
    }
};
