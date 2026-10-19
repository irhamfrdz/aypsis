<?php

namespace App\Console\Commands;

use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\PembatalanSuratJalan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class MovePembatalanSuratJalanToBiayaTrucking extends Command
{
    protected $signature = 'pembatalan-surat-jalan:akun-biaya-trucking {--execute : Simpan perubahan; tanpa opsi ini hanya pratinjau}';

    protected $description = 'Pindahkan akun biaya semua pembatalan surat jalan dan jurnalnya ke 5101.011 Biaya Trucking';

    public function handle(): int
    {
        if (! Schema::hasColumn('pembatalan_surat_jalans', 'akun_coa_id')) {
            $this->error('Kolom akun_coa_id belum ada. Jalankan php artisan migrate terlebih dahulu.');

            return self::FAILURE;
        }

        $target = Coa::where('nomor_akun', '5101.011')->first();
        if (! $target || $target->nama_akun !== 'Biaya Trucking') {
            $this->error('Akun tujuan 5101.011 Biaya Trucking tidak ditemukan dengan nomor dan nama yang tepat.');

            return self::FAILURE;
        }

        $execute = (bool) $this->option('execute');
        $summary = ['pembatalan' => 0, 'jurnal_dipindah' => 0, 'tanpa_jurnal' => 0, 'sudah_sesuai' => 0];
        $openingBalances = [];
        $balanceChanges = [];
        $seenReferences = [];

        try {
            DB::transaction(function () use ($target, $execute, &$summary, &$openingBalances, &$balanceChanges, &$seenReferences) {
                PembatalanSuratJalan::query()
                    ->orderBy('id')
                    ->chunkById(200, function ($cancellations) use ($target, $execute, &$summary, &$openingBalances, &$balanceChanges, &$seenReferences) {
                        $entries = CoaTransaction::whereIn('nomor_referensi', $cancellations->pluck('nomor_pembayaran')->filter())
                            ->get(['id', 'nomor_referensi', 'jenis_transaksi', 'coa_id', 'debit', 'kredit'])
                            ->groupBy('nomor_referensi');

                        foreach ($cancellations as $cancellation) {
                            $reference = $cancellation->nomor_pembayaran;
                            if ($reference && isset($seenReferences[$reference])) {
                                throw new RuntimeException("Nomor pembayaran {$reference} dipakai oleh lebih dari satu pembatalan.");
                            }
                            if ($reference) {
                                $seenReferences[$reference] = true;
                            }

                            $journal = $entries->get($reference, collect());
                            $summary['pembatalan']++;

                            if ($journal->isEmpty()) {
                                $summary['tanpa_jurnal']++;
                            } else {
                                if ($journal->count() !== 2
                                    || $journal->contains(fn ($entry) => $entry->jenis_transaksi !== 'Pembatalan Surat Jalan')
                                    || ! in_array($cancellation->jenis_transaksi, ['Debit', 'Kredit'], true)) {
                                    throw new RuntimeException("Jurnal {$reference} tidak memiliki pasangan pembatalan debit/kredit yang valid.");
                                }

                                $bankId = Coa::where('nama_akun', $cancellation->bank)->value('id');
                                if (! $bankId || $journal->where('coa_id', $bankId)->count() !== 1) {
                                    throw new RuntimeException("Akun bank pada jurnal {$reference} tidak dapat dikenali dengan pasti.");
                                }

                                $expenseCandidates = $journal->filter(function ($entry) use ($cancellation, $bankId) {
                                    return (int) $entry->coa_id !== (int) $bankId
                                        && ($cancellation->jenis_transaksi === 'Debit'
                                            ? self::toCents($entry->kredit) > 0 && self::toCents($entry->debit) === 0
                                            : self::toCents($entry->debit) > 0 && self::toCents($entry->kredit) === 0);
                                });

                                if ($expenseCandidates->count() !== 1) {
                                    throw new RuntimeException("Akun biaya pada jurnal {$reference} tidak dapat dikenali dengan pasti.");
                                }

                                $expense = $expenseCandidates->first();
                                $bankEntry = $journal->firstWhere('coa_id', $bankId);
                                $bankValid = $cancellation->jenis_transaksi === 'Debit'
                                    ? self::toCents($bankEntry->debit) === self::toCents($expense->kredit)
                                        && self::toCents($bankEntry->kredit) === 0
                                    : self::toCents($bankEntry->kredit) === self::toCents($expense->debit)
                                        && self::toCents($bankEntry->debit) === 0;
                                if (! $bankValid) {
                                    throw new RuntimeException("Jumlah debit dan kredit pada jurnal {$reference} tidak seimbang.");
                                }

                                if ((int) $expense->coa_id !== (int) $target->id) {
                                    $oldId = (int) $expense->coa_id;
                                    $this->rememberOpeningBalance($openingBalances, $oldId, $execute);
                                    $this->rememberOpeningBalance($openingBalances, (int) $target->id, $execute);

                                    $net = self::toCents($expense->debit) - self::toCents($expense->kredit);
                                    $balanceChanges[$oldId] = ($balanceChanges[$oldId] ?? 0) - $net;
                                    $balanceChanges[$target->id] = ($balanceChanges[$target->id] ?? 0) + $net;
                                    $summary['jurnal_dipindah']++;

                                    if ($execute) {
                                        CoaTransaction::whereKey($expense->id)->update(['coa_id' => $target->id]);
                                    }
                                }
                            }

                            if ((int) $cancellation->akun_coa_id === (int) $target->id) {
                                $summary['sudah_sesuai']++;
                            } elseif ($execute) {
                                $cancellation->update(['akun_coa_id' => $target->id]);
                            }
                        }
                    });

                if ($execute) {
                    foreach ($balanceChanges as $accountId => $change) {
                        DB::update('UPDATE akun_coa SET saldo = saldo + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?', [self::fromCents($change), $accountId]);
                    }

                    foreach ($openingBalances as $accountId => $opening) {
                        $running = $opening;
                        CoaTransaction::where('coa_id', $accountId)
                            ->orderBy('id')
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

        $this->table(['Pembatalan', 'Jurnal dipindah', 'Tanpa jurnal', 'Akun pembatalan sudah sesuai'], [[
            $summary['pembatalan'], $summary['jurnal_dipindah'], $summary['tanpa_jurnal'], $summary['sudah_sesuai'],
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
