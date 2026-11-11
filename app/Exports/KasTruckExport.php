<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KasTruckExport implements FromArray, ShouldAutoSize
{
    public function __construct(private array $data) {}

    public function array(): array
    {
        $rows = [
            ['Laporan Kas Truck'],
            ['Rekening', $this->data['accountName']],
            ['Saldo Awal', (float) $this->data['saldoAwal']],
            ['Tanggal', 'No. Accurate', 'Keterangan', 'Akun', 'Pemasukan', 'Pengeluaran', 'Saldo'],
        ];

        foreach ($this->data['transactions'] as $trx) {
            $akun = collect($trx->rincian_akun_pembayaran ?? [])->map(function ($rincian) {
                return trim(($rincian['nomor_akun'] ?? '').' '.($rincian['nama_akun'] ?? ''));
            })->implode(', ');

            $rows[] = [
                Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y'),
                $trx->nomor_accurate ?: ($trx->nomor_referensi ?: '-'),
                $trx->keterangan ?: '-',
                $akun ?: ($trx->akun_pembayaran ?: '-'),
                (float) $trx->debit,
                (float) $trx->kredit,
                (float) $trx->running_balance,
            ];
        }

        $rows[] = ['Total', '', '', '', (float) $this->data['totalDebit'], (float) $this->data['totalKredit'], (float) $this->data['saldoAkhir']];

        return $rows;
    }
}
