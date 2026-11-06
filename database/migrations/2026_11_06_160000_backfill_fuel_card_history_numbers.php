<?php

use App\Models\AuditLog;
use App\Models\BiayaBensin;
use App\Models\MasterKartuBensinBatamHistory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pasangan riwayat dan transaksi ini telah dicocokkan dengan audit pembuatan.
        $matches = [
            1 => 14,
            15 => 162,
            17 => 210,
            18 => 211,
            19 => 219,
            20 => 246,
            21 => 250,
            22 => 251,
            23 => 256,
            25 => 286,
            26 => 287,
            27 => 288,
            28 => 289,
            29 => 290,
            30 => 295,
        ];

        DB::transaction(function () use ($matches): void {
            foreach ($matches as $historyId => $fuelId) {
                $history = MasterKartuBensinBatamHistory::query()
                    ->with('card')->lockForUpdate()->find($historyId);
                if (! $history || filled($history->nomor_transaksi)) {
                    continue;
                }

                $created = AuditLog::query()->where('auditable_type', BiayaBensin::class)
                    ->where('auditable_id', $fuelId)->where('action', 'created')->sole();
                $initial = $created->new_values;
                $expense = BiayaBensin::query()->findOrFail($fuelId);
                $sameAmount = fn ($value) => is_numeric($value)
                    && abs((float) $value - (float) $history->nominal) < 0.01;

                if ($history->tipe !== 'berkurang'
                    || ! str_starts_with((string) $history->keterangan, 'Pengisian bensin Kendaraan ')
                    || data_get($initial, 'nomor_kartu') !== $history->card?->nomor_kartu
                    || (int) data_get($initial, 'created_by') !== (int) $history->created_by
                    || abs($created->created_at->diffInSeconds($history->created_at)) > 2
                    || (! $sameAmount(data_get($initial, 'biaya')) && ! $sameAmount($expense->biaya))) {
                    throw new RuntimeException("Riwayat kartu {$historyId} tidak cocok dengan audit BBS-{$fuelId}.");
                }

                $history->updateQuietly(['nomor_transaksi' => $expense->nomor_transaksi]);
            }
        });
    }

    public function down(): void
    {
        // Nomor transaksi yang telah diverifikasi tidak dihapus kembali.
    }
};
