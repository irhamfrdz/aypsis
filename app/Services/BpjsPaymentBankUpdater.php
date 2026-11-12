<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\PembayaranPranotaBpjs;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BpjsPaymentBankUpdater
{
    public function change(PembayaranPranotaBpjs $payment, int $bankId, int $expectedBankId): bool
    {
        return DB::transaction(function () use ($payment, $bankId, $expectedBankId) {
            $payment = PembayaranPranotaBpjs::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $oldBankId = (int) $payment->akun_coa_id;
            if ($oldBankId !== $expectedBankId) {
                $this->invalid('Bank pembayaran sudah diubah oleh pengguna lain. Muat ulang halaman sebelum mengedit kembali.');
            }
            if ($oldBankId === $bankId) {
                return false;
            }

            $journals = CoaTransaction::where('nomor_referensi', $payment->nomor_pembayaran)
                ->where('jenis_transaksi', 'Pembayaran Pranota BPJS')->orderBy('id')->lockForUpdate()->get();
            $cents = fn ($amount) => (int) round((float) $amount * 100);
            $total = $cents($payment->total_pembayaran);
            $credits = $journals->filter(fn ($entry) => $cents($entry->kredit) > 0);
            if ($total <= 0 || $cents($journals->sum('debit')) !== $total
                || $cents($credits->sum('kredit')) !== $total
                || $credits->contains(fn ($entry) => (int) $entry->coa_id !== $oldBankId || $cents($entry->debit) !== 0)
                || $journals->contains(fn ($entry) => $cents($entry->debit) < 0 || $cents($entry->kredit) < 0)
                || $cents($journals->where('coa_id', $oldBankId)->sum('debit')) !== 0) {
                $this->invalid('Jurnal pembayaran belum lengkap atau tidak sesuai. Periksa atau sinkronkan COA sebelum mengubah bank.');
            }

            $debits = $journals->filter(fn ($entry) => $cents($entry->debit) > 0)->groupBy('coa_id');
            $sourceIds = collect($payment->bank_allocations ?? [])->pluck('coa_id')->push($payment->bank_coa_id);
            if ($debits->has($bankId) || $sourceIds->contains(fn ($id) => (int) $id === $bankId)) {
                $this->invalid('Bank yang digunakan harus berbeda dengan Akun Bank dari jurnal.');
            }
            if ($payment->journal_allocations) {
                $expected = collect($payment->journal_allocations)->groupBy('coa_id')->map(fn ($rows) => $cents($rows->sum('subtotal')))->sortKeys()->all();
                $actual = $debits->map(fn ($rows) => $cents($rows->sum('debit')))->sortKeys()->all();
                if ($expected !== $actual) {
                    $this->invalid('Rincian jurnal pembayaran tidak sesuai. Sinkronkan COA sebelum mengubah bank.');
                }
            }

            $accounts = Coa::whereIn('id', [$oldBankId, $bankId])->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            if ($accounts->count() !== 2) {
                $this->invalid('Rekening bank lama atau baru tidak ditemukan.');
            }
            $oldBank = $accounts[$oldBankId];
            $newBank = $accounts[$bankId];
            $oldBank->saldo = round((float) $oldBank->saldo + $total / 100, 2);
            $oldBank->save();
            foreach ($credits as $entry) {
                $newBank->saldo = round((float) $newBank->saldo - (float) $entry->kredit, 2);
                $entry->coa_id = $bankId;
                $entry->saldo = $newBank->saldo;
                $entry->save();
            }
            $newBank->save();
            $payment->update(['akun_coa_id' => $bankId]);

            return true;
        });
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['akun_coa_id' => $message]);
    }
}
