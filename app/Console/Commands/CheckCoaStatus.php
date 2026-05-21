<?php

namespace App\Console\Commands;

use App\Models\Coa;
use App\Models\CoaTransaction;
use Illuminate\Console\Command;

class CheckCoaStatus extends Command
{
    protected $signature = 'coa:check {nomor_akun=COA007}';

    protected $description = 'Check COA account status and transactions';

    public function handle()
    {
        $nomorAkun = $this->argument('nomor_akun');

        $coa = Coa::where('nomor_akun', $nomorAkun)->first();

        if (! $coa) {
            $this->error("COA {$nomorAkun} not found!");

            return Command::FAILURE;
        }

        $this->info("🏦 COA Account: {$coa->nomor_akun} - {$coa->nama_akun}");

        $transactions = CoaTransaction::where('coa_id', $coa->id)->get();
        $totalTransactions = $transactions->count();
        $totalDebit = $transactions->sum(function ($t) {
            return (float) $t->debit;
        });
        $totalKredit = $transactions->sum(function ($t) {
            return (float) $t->kredit;
        });
        $latestSaldo = $transactions->last() ? (float) $transactions->last()->saldo : 0;

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Transactions', number_format($totalTransactions)],
                ['Total Debit', 'Rp '.number_format($totalDebit, 2, ',', '.')],
                ['Total Kredit', 'Rp '.number_format($totalKredit, 2, ',', '.')],
                ['Current Saldo', 'Rp '.number_format($latestSaldo, 2, ',', '.')],
            ]
        );

        if ($totalTransactions > 0) {
            $this->info('📋 Recent transactions:');
            $recentTransactions = $transactions->sortByDesc('id')->take(5);

            $this->table(
                ['Date', 'Reference', 'Debit', 'Kredit', 'Saldo'],
                $recentTransactions->map(function ($trans) {
                    return [
                        $trans->tanggal_transaksi,
                        $trans->nomor_referensi,
                        'Rp '.number_format((float) $trans->debit, 0, ',', '.'),
                        'Rp '.number_format((float) $trans->kredit, 0, ',', '.'),
                        'Rp '.number_format((float) $trans->saldo, 0, ',', '.'),
                    ];
                })->toArray()
            );
        }

        return Command::SUCCESS;
    }
}
