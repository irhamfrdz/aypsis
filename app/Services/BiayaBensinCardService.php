<?php

namespace App\Services;

use App\Models\BiayaBensin;
use App\Models\MasterKartuBensinBatam;
use Illuminate\Validation\ValidationException;

class BiayaBensinCardService
{
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
