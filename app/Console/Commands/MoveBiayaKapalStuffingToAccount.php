<?php

namespace App\Console\Commands;

use App\Models\BiayaKapal;
use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\KlasifikasiBiaya;
use App\Models\PembayaranBiayaKapal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class MoveBiayaKapalStuffingToAccount extends Command
{
    protected $signature = 'pembayaran-biaya-kapal:akun-stuffing {--execute : Simpan perubahan; tanpa opsi ini hanya pratinjau}';

    protected $description = 'Pindahkan akun pembayaran invoice BIAYA STUFFING dan jurnalnya ke 5101.025';

    public function handle(): int
    {
        if (! Schema::hasColumn('pembayaran_biaya_kapals', 'akun_coa_id')) {
            $this->error('Kolom akun_coa_id belum ada. Jalankan php artisan migrate --force terlebih dahulu.');

            return self::FAILURE;
        }

        $target = Coa::where('nomor_akun', '5101.025')->first();
        if (! $target || $target->nama_akun !== 'Biaya Stripping/Stuffing Jakarta') {
            $this->error('Akun 5101.025 Biaya Stripping/Stuffing Jakarta tidak ditemukan dengan nomor dan nama yang tepat.');

            return self::FAILURE;
        }

        $execute = (bool) $this->option('execute');
        $summary = ['invoice' => 0, 'pembayaran' => 0, 'akun_diubah' => 0, 'jurnal_dipindah' => 0, 'tanpa_jurnal' => 0];
        $openingBalances = [];
        $balanceChanges = [];

        try {
            DB::transaction(function () use ($target, $execute, &$summary, &$openingBalances, &$balanceChanges) {
                $codes = KlasifikasiBiaya::whereRaw('UPPER(TRIM(nama)) = ?', ['BIAYA STUFFING'])->pluck('kode');
                $invoiceIds = BiayaKapal::where(function ($query) use ($codes) {
                    $query->whereIn('jenis_biaya', $codes)
                        ->orWhereRaw('UPPER(TRIM(jenis_biaya)) = ?', ['BIAYA STUFFING']);
                })->pluck('id');
                $summary['invoice'] = $invoiceIds->count();

                if ($invoiceIds->isEmpty()) {
                    return;
                }

                $query = PembayaranBiayaKapal::whereHas('items', fn ($q) => $q->whereIn('biaya_kapal_id', $invoiceIds))
                    ->with('items')->orderBy('id');
                if ($execute) {
                    $query->lockForUpdate();
                }

                foreach ($query->get() as $payment) {
                    $items = $payment->items;
                    if ($items->isEmpty() || $items->contains(fn ($item) => ! $invoiceIds->contains($item->biaya_kapal_id))) {
                        throw new RuntimeException("Pembayaran {$payment->nomor_pembayaran} berisi invoice campuran. Pisahkan atau periksa secara manual.");
                    }

                    $summary['pembayaran']++;
                    $bankIds = Coa::where('nama_akun', $payment->bank)->pluck('id');
                    if ($bankIds->count() !== 1 || (int) $bankIds->first() === (int) $target->id) {
                        throw new RuntimeException("Akun Kas/Bank pembayaran {$payment->nomor_pembayaran} tidak dapat dikenali secara unik.");
                    }
                    $bankId = (int) $bankIds->first();

                    $journalQuery = CoaTransaction::where('nomor_referensi', $payment->nomor_pembayaran)->orderBy('id');
                    if ($execute) {
                        $journalQuery->lockForUpdate();
                    }
                    $journal = $journalQuery->get();
                    if ($journal->isEmpty()) {
                        $summary['tanpa_jurnal']++;
                    } else {
                        $adjustment = self::toCents($payment->total_tagihan_penyesuaian ?? 0);
                        $expectedPairs = $items->count() + ($adjustment !== 0 ? 1 : 0);
                        $bank = $journal->where('coa_id', $bankId);
                        $expense = $journal->reject(fn ($entry) => (int) $entry->coa_id === $bankId);
                        if ($journal->count() !== $expectedPairs * 2
                            || $bank->count() !== $expectedPairs
                            || $expense->count() !== $expectedPairs
                            || $journal->contains(fn ($entry) => $entry->jenis_transaksi !== 'pembayaran_biaya_kapal')) {
                            throw new RuntimeException("Jurnal pembayaran {$payment->nomor_pembayaran} tidak sesuai jumlah invoice dan penyesuaian.");
                        }

                        $expected = $items->map(fn ($item) => self::toCents($item->nominal))->all();
                        if ($adjustment !== 0) {
                            $expected[] = $adjustment;
                        }
                        sort($expected);
                        $direction = $payment->jenis_transaksi === 'debit' ? -1 : 1;
                        if (! in_array($payment->jenis_transaksi, ['debit', 'kredit'], true)
                            || self::sortedNets($expense, $direction) !== $expected
                            || self::sortedNets($bank, -$direction) !== $expected) {
                            throw new RuntimeException("Nominal atau arah jurnal pembayaran {$payment->nomor_pembayaran} tidak cocok.");
                        }

                        foreach ($expense as $entry) {
                            if ((int) $entry->coa_id === (int) $target->id) {
                                continue;
                            }
                            $oldId = (int) $entry->coa_id;
                            $this->rememberOpeningBalance($openingBalances, $oldId, $execute);
                            $this->rememberOpeningBalance($openingBalances, (int) $target->id, $execute);
                            $net = self::toCents($entry->debit) - self::toCents($entry->kredit);
                            $balanceChanges[$oldId] = ($balanceChanges[$oldId] ?? 0) - $net;
                            $balanceChanges[$target->id] = ($balanceChanges[$target->id] ?? 0) + $net;
                            $summary['jurnal_dipindah']++;
                            if ($execute) {
                                DB::table('coa_transactions')->where('id', $entry->id)->update(['coa_id' => $target->id]);
                            }
                        }
                    }

                    if ((int) $payment->akun_coa_id !== (int) $target->id) {
                        $summary['akun_diubah']++;
                        if ($execute) {
                            DB::table('pembayaran_biaya_kapals')->where('id', $payment->id)
                                ->update(['akun_coa_id' => $target->id, 'updated_at' => now()]);
                        }
                    }
                }

                if ($execute) {
                    foreach ($balanceChanges as $accountId => $change) {
                        DB::table('akun_coa')->where('id', $accountId)
                            ->update(['saldo' => DB::raw('saldo + '.self::fromCents($change)), 'updated_at' => now()]);
                    }
                    foreach ($openingBalances as $accountId => $opening) {
                        $running = $opening;
                        CoaTransaction::where('coa_id', $accountId)->orderBy('id')
                            ->chunkById(500, function ($transactions) use (&$running) {
                                foreach ($transactions as $transaction) {
                                    $running += self::toCents($transaction->debit) - self::toCents($transaction->kredit);
                                    if (self::toCents($transaction->saldo) !== $running) {
                                        DB::table('coa_transactions')->where('id', $transaction->id)
                                            ->update(['saldo' => self::fromCents($running)]);
                                    }
                                }
                            });
                    }
                }
            });
        } catch (Throwable $e) {
            $this->error('Tidak ada perubahan yang disimpan: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->table(['Invoice', 'Pembayaran', 'Akun diubah', 'Jurnal dipindah', 'Tanpa jurnal'], [[
            $summary['invoice'], $summary['pembayaran'], $summary['akun_diubah'], $summary['jurnal_dipindah'], $summary['tanpa_jurnal'],
        ]]);
        $this->info($execute ? 'Selesai. Perubahan tersimpan.' : 'Pratinjau selesai. Jalankan lagi dengan --execute untuk menyimpan.');

        return self::SUCCESS;
    }

    private function rememberOpeningBalance(array &$openings, int $accountId, bool $execute): void
    {
        if (isset($openings[$accountId])) {
            return;
        }

        $query = Coa::whereKey($accountId);
        $account = ($execute ? $query->lockForUpdate() : $query)->firstOrFail();
        $net = DB::table('coa_transactions')->where('coa_id', $accountId)
            ->selectRaw('COALESCE(SUM(debit - kredit), 0) AS net')->value('net');
        $openings[$accountId] = self::toCents($account->saldo) - self::toCents($net);
    }

    private static function sortedNets($entries, int $direction): array
    {
        $amounts = $entries->map(fn ($entry) => $direction * (self::toCents($entry->debit) - self::toCents($entry->kredit)))->all();
        sort($amounts);

        return $amounts;
    }

    private static function toCents(string|int|float $amount): int
    {
        $value = (string) $amount;
        $negative = str_starts_with($value, '-');
        [$whole, $fraction] = array_pad(explode('.', ltrim($value, '-'), 2), 2, '');
        $cents = ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');

        return $negative ? -$cents : $cents;
    }

    private static function fromCents(int $cents): string
    {
        return ($cents < 0 ? '-' : '').intdiv(abs($cents), 100).'.'.str_pad((string) (abs($cents) % 100), 2, '0', STR_PAD_LEFT);
    }
}
