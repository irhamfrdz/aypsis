@extends('layouts.print')

@section('content')
<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 0.5in;
        }
        body {
            font-size: 9px;
        }
        table {
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        thead {
            display: table-header-group;
        }
    }
    .compact-table {
        font-size: 9px;
        width: 100%;
        border-collapse: collapse;
    }
    .compact-table th,
    .compact-table td {
        border: 1px solid #ddd;
        padding: 3px;
        text-align: left;
        vertical-align: top;
    }
    .compact-table th {
        background: #f3f4f6;
        font-weight: bold;
        text-align: center;
    }
    .compact-table td.text-right {
        text-align: right;
    }
    .compact-table td.text-center {
        text-align: center;
    }
    .bg-yellow {
        background-color: #fef3c7 !important;
    }
    .bg-gray {
        background-color: #f3f4f6 !important;
    }
    .text-green {
        color: #059669;
    }
    .text-red {
        color: #dc2626;
    }
    .text-blue {
        color: #2563eb;
    }
</style>

<div style="padding:8px;">
    <!-- Header -->
    <div style="margin-bottom:12px;border-bottom:2px solid #000;padding-bottom:8px;">
        <h1 style="font-size:18px;margin:0;text-align:center;font-weight:bold;">BUKU BESAR (LEDGER)</h1>
        <div style="text-align:center;margin-top:4px;">
            <div style="font-size:11px;color:#333;">
                <strong>Nomor Akun:</strong> {{ $coa->nomor_akun }} |
                <strong>Nama Akun:</strong> {{ $coa->nama_akun }} |
                <strong>Tipe:</strong> {{ $coa->tipe_akun }}
            </div>
            @if(request('dari_tanggal') || request('sampai_tanggal'))
                <div style="font-size:10px;color:#666;margin-top:2px;">
                    Periode: {{ request('dari_tanggal') ? \Carbon\Carbon::parse(request('dari_tanggal'))->format('d/m/Y') : 'Awal' }}
                    s/d
                    {{ request('sampai_tanggal') ? \Carbon\Carbon::parse(request('sampai_tanggal'))->format('d/m/Y') : 'Sekarang' }}
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Cards -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:12px;">
        <div style="border:1px solid #ddd;padding:8px;background:#f9fafb;">
            <div style="font-size:9px;color:#666;text-transform:uppercase;font-weight:bold;">Saldo Awal</div>
            <div style="font-size:14px;font-weight:bold;color:#000;margin-top:4px;">
                Rp {{ number_format($saldoAwal, 2, ',', '.') }}
            </div>
        </div>
        <div style="border:1px solid #ddd;padding:8px;background:#f9fafb;">
            <div style="font-size:9px;color:#666;text-transform:uppercase;font-weight:bold;">Total Debit</div>
            <div style="font-size:14px;font-weight:bold;color:#059669;margin-top:4px;">
                Rp {{ number_format(abs($totalDebit), 2, ',', '.') }}
            </div>
        </div>
        <div style="border:1px solid #ddd;padding:8px;background:#f9fafb;">
            <div style="font-size:9px;color:#666;text-transform:uppercase;font-weight:bold;">Total Kredit</div>
            <div style="font-size:14px;font-weight:bold;color:#dc2626;margin-top:4px;">
                Rp {{ number_format(abs($totalKredit), 2, ',', '.') }}
            </div>
        </div>
        <div style="border:1px solid #2563eb;padding:8px;background:#dbeafe;">
            <div style="font-size:9px;color:#1e40af;text-transform:uppercase;font-weight:bold;">Saldo Akhir</div>
            <div style="font-size:14px;font-weight:bold;color:#1e3a8a;margin-top:4px;">
                Rp {{ number_format($coa->saldo, 2, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <table class="compact-table">
        <thead>
            <tr>
                <th style="width:8%;">Tanggal</th>
                <th style="width:15%;">No. Referensi</th>
                <th style="width:12%;">Jenis Transaksi</th>
                <th style="width:25%;">Keterangan</th>
                <th style="width:13%;">Debit</th>
                <th style="width:13%;">Kredit</th>
                <th style="width:14%;">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <!-- Saldo Awal Row -->
            <tr class="bg-yellow">
                <td colspan="4" style="font-weight:bold;">Saldo Awal</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right" style="font-weight:bold;">
                    Rp {{ number_format($saldoAwal, 2, ',', '.') }}
                </td>
            </tr>

            <!-- Transaction Rows -->
            @forelse($transactions as $transaction)
                <tr>
                    <td class="text-center">{{ $transaction->tanggal_transaksi->format('d/m/Y') }}</td>
                    <td>{{ $transaction->nomor_referensi }}</td>
                    <td class="text-center">{{ $transaction->jenis_transaksi }}</td>
                    <td>{{ $transaction->keterangan ?? '-' }}</td>
                    <td class="text-right text-green" style="font-weight:600;">
                        @if($transaction->debit != 0)
                            Rp {{ number_format(abs($transaction->debit), 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right text-red" style="font-weight:600;">
                        @if($transaction->kredit != 0)
                            Rp {{ number_format(abs($transaction->kredit), 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right" style="font-weight:bold;">
                        Rp {{ number_format($transaction->saldo, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:#666;padding:12px;">
                        Tidak ada transaksi untuk periode ini
                    </td>
                </tr>
            @endforelse

            <!-- Summary Row -->
            @if($transactions->count() > 0)
                <tr class="bg-gray" style="font-weight:bold;">
                    <td colspan="4" style="font-weight:bold;">TOTAL</td>
                    <td class="text-right text-green">
                        Rp {{ number_format(abs($totalDebit), 2, ',', '.') }}
                    </td>
                    <td class="text-right text-red">
                        Rp {{ number_format(abs($totalKredit), 2, ',', '.') }}
                    </td>
                    <td class="text-right text-blue">
                        Rp {{ number_format($coa->saldo, 2, ',', '.') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Footer -->
    <div style="margin-top:12px;font-size:8px;color:#666;">
        Generated by {{ config('app.name') }} |
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} |
        Total Transaksi: {{ $transactions->count() }}
    </div>
</div>
@endsection
