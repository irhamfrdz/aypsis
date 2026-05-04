<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valuasi Persediaan - {{ $masterItem->nama_barang }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
            margin: 20px;
            background: #fff;
        }
        .header-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #8B0000; /* Dark red/brown like the image */
            margin-bottom: 5px;
        }
        .report-period {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .filter-text {
            text-align: right;
            font-size: 10px;
            margin-bottom: 5px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            vertical-align: top;
            color: #000080; /* Dark blue for info labels like the image */
            font-weight: bold;
        }
        .info-table td.label-col {
            width: 120px;
        }
        .info-table td.value-col {
            width: auto;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            padding: 4px;
        }
        table.data-table th {
            color: #000080;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            font-weight: bold;
            text-align: left;
        }
        table.data-table th.right, table.data-table td.right {
            text-align: right;
        }
        table.data-table th.center, table.data-table td.center {
            text-align: center;
        }
        table.data-table .row-separator {
            border-bottom: 1px solid #eee;
        }
        table.data-table .totals-row td {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            color: #000080;
        }
        @media print {
            body {
                margin: 0;
            }
            @page {
                size: landscape;
                margin: 1cm;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header-container">
        <div class="company-name">AYPSIS</div>
        <div class="report-title">Rincian Valuasi Persediaan</div>
        <div class="report-period">Dari {{ $fromDate->format('d M Y') }} ke {{ $toDate->format('d M Y') }}</div>
    </div>

    <div class="filter-text">
        Filter berdasarkan : Tanggal, Barang
    </div>

    <table class="info-table">
        <tr>
            <td class="label-col">No. Barang</td>
            <td class="value-col">: {{ $masterItem->id }}</td>
            <td class="label-col" style="text-align: right; padding-right: 20px;">Kts. Saldo Awal</td>
            <td class="value-col" style="width: 100px;">: {{ number_format($saldoAwalQty, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label-col">Deskripsi Barang</td>
            <td class="value-col">: {{ $masterItem->nama_barang }}</td>
            <td class="label-col" style="text-align: right; padding-right: 20px;">Nilai Saldo Awal</td>
            <td class="value-col">: {{ number_format($saldoAwalNilai, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 80px;">Tanggal</th>
                <th>Tipe</th>
                <th>No. Faktur</th>
                <th class="right">Kts. Masuk</th>
                <th class="right">Nilai masuk</th>
                <th class="right">Kts. Keluar</th>
                <th class="right">Nilai keluar</th>
                <th class="right">Kuantitas</th>
                <th class="right">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalKtsMasuk = 0;
                $totalNilaiMasuk = 0;
                $totalKtsKeluar = 0;
                $totalNilaiKeluar = 0;
            @endphp
            @forelse($transaksi as $trx)
                @php
                    $totalKtsMasuk += $trx->kts_masuk;
                    $totalNilaiMasuk += $trx->nilai_masuk;
                    $totalKtsKeluar += $trx->kts_keluar;
                    $totalNilaiKeluar += $trx->nilai_keluar;
                @endphp
                <tr class="row-separator">
                    <td>{{ $trx->tanggal }}</td>
                    <td>{{ $trx->tipe }}</td>
                    <td>{{ $trx->no_faktur }}</td>
                    <td class="right">{{ $trx->kts_masuk > 0 ? number_format($trx->kts_masuk, 0, ',', '.') : '0' }}</td>
                    <td class="right">{{ $trx->nilai_masuk > 0 ? number_format($trx->nilai_masuk, 0, ',', '.') : '0' }}</td>
                    <td class="right">{{ $trx->kts_keluar > 0 ? number_format($trx->kts_keluar, 0, ',', '.') : '0' }}</td>
                    <td class="right">{{ $trx->nilai_keluar > 0 ? number_format($trx->nilai_keluar, 0, ',', '.') : '0' }}</td>
                    <td class="right">{{ number_format($trx->kuantitas, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($trx->nilai_akhir, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center" style="padding: 20px;">Tidak ada transaksi dalam periode ini.</td>
                </tr>
            @endforelse
            <tr class="totals-row">
                <td colspan="3"></td>
                <td class="right">{{ number_format($totalKtsMasuk, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($totalNilaiMasuk, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($totalKtsKeluar, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($totalNilaiKeluar, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
