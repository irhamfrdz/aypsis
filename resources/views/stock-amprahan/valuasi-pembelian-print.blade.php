<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valuasi Pembelian - {{ $lokasiName }}</title>
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
            color: #0f766e; /* Teal color to match the button */
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
            color: #0f766e; /* Teal for info labels */
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
            color: #0f766e;
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
            color: #0f766e;
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
        <div class="report-title">Rincian Valuasi Pembelian Stock Amprahan</div>
        <div class="report-period">Dari {{ $fromDate->format('d M Y') }} ke {{ $toDate->format('d M Y') }}</div>
    </div>

    <div class="filter-text">
        Filter berdasarkan : Tanggal, Lokasi
    </div>

    <table class="info-table">
        <tr>
            <td class="label-col">Lokasi</td>
            <td class="value-col">: {{ $lokasiName }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 80px;">Tanggal Beli</th>
                <th style="width: 100px;">No. Bukti</th>
                <th>Nama Barang</th>
                <th>Vendor / Toko</th>
                <th style="width: 90px;">Tipe Amprahan</th>
                <th class="right" style="width: 70px;">Qty Beli</th>
                <th style="width: 50px;">Satuan</th>
                <th class="right" style="width: 90px;">Harga Satuan</th>
                <th class="right" style="width: 80px;">Adjustment</th>
                <th class="right" style="width: 100px;">Total Nilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQtyBeli = 0;
                $totalNilaiBeli = 0;
            @endphp
            @forelse($purchases as $purchase)
                @php
                    // Purchase Qty = current stock (jumlah) + usages sum
                    $qty = $purchase->jumlah + $purchase->usages->sum('jumlah');
                    $harga = $purchase->harga_satuan ?? 0;
                    $adj = $purchase->adjustment ?? 0;
                    $subtotal = ($qty * $harga) + $adj;
                    
                    $totalQtyBeli += $qty;
                    $totalNilaiBeli += $subtotal;
                @endphp
                <tr class="row-separator">
                    <td>{{ $purchase->tanggal_beli ? $purchase->tanggal_beli->format('d M Y') : ($purchase->created_at ? $purchase->created_at->format('d M Y') : '-') }}</td>
                    <td>{{ $purchase->nomor_bukti ?? '-' }}</td>
                    <td>{{ $purchase->nama_barang ?? ($purchase->masterNamaBarangAmprahan->nama_barang ?? '-') }}</td>
                    <td>{{ $purchase->vendorAmprahan->nama_toko ?? '-' }}</td>
                    <td>{{ $purchase->type_amprahan ?? '-' }}</td>
                    <td class="right">{{ number_format($qty, 0, ',', '.') }}</td>
                    <td>{{ $purchase->satuan ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($adj, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    <td>{{ $purchase->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center" style="padding: 20px;">Tidak ada transaksi pembelian dalam periode ini.</td>
                </tr>
            @endforelse
            <tr class="totals-row">
                <td colspan="5">TOTAL</td>
                <td class="right">{{ number_format($totalQtyBeli, 0, ',', '.') }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td class="right">Rp {{ number_format($totalNilaiBeli, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
