<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\BiayaBensin;
use App\Models\MasterKartuBensinBatam;
use RuntimeException;

class LegacyFuelCardHistoryRepair
{
    /**
     * Repair a proven legacy fuel-card charge. The caller must hold a database transaction.
     *
     * @param  'removed'|'deleted'|'amount'  $reason
     */
    public function repair(
        int $fuelId,
        string $cardNumber,
        float $chargedAmount,
        ?float $correctAmount,
        string $reason,
        string $description
    ): void
    {
        $audits = AuditLog::query()->where('auditable_type', BiayaBensin::class)
            ->where('auditable_id', $fuelId)->orderBy('id')->get();
        $created = $audits->firstWhere('action', 'created');
        if (! $created) {
            return; // Database ini tidak memiliki transaksi tersebut.
        }

        $initial = $created->new_values;
        if (data_get($initial, 'nomor_kartu') !== $cardNumber
            || ! $this->sameAmount(data_get($initial, 'biaya'), $chargedAmount)) {
            throw new RuntimeException("Audit awal BBS-{$fuelId} tidak sesuai dengan potongan yang akan diperbaiki.");
        }

        $expense = BiayaBensin::query()->lockForUpdate()->find($fuelId);
        $changedAsExpected = $audits->contains(function (AuditLog $audit) use ($reason, $cardNumber, $chargedAmount, $correctAmount): bool {
            $old = $audit->old_values;
            $new = $audit->new_values;
            if ($reason === 'deleted') {
                return $audit->action === 'deleted'
                    && data_get($old, 'nomor_kartu') === $cardNumber
                    && $this->sameAmount(data_get($old, 'biaya'), $chargedAmount);
            }
            if ($audit->action !== 'updated' || data_get($old, 'nomor_kartu') !== $cardNumber
                || ! $this->sameAmount(data_get($old, 'biaya'), $chargedAmount)) {
                return false;
            }

            return $reason === 'removed'
                ? blank(data_get($new, 'nomor_kartu'))
                : data_get($new, 'nomor_kartu') === $cardNumber
                    && $this->sameAmount(data_get($new, 'biaya'), $correctAmount);
        });
        $validCurrentState = match ($reason) {
            'removed' => $expense && blank($expense->nomor_kartu),
            'deleted' => ! $expense,
            'amount' => $expense && $expense->nomor_kartu === $cardNumber
                && $this->sameAmount($expense->biaya, $correctAmount),
            default => false,
        };
        if (! $changedAsExpected || ! $validCurrentState) {
            throw new RuntimeException("Status atau audit BBS-{$fuelId} tidak sesuai; koreksi dibatalkan.");
        }

        $card = MasterKartuBensinBatam::query()->where('nomor_kartu', $cardNumber)->lockForUpdate()->firstOrFail();
        $chargeQuery = $card->histories()
            ->where('tipe', 'berkurang')
            ->where('created_by', data_get($initial, 'created_by'))
            ->where('keterangan', $description)
            ->whereBetween('created_at', [
                $created->created_at->copy()->subMinute(),
                $created->created_at->copy()->addMinute(),
            ]);
        $charges = (clone $chargeQuery)->where('nominal', $chargedAmount)->lockForUpdate()->get();
        if ($charges->isEmpty()) {
            if ($reason !== 'amount' || (clone $chargeQuery)->where('nominal', $correctAmount)->count() === 1) {
                return; // Potongan sudah dihapus atau nominalnya sudah dikoreksi.
            }
            throw new RuntimeException("Potongan asal BBS-{$fuelId} tidak ditemukan.");
        }
        if ($charges->count() !== 1) {
            throw new RuntimeException("Potongan asal BBS-{$fuelId} tidak tunggal.");
        }

        $charge = $charges->first();
        if (! $this->sameAmount((float) $charge->saldo_sebelum - (float) $charge->saldo_sesudah, $chargedAmount)) {
            throw new RuntimeException("Saldo potongan BBS-{$fuelId} tidak sesuai.");
        }
        $lastHistory = $card->histories()->orderByDesc('id')->lockForUpdate()->first();
        if (! $lastHistory || ! $this->sameAmount($lastHistory->saldo_sesudah, $card->saldo)) {
            throw new RuntimeException("Saldo kartu {$cardNumber} tidak cocok dengan riwayat terakhir.");
        }

        $delta = $chargedAmount - ($correctAmount ?? 0);
        $laterHistories = $card->histories()->where('id', '>', $charge->id)
            ->orderBy('id')->lockForUpdate()->get();
        if ($reason === 'amount') {
            $charge->updateQuietly([
                'nominal' => $correctAmount,
                'saldo_sesudah' => round((float) $charge->saldo_sebelum - $correctAmount, 2),
            ]);
        } else {
            $charge->delete();
        }
        foreach ($laterHistories as $history) {
            $history->updateQuietly([
                'saldo_sebelum' => round((float) $history->saldo_sebelum + $delta, 2),
                'saldo_sesudah' => round((float) $history->saldo_sesudah + $delta, 2),
            ]);
        }
        $card->update(['saldo' => round((float) $card->saldo + $delta, 2)]);
    }

    private function sameAmount(mixed $left, mixed $right): bool
    {
        return is_numeric($left) && is_numeric($right) && abs((float) $left - (float) $right) < 0.01;
    }
}
