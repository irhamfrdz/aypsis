<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Invoice Kanisir Ban - {{ $invoice->nomor_invoice }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 120px;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .content-table th, .content-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }
        .content-table th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
        }
        .content-table td.text-right {
            text-align: right;
        }
        .content-table td.text-center {
            text-align: center;
        }
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 50px;
            width: 100%;
        }
        @media print {
            body { 
                padding: 0; 
                margin: 0;
            }
            @page {
                size: A4;
                margin: 1cm;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>INVOICE MASAK KANISIR</h1>
        <p>{{ $invoice->nomor_invoice }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Tanggal</td>
            <td>: {{ date('d F Y', strtotime($invoice->tanggal_invoice)) }}</td>
            <td class="label">Total Ban</td>
            <td>: {{ $invoice->jumlah_ban }} Unit</td>
        </tr>
        <tr>
            <td class="label">Vendor</td>
            <td>: {{ $invoice->vendor }}</td>
            <td class="label">Total Biaya</td>
            <td>: Rp {{ number_format($invoice->total_biaya, 0, ',', '.') }}</td>
        </tr>
        @if($invoice->keterangan)
        <tr>
            <td class="label">Keterangan</td>
            <td colspan="3">: {{ $invoice->keterangan }}</td>
        </tr>
        @endif
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">No</th>
                <th>Nomor Seri / ID Ban</th>
                <th>Detail Ban (Merk / Ukuran)</th>
                <th class="text-right">Biaya Masak</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    {{ $item->stockBan ? ($item->stockBan->nomor_seri ?: 'ID: ' . $item->stockBan->id) : '-' }}
                </td>
                <td>
                    @if($item->stockBan)
                        {{ $item->stockBan->namaStockBan->nama ?? '-' }} <br>
                        <small>{{ $item->stockBan->merk ?? '-' }} - {{ $item->stockBan->ukuran ?? '-' }}</small>
                    @else
                        Ban Terhapus
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right" style="font-weight: bold;">TOTAL</td>
                <td class="text-right" style="font-weight: bold;">Rp {{ number_format($invoice->total_biaya, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Dibuat Oleh,</p>
            <div class="signature-line"></div>
            <p>( Admin )</p>
        </div>
        <div class="signature-box">
            <p>Disetujui Oleh,</p>
            <div class="signature-line"></div>
            <p>( Pimpinan )</p>
        </div>
    </div>

</body>
</html>
