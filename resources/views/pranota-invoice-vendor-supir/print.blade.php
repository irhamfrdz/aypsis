<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pranota Invoice Vendor Supir - {{ $pranota->no_pranota }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 12px;
            font-weight: bold;
        }
        
        .info-section {
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        .info-table {
            width: 100%;
        }
        
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        
        .info-label {
            font-weight: bold;
            width: 130px;
        }
        
        .section-header {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 10px;
        }
        
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        .custom-table th, 
        .custom-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: middle;
        }
        
        .custom-table th {
            text-align: center;
            font-weight: bold;
            background-color: #fff;
            border-bottom: 2px solid #000; /* Thicker border for header */
        }
        
        .custom-table tr.total-row td {
            background-color: #e9ecef;
            font-weight: bold;
            border-top: 2px solid #000;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .keterangan-box {
            border: 2px solid #000;
            padding: 10px;
            margin-top: 10px;
            min-height: 40px;
        }
        
        .keterangan-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .footer {
            margin-top: 15px;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        
        .signature-table td {
            border: none;
            text-align: center;
            padding: 5px;
            width: 33.33%;
        }
        
        .signature-space {
            height: 40px;
        }
        
        @page {
            size: 165mm 215mm;
            margin: 5mm;
        }

        @media print {
            .no-print { display: none !important; }
            body { 
                padding: 0; 
                margin: 0;
            }
        }
        
        .btn-print {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .btn-back {
            position: fixed;
            top: 10px;
            right: 70px;
            padding: 5px 10px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 3px;
            text-decoration: none;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <button class="btn-print no-print" onclick="window.print()">Print</button>
    <a href="{{ route('pranota-invoice-vendor-supir.index') }}" class="btn-back no-print">Kembali</a>

    <div class="header">
        <h1>PERMOHONAN TRANSFER</h1>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-label">Nomor Pranota</td>
                <td>: <strong>{{ $pranota->no_pranota }}</strong></td>
                <td class="info-label">Tanggal</td>
                <td>: {{ $pranota->tanggal_pranota->format('d/M/Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Penerima</td>
                <td>: {{ $pranota->vendor->nama_vendor ?? '-' }}</td>
                <td class="info-label">Vendor</td>
                <td>: {{ $pranota->vendor->nama_vendor ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Status</td>
                <td>: {{ strtoupper(str_replace('_', ' ', $pranota->status_pembayaran)) }}</td>
                <td class="info-label">Dibuat Oleh</td>
                <td>: {{ optional($pranota->creator)->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section-header">Detail Invoice:</div>
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">No Invoice</th>
                <th style="width: 25%;">Tanggal Invoice</th>
                <th style="text-align: right;">Total Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pranota->invoiceTagihanVendors as $index => $invoice)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center font-bold">{{ $invoice->no_invoice }}</td>
                <td class="text-center">{{ $invoice->tanggal_invoice->format('d/M/Y') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($invoice->total_nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-right font-bold">SUBTOTAL KESELURUHAN</td>
                <td class="text-right font-bold">Rp {{ number_format($pranota->total_nominal + $pranota->pph, 0, ',', '.') }}</td>
            </tr>
            @if($pranota->pph > 0)
            <tr>
                <td colspan="3" class="text-right font-bold">PPH 2%</td>
                <td class="text-right font-bold">- Rp {{ number_format($pranota->pph, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($pranota->total_uang_muat > 0)
            <tr>
                @php
                    $totalSJ = 0;
                    foreach($pranota->invoiceTagihanVendors as $invoice) {
                        $totalSJ += $invoice->tagihanSupirVendors->count();
                    }
                @endphp
                <td colspan="3" class="text-right font-bold text-indigo-700">TOTAL UANG MUAT ({{ $totalSJ }} SJ)</td>
                <td class="text-right font-bold text-indigo-700">+ Rp {{ number_format($pranota->total_uang_muat, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="3" class="text-right">GRAND TOTAL KESELURUHAN</td>
                <td class="text-right" style="font-size: 11px;">Rp {{ number_format($pranota->grand_total > 0 ? $pranota->grand_total : $pranota->total_nominal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-header" style="margin-top: 20px;">Detail Surat Jalan:</div>
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 11%;">No SJ</th>
                <th style="width: 9%;">Tgl SJ</th>
                <th style="width: 12%;">No Kontainer</th>
                <th style="width: 10%;">Voyage</th>
                <th style="width: 5%;">Size</th>
                <th>Rute (Dari -> Ke)</th>
                <th style="width: 14%; text-align: right;">Nominal SJ</th>
                <th style="width: 14%; text-align: right;">Uang Muat</th>
            </tr>
        </thead>
        <tbody>
            @php $sjNo = 1; @endphp
            @foreach($pranota->invoiceTagihanVendors as $invoice)
                @foreach($invoice->tagihanSupirVendors as $tagihan)
                <tr>
                    <td class="text-center">{{ $sjNo++ }}</td>
                    <td class="text-center">{{ $tagihan->suratJalan->no_surat_jalan ?? '-' }}</td>
                    <td class="text-center">{{ optional($tagihan->suratJalan->tanggal_surat_jalan)->format('d/M/y') ?? '-' }}</td>
                    <td class="text-center">{{ $tagihan->suratJalan->no_kontainer ?? '-' }}</td>
                    <td class="text-center">{{ optional($tagihan->suratJalan->prospeks->first())->no_voyage ?? '-' }}</td>
                    <td class="text-center">{{ $tagihan->jenis_kontainer ?? ($tagihan->suratJalan->size ?? '-') }}</td>
                    <td style="font-size: 9px;">{{ $tagihan->dari ?? ($tagihan->suratJalan->dari ?? '-') }} -> {{ $tagihan->ke ?? ($tagihan->suratJalan->ke ?? '-') }}</td>
                    <td class="text-right" style="white-space: nowrap;">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="white-space: nowrap;">Rp {{ number_format($tagihan->uang_muat ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    @if($pranota->keterangan)
    <div class="keterangan-box">
        <div class="keterangan-title">Keterangan:</div>
        <div>
            {!! nl2br(e($pranota->keterangan)) !!}
        </div>
    </div>
    @endif

    <div class="footer">
        <table class="signature-table">
            <tr>
                <td><strong>Dibuat Oleh:</strong></td>
                <td><strong>Diperiksa Oleh:</strong></td>
                <td><strong>Disetujui Oleh:</strong></td>
            </tr>
            <tr>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
            </tr>
            <tr>
                <td>___________</td>
                <td>___________</td>
                <td>___________</td>
            </tr>
        </table>
    </div>
</body>
</html>
