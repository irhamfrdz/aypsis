<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('temas_dp_references', function (Blueprint $table) {
            $table->decimal('nominal_digunakan', 15, 2)->default(0);
        });

        // Preserve the amounts already deducted by existing settlements, including audit records.
        $references = DB::table('temas_dp_references as refs')
            ->join('biaya_kapal_temas_stages as dp', 'dp.id', '=', 'refs.dp_stage_id')
            ->join('biaya_kapal_temas_stages as settlement', 'settlement.id', '=', 'refs.settlement_stage_id')
            ->select('refs.*', 'dp.nominal_dibayar', 'settlement.dp_diperhitungkan')
            ->orderBy('refs.settlement_stage_id')->orderBy('refs.dp_stage_id')->get();
        foreach ($references->groupBy('settlement_stage_id') as $items) {
            $remaining = (int) round((float) $items->first()->dp_diperhitungkan * 100);
            foreach ($items as $reference) {
                $used = max(0, min($remaining, (int) round((float) $reference->nominal_dibayar * 100)));
                DB::table('temas_dp_references')
                    ->where('settlement_stage_id', $reference->settlement_stage_id)
                    ->where('dp_stage_id', $reference->dp_stage_id)
                    ->update(['nominal_digunakan' => $used / 100]);
                $remaining -= $used;
            }
        }
    }

    public function down(): void
    {
        Schema::table('temas_dp_references', fn (Blueprint $table) => $table->dropColumn('nominal_digunakan'));
    }
};
