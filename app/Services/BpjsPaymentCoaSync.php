<?php

namespace App\Services;

use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\PembayaranPranotaBpjs;
use App\Models\PranotaBpjsDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BpjsPaymentCoaSync
{
    public function sync(PembayaranPranotaBpjs $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            $payment = PembayaranPranotaBpjs::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $allocations = $payment->journal_allocations;
            if (! $allocations) {
                $parts = DB::table('pembayaran_pranota_bpjs_parts')->where('pembayaran_pranota_bpjs_id', $payment->id)->get();
                if ($parts->isEmpty()) {
                    $this->invalid('Rincian pembayaran lama tidak tersedia. Lengkapi rincian pembayaran sebelum sinkronisasi COA.');
                }
                $details = PranotaBpjsDetail::whereIn('id', $parts->pluck('pranota_bpjs_detail_id'))->get()->keyBy('id');
                $sources = collect($payment->bank_allocations ?: [['coa_id' => $payment->bank_coa_id]]);
                $groups = [];
                foreach ($parts->groupBy('tipe') as $type => $rows) {
                    $mapped = (new BpjsJournalAccount)->resolve((string) $type);
                    $source = $sources->firstWhere('coa_id', $mapped?->id);
                    if (! $source && $sources->count() === 1) $source = $sources->first();
                    if (! $source) $this->invalid('Akun asal untuk tipe '.$type.' tidak dapat ditentukan dari pembayaran tersimpan.');
                    foreach ($rows as $part) {
                        if (! $details->has($part->pranota_bpjs_detail_id)) $this->invalid('Rincian karyawan pembayaran tidak ditemukan.');
                    }
                    $groups[] = ['credit_id' => $source['coa_id'], 'parts' => $rows->map(fn ($row) => (array) $row)->all()];
                }
                $debits = (new BpjsPaymentJournal)->allocations($groups, $details);
            } else {
                $debits = collect($allocations)->groupBy('coa_id')->map(fn ($rows) => round($rows->sum('subtotal'), 2))->all();
            }
            $total = (int) round((float) $payment->total_pembayaran * 100);
            if ($total <= 0 || array_sum(array_map(fn ($amount) => (int) round($amount * 100), $debits)) !== $total
                || collect($debits)->contains(fn ($amount) => $amount <= 0) || isset($debits[$payment->akun_coa_id])) {
                $this->invalid('Alokasi jurnal tidak sesuai total pembayaran atau akun bank.');
            }
            $entries = CoaTransaction::where('nomor_referensi', $payment->nomor_pembayaran)
                ->where('jenis_transaksi', 'Pembayaran Pranota BPJS')->lockForUpdate()->get();
            $ids = array_unique([...array_keys($debits), $payment->akun_coa_id, ...$entries->pluck('coa_id')->all()]);
            $accounts = Coa::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            if ($accounts->count() !== count($ids)) $this->invalid('Salah satu akun COA pembayaran tidak ditemukan.');
            $expected = [];
            foreach ($debits as $id => $amount) $expected[$id] = [(int) round($amount * 100), 0];
            $expected[$payment->akun_coa_id] = [0, $total];
            $actual = $entries->groupBy('coa_id')->map(fn ($rows) => [(int) round($rows->sum('debit') * 100), (int) round($rows->sum('kredit') * 100)])->all();
            ksort($expected);
            ksort($actual);
            $changed = $expected !== $actual;
            if ($changed) {
                // Reverse only this payment before replacing its journal; other references are untouched.
                foreach ($entries->groupBy('coa_id') as $id => $rows) {
                    $account = $accounts[$id];
                    $account->saldo = round((float) $account->saldo - $rows->sum('debit') + $rows->sum('kredit'), 2);
                    $account->save();
                }
                foreach ($entries as $entry) $entry->delete();
                $journal = new CoaTransactionService;
                foreach ($debits as $id => $amount) {
                    if (! $journal->recordDoubleEntry(
                        ['coa_id' => $id, 'nama_akun' => $accounts[$id]->nama_akun, 'jumlah' => $amount],
                        ['coa_id' => $payment->akun_coa_id, 'nama_akun' => $accounts[$payment->akun_coa_id]->nama_akun, 'jumlah' => $amount],
                        $payment->tanggal_pembayaran->format('Y-m-d'), $payment->nomor_pembayaran,
                        'Pembayaran Pranota BPJS', 'Pembayaran Pranota BPJS - '.$payment->nomor_pembayaran
                    )) throw new \RuntimeException('Jurnal sinkronisasi gagal dicatat.');
                }
            }
            $payment->update(['journal_allocations' => collect($debits)->map(fn ($amount, $id) => [
                'coa_id' => $id, 'nomor_akun' => $accounts[$id]->nomor_akun, 'nama_akun' => $accounts[$id]->nama_akun, 'subtotal' => $amount,
            ])->values()->all()]);
            return $changed;
        });
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['sync_coa' => $message]);
    }
}
