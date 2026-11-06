<?php

namespace App\Services;

use App\Models\BiayaBensin;
use App\Models\AuditLog;
use App\Models\MasterKartuBensinBatam;
use App\Models\MasterKartuBensinBatamHistory;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class BiayaBensinCardService
{
    public function removeUsage(BiayaBensin $item, string $cardNumber): void
    {
        $card = MasterKartuBensinBatam::where('nomor_kartu', $cardNumber)->lockForUpdate()->first();
        if (! $card) {
            throw ValidationException::withMessages(['nomor_kartu' => "Kartu bensin {$cardNumber} tidak ditemukan."]);
        }

        $histories = $card->histories()->orderBy('id')->lockForUpdate()->get();
        $relatedIds = $histories->where('nomor_transaksi', $item->nomor_transaksi)->pluck('id')->all();

        // Riwayat lama belum menyimpan nomor transaksi; cocokkan dengan audit saat dibuat.
        $created = AuditLog::where('auditable_type', BiayaBensin::class)
            ->where('auditable_id', $item->id)->where('action', 'created')->first();
        $initial = $created?->new_values;
        if ($created && data_get($initial, 'nomor_kartu') === $cardNumber) {
            $legacy = $histories->filter(fn (MasterKartuBensinBatamHistory $history) =>
                blank($history->nomor_transaksi)
                && $history->tipe === 'berkurang'
                && (int) $history->created_by === (int) data_get($initial, 'created_by')
                && abs($history->created_at->diffInSeconds($created->created_at)) <= 60
                && abs((float) $history->nominal - (float) data_get($initial, 'biaya')) < 0.01
                && str_starts_with((string) $history->keterangan, 'Pengisian bensin Kendaraan ')
            );
            if ($legacy->count() > 1) {
                throw new RuntimeException("Riwayat lama {$item->nomor_transaksi} tidak tunggal.");
            }
            $relatedIds = array_merge($relatedIds, $legacy->pluck('id')->all());
        }

        if ($relatedIds === []) {
            throw ValidationException::withMessages([
                'nomor_kartu' => "Riwayat pemakaian {$item->nomor_transaksi} pada kartu {$cardNumber} tidak ditemukan.",
            ]);
        }

        $firstId = min($relatedIds);
        $relevant = $histories->filter(fn ($history) => $history->id >= $firstId)->values();
        $last = $histories->last();
        if (abs((float) $last->saldo_sesudah - (float) $card->saldo) >= 0.01) {
            throw new RuntimeException("Saldo kartu {$cardNumber} tidak cocok dengan riwayat terakhir.");
        }

        $balance = (float) $relevant->first()->saldo_sebelum;
        $previousOriginalBalance = $balance;
        foreach ($relevant as $history) {
            if (abs((float) $history->saldo_sebelum - $previousOriginalBalance) >= 0.01) {
                throw new RuntimeException("Urutan saldo kartu {$cardNumber} tidak konsisten.");
            }
            $signedAmount = match ($history->tipe) {
                'bertambah' => (float) $history->nominal,
                'berkurang' => -(float) $history->nominal,
                default => throw new RuntimeException("Tipe riwayat kartu {$cardNumber} tidak dikenal."),
            };
            if (abs((float) $history->saldo_sesudah - ((float) $history->saldo_sebelum + $signedAmount)) >= 0.01) {
                throw new RuntimeException("Nominal riwayat kartu {$cardNumber} tidak cocok dengan saldonya.");
            }
            $previousOriginalBalance = (float) $history->saldo_sesudah;

            if (in_array($history->id, $relatedIds, true)) {
                $history->delete();
                continue;
            }
            $nextBalance = round($balance + $signedAmount, 2);
            $history->updateQuietly(['saldo_sebelum' => $balance, 'saldo_sesudah' => $nextBalance]);
            $balance = $nextBalance;
        }
        $card->update(['saldo' => $balance]);
    }

    public function adjust(
        BiayaBensin $item,
        ?string $oldCardNumber,
        float $oldAmount,
        ?string $newCardNumber,
        float $newAmount,
        string $description
    ): void {
        $deltas = [];

        if (filled($oldCardNumber)) {
            $deltas[$oldCardNumber] = round(($deltas[$oldCardNumber] ?? 0) + $oldAmount, 2);
        }

        if (filled($newCardNumber)) {
            $deltas[$newCardNumber] = round(($deltas[$newCardNumber] ?? 0) - $newAmount, 2);
        }

        $cards = MasterKartuBensinBatam::whereIn('nomor_kartu', array_map('strval', array_keys($deltas)))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('nomor_kartu');

        foreach ($deltas as $cardNumber => $delta) {
            if (abs($delta) < 0.01) {
                continue;
            }

            $card = $cards->get($cardNumber);
            if (! $card) {
                throw ValidationException::withMessages([
                    'nomor_kartu' => "Kartu bensin {$cardNumber} tidak ditemukan. Saldo tidak diubah.",
                ]);
            }

            $saldoSebelum = (float) $card->saldo;
            $saldoSesudah = round($saldoSebelum + $delta, 2);
            $card->update(['saldo' => $saldoSesudah]);

            $card->histories()->create([
                'nomor_transaksi' => $item->nomor_transaksi,
                'tanggal' => now(),
                'tipe' => $delta > 0 ? 'bertambah' : 'berkurang',
                'nominal' => abs($delta),
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'keterangan' => $description,
                'created_by' => auth()->id(),
            ]);
        }
    }
}
