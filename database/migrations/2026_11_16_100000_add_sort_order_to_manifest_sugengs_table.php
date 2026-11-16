<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manifest_sugengs', function (Blueprint $table) {
            $table->unsignedBigInteger('sort_order')->nullable()->index();
        });

        $position = 0;
        DB::table('manifest_sugengs')
            ->orderBy('nama_kapal')
            ->orderBy('no_voyage')
            ->orderByRaw("CASE WHEN source_type = 'lcl' OR UPPER(COALESCE(tipe_kontainer, '')) LIKE '%LCL%' THEN 0 ELSE 1 END")
            ->orderBy('nomor_kontainer')
            ->orderBy('id')
            ->select('id')
            ->lazy()
            ->each(function ($item) use (&$position): void {
                DB::table('manifest_sugengs')->where('id', $item->id)->update(['sort_order' => ++$position]);
            });
    }

    public function down(): void
    {
        Schema::table('manifest_sugengs', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
