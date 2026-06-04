<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valuasi Pemakaian - {{ $pemakaiName }}</title>
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
            color: #6b21a8; /* Purple to match the button */
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
            color: #5b21b6; /* Dark purple for info labels */
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
            padding: 6px 4px;
        }
        table.data-table th {
            color: #5b21b6;
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
            color: #5b21b6;
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
        <div class="report-title">Rincian Valuasi Pemakaian Stock Amprahan</div>
        <div class="report-period">Dari {{ $fromDate->format('d M Y') }} ke {{ $toDate->format('d M Y') }}</div>
    </div>

    <div class="filter-text">
        Filter berdasarkan : Tanggal, Kategori, Pemakai
    </div>

    <table class="info-table">
        <tr>
            <td class="label-col">Kategori Pemakai</td>
            <td class="value-col">: {{ $kategori }}</td>
        </tr>
        <tr>
            <td class="label-col">Nama Pemakai</td>
            <td class="value-col">: {{ $pemakaiName }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 80px;">Tanggal Pakai</th>
                <th style="width: 100px;">No. Bukti Stock</th>
                <th>Nama Barang</th>
                <th style="width: 90px;">Tipe Amprahan</th>
                <th class="right" style="width: 70px;">Kts. Keluar</th>
                <th style="width: 50px;">Satuan</th>
                <th class="right" style="width: 90px;">Harga Satuan</th>
                <th class="right" style="width: 100px;">Total Nilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalKtsKeluar = 0;
                $totalNilaiKeluar = 0;
            @endphp
            @forelse($usages as $usage)
                @php
                    $qty = $usage->jumlah;
                    $harga = $usage->stockAmprahan->harga_satuan ?? 0;
                    $subtotal = $qty * $harga;
                    
                    $totalKtsKeluar += $qty;
                    $totalNilaiKeluar += $subtotal;
                @endphp
                <tr class="row-separator">
                    <td>{{ $usage->tanggal_pengambilan ? \Carbon\Carbon::parse($usage->tanggal_pengambilan)->format('d M Y') : '-' }}</td>
                    <td>{{ $usage->stockAmprahan->nomor_bukti ?? '-' }}</td>
                    <td>{{ $usage->stockAmprahan->nama_barang ?? ($usage->stockAmprahan->masterNamaBarangAmprahan->nama_barang ?? '-') }}</td>
                    <td>{{ $usage->stockAmprahan->type_amprahan ?? '-' }}</td>
                    <td class="right">{{ number_format($qty, 0, ',', '.') }}</td>
                    <td>{{ $usage->stockAmprahan->satuan ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    <td>{{ $usage->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center" style="padding: 20px;">Tidak ada transaksi pemakaian dalam periode ini.</td>
                </tr>
            @endforelse
            <tr class="totals-row">
                <td colspan="4">TOTAL</td>
                <td class="right">{{ number_format($totalKtsKeluar, 0, ',', '.') }}</td>
                <td></td>
                <td></td>
                <td class="right">Rp {{ number_format($totalNilaiKeluar, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
