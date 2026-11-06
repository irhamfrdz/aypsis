<?php

use App\Models\AuditLog;
use App\Models\BiayaBensin;
use App\Models\MasterKartuBensinBatam;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $expense = BiayaBensin::query()->with('mobil')->lockForUpdate()->find(127);
            if (! $expense || filled($expense->nomor_kartu)) {
                return;
            }

            $cardNumber = '20000063108';
            $wasRemovedFromCard = AuditLog::query()
                ->where('auditable_type', BiayaBensin::class)
                ->where('auditable_id', $expense->id)
                ->where('action', 'updated')
                ->get()
                ->contains(fn (AuditLog $audit) =>
                    data_get($audit->old_values, 'nomor_kartu') === $cardNumber
                    && blank(data_get($audit->new_values, 'nomor_kartu'))
                );
            if (! $wasRemovedFromCard) {
                return;
            }

            $card = MasterKartuBensinBatam::query()->where('nomor_kartu', $cardNumber)->lockForUpdate()->firstOrFail();
            $histories = $card->histories()
                ->where('tipe', 'berkurang')
                ->where('nominal', $expense->biaya)
                ->where('created_by', $expense->created_by)
                ->where('keterangan', 'Pengisian bensin Kendaraan '.$expense->mobil?->nomor_polisi)
                ->whereBetween('created_at', [
                    $expense->created_at->copy()->subMinute(),
                    $expense->created_at->copy()->addMinute(),
                ])
                ->lockForUpdate()
                ->get();

            if ($histories->isEmpty()) {
                return; // Sudah diperbaiki atau tidak ada potongan yang dapat dipastikan.
            }
            if ($histories->count() !== 1) {
                throw new RuntimeException('Potongan kartu untuk BBS-000127 tidak tunggal.');
            }

            $charge = $histories->first();
            $amount = (float) $charge->nominal;
            if (abs((float) $charge->saldo_sebelum - (float) $charge->saldo_sesudah - $amount) > 0.01) {
                throw new RuntimeException('Nominal dan saldo potongan BBS-000127 tidak sesuai.');
            }

            $lastHistory = $card->histories()->orderByDesc('id')->lockForUpdate()->first();
            if (abs((float) $lastHistory->saldo_sesudah - (float) $card->saldo) > 0.01) {
                throw new RuntimeException('Saldo kartu tidak cocok dengan riwayat terakhir.');
            }

            $laterHistories = $card->histories()->where('id', '>', $charge->id)
                ->orderBy('id')->lockForUpdate()->get();
            $charge->delete();
            foreach ($laterHistories as $history) {
                $history->updateQuietly([
                    'saldo_sebelum' => round((float) $history->saldo_sebelum + $amount, 2),
                    'saldo_sesudah' => round((float) $history->saldo_sesudah + $amount, 2),
                ]);
            }
            $card->update(['saldo' => round((float) $card->saldo + $amount, 2)]);
        });
    }

    public function down(): void
    {
        // Koreksi saldo historis tidak dapat dibalik tanpa memulihkan data awal.
    }
};
