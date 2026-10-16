<?php

namespace App\Console\Commands;

use App\Models\Coa;
use App\Models\CoaTransaction;
use App\Models\PembayaranPranotaRit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

class MovePranotaRitToDriverIncentiveAccount extends Command
{
    protected $signature = 'pembayaran-pranota-rit:akun-insentif-supir-truck-jakarta {--execute : Simpan perubahan; tanpa opsi ini hanya pratinjau}';

    protected $description = 'Pindahkan akun seluruh pembayaran pranota rit dan jurnalnya ke 5101.001 Biaya Insentif Supir Truck Jakarta';

    public function handle(): int
    {
        if (! Schema::hasColumn('pembayaran_pranota_rits', 'akun_coa_id')) {
            $this->error('Kolom akun_coa_id belum ada. Jalankan php artisan migrate --force terlebih dahulu.');

            return self::FAILURE;
        }

        $target = Coa::where('nomor_akun', '5101.001')->first();
        if (! $target || $target->nama_akun !== 'Biaya Insentif Supir Truck Jakarta') {
            $this->error('Akun 5101.001 Biaya Insentif Supir Truck Jakarta tidak ditemukan dengan nomor dan nama yang tepat.');

            return self::FAILURE;
        }

        $execute = (bool) $this->option('execute');
        $summary = ['pembayaran' => 0, 'akun_diubah' => 0, 'jurnal_dipindah' => 0, 'jurnal_dibuat' => 0, 'tanpa_jurnal' => 0];
        $openingBalances = [];
        $balanceChanges = [];

        try {
            DB::transaction(function () use ($target, $execute, &$summary, &$openingBalances, &$balanceChanges) {
                $paymentsQuery = PembayaranPranotaRit::query()->orderBy('id');
                if ($execute) {
                    $paymentsQuery->lockForUpdate();
                }

                $paymentsQuery->chunkById(200, function ($payments) use ($target, $execute, &$summary, &$openingBalances, &$balanceChanges) {
                    $journalQuery = CoaTransaction::whereIn('nomor_referensi', $payments->pluck('nomor_pembayaran'));
                    if ($execute) {
                        $journalQuery->lockForUpdate();
                    }
                    $journals = $journalQuery->get(['id', 'nomor_referensi', 'jenis_transaksi', 'coa_id', 'tanggal_transaksi', 'keterangan', 'created_by', 'debit', 'kredit'])
                        ->groupBy('nomor_referensi');

                    foreach ($payments as $payment) {
                        $summary['pembayaran']++;
                        $reference = $payment->nomor_pembayaran;
                        $journal = $journals->get($reference, collect());

                        if ($journal->isEmpty()) {
                            $summary['tanpa_jurnal']++;
                        } else {
                            if (! in_array($journal->count(), [1, 2], true)
                                || ! in_array($payment->jenis_transaksi, ['Debit', 'Kredit'], true)
                                || $journal->contains(fn ($entry) => $entry->jenis_transaksi !== 'Pembayaran Pranota Rit')) {
                                throw new RuntimeException("Jurnal {$reference} tidak memiliki pasangan pembayaran pranota rit yang valid.");
                            }

                            $bankIds = Coa::where('nama_akun', $payment->bank)->pluck('id');
                            if ($bankIds->count() !== 1 || (int) $bankIds->first() === (int) $target->id) {
                                throw new RuntimeException("Akun bank untuk pembayaran {$reference} tidak dapat dikenali dengan pasti.");
                            }

                            $bankId = (int) $bankIds->first();
                            $bankEntries = $journal->where('coa_id', $bankId);
                            $expenseEntries = $journal->reject(fn ($entry) => (int) $entry->coa_id === $bankId);
                            if ($bankEntries->count() !== 1 || $expenseEntries->count() !== $journal->count() - 1) {
                                throw new RuntimeException("Akun biaya pada jurnal {$reference} tidak dapat dikenali dengan pasti.");
                            }

                            $amount = self::toCents($payment->total_tagihan_setelah_penyesuaian);
                            $bankEntry = $bankEntries->first();
                            $bankNet = self::toCents($bankEntry->debit) - self::toCents($bankEntry->kredit);
                            $expectedBankNet = $payment->jenis_transaksi === 'Debit' ? $amount : -$amount;
                            if ($bankNet !== $expectedBankNet) {
                                throw new RuntimeException("Nominal atau arah jurnal {$reference} tidak sesuai pembayaran.");
                            }

                            if ($expenseEntries->isEmpty()) {
                                // Data lama dapat berisi hanya jurnal bank. Lengkapi sisi biaya
                                // dengan tanggal dan metadata yang sama setelah jurnal bank tervalidasi.
                                $this->rememberOpeningBalance($openingBalances, (int) $target->id, $execute);
                                $expenseNet = -$bankNet;
                                $balanceChanges[$target->id] = ($balanceChanges[$target->id] ?? 0) + $expenseNet;
                                $summary['jurnal_dibuat']++;

                                if ($execute) {
                                    DB::table('coa_transactions')->insert([
                                        'coa_id' => $target->id,
                                        'tanggal_transaksi' => $bankEntry->tanggal_transaksi,
                                        'nomor_referensi' => $reference,
                                        'jenis_transaksi' => 'Pembayaran Pranota Rit',
                                        'keterangan' => $bankEntry->keterangan,
                                        'debit' => self::fromCents(max(0, $expenseNet)),
                                        'kredit' => self::fromCents(max(0, -$expenseNet)),
                                        'saldo' => '0.00',
                                        'created_by' => $bankEntry->created_by,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                }
                            } else {
                                $expense = $expenseEntries->first();
                                $expenseNet = self::toCents($expense->debit) - self::toCents($expense->kredit);
                                if ($expenseNet !== -$expectedBankNet) {
                                    throw new RuntimeException("Nominal atau arah jurnal {$reference} tidak sesuai pembayaran.");
                                }

                                if ((int) $expense->coa_id !== (int) $target->id) {
                                    $oldId = (int) $expense->coa_id;
                                    $this->rememberOpeningBalance($openingBalances, $oldId, $execute);
                                    $this->rememberOpeningBalance($openingBalances, (int) $target->id, $execute);

                                    $balanceChanges[$oldId] = ($balanceChanges[$oldId] ?? 0) - $expenseNet;
                                    $balanceChanges[$target->id] = ($balanceChanges[$target->id] ?? 0) + $expenseNet;
                                    $summary['jurnal_dipindah']++;

                                    if ($execute) {
                                        DB::table('coa_transactions')->where('id', $expense->id)->update(['coa_id' => $target->id]);
                                    }
                                }
                            }
                        }

                        if ((int) $payment->akun_coa_id !== (int) $target->id) {
                            $summary['akun_diubah']++;
                            if ($execute) {
                                DB::table('pembayaran_pranota_rits')->where('id', $payment->id)
                                    ->update(['akun_coa_id' => $target->id, 'updated_at' => now()]);
                            }
                        }
                    }
                });

                if ($execute) {
                    foreach ($balanceChanges as $accountId => $change) {
                        DB::update('UPDATE akun_coa SET saldo = saldo + ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?', [self::fromCents($change), $accountId]);
                    }

                    // Saldo transaksi mengikuti urutan pencatatan setiap akun.
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

        $this->table(['Pembayaran', 'Akun diubah', 'Jurnal dipindah', 'Jurnal biaya dibuat', 'Tanpa jurnal'], [[
            $summary['pembayaran'], $summary['akun_diubah'], $summary['jurnal_dipindah'], $summary['jurnal_dibuat'], $summary['tanpa_jurnal'],
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
