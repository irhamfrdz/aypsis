<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $records = DB::table('master_rumus_bpjs')->where('jenis', 'jamsostek')
            ->where('group_name', 'BPU-ALEXINDO PLUIT')->whereRaw('UPPER(TRIM(tipe)) = ?', ['CREW'])->get();
        foreach ($records as $record) {
            $tiers = json_decode($record->hutang_tiers ?? '[]', true);
            if (!is_array($tiers)) continue;
            foreach ($tiers as &$tier) {
                $amount = match ((int) ($tier['dpp'] ?? 0)) {
                    2200000 => 14000,
                    4450000 => 25000,
                    default => null,
                };
                if ($amount !== null && !isset($tier['potongan_tanpa_diskon'])) {
                    $tier['potongan_tanpa_diskon'] = $amount;
                }
            }
            unset($tier);
            DB::table('master_rumus_bpjs')->where('id', $record->id)->update(['hutang_tiers' => json_encode($tiers)]);
        }
    }

    public function down(): void
    {
        // Preserve edited master formulas and saved pranota snapshots.
    }
};
