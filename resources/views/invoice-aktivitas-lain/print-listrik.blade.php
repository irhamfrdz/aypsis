<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Biaya Listrik - {{ $invoice->nomor_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 12px;
            font-weight: bold;
        }
        
        .info-section {
            margin-bottom: 20px;
            font-size: 11px;
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
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .custom-table th, 
        .custom-table td {
            border: 1px solid #000;
            padding: 5px 8px;
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
            margin-top: 20px;
            min-height: 60px;
        }
        
        .keterangan-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .footer {
            margin-top: 30px;
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
            height: 60px;
            vertical-align: bottom !important;
        }
        
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            @page { margin: 1cm; size: auto; }
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
    <a href="{{ route('invoice-aktivitas-lain.index') }}" class="btn-back no-print">Kembali</a>

    <div class="header">
        <h1>PERMOHONAN TRANSFER</h1>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-label">Nomor Invoice</td>
                <td>: <strong>{{ $invoice->nomor_invoice }}</strong></td>
                <td class="info-label">Tanggal Invoice</td>
                <td>: {{ $invoice->tanggal_invoice->format('d/M/Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Vendor</td>
                <td>: {{ $invoice->vendor_listrik ?? '-' }}</td>
                <td class="info-label">Referensi</td>
                <td>: {{ $invoice->referensi ?? '-' }}</td>
            </tr>
        </table>
    </div>

    @php
        $totalDPP = 0;
        $totalPPH = 0;
        $totalGrandTotal = 0;
    @endphp

    <!-- TABLE: DETAIL BIAYA LISTRIK -->
    <div class="section-header">Detail Biaya Listrik:</div>
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 25%;">Referensi</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">Penerima</th>
                <th style="width: 13%;">DPP</th>
                <th style="width: 13%;">PPH</th>
                <th style="width: 14%;">Grandtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biayaListrikEntries as $biayaListrik)
                <tr>
                    <td>{{ $biayaListrik->referensi ?? '-' }}</td>
                    <td class="text-center">{{ $biayaListrik->tanggal ? $biayaListrik->tanggal->format('d/M/Y') : '-' }}</td>
                    <td>{{ $biayaListrik->penerima ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($biayaListrik->dpp, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($biayaListrik->pph, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($biayaListrik->grand_total, 0, ',', '.') }}</td>
                </tr>
                @php
                    $totalDPP += $biayaListrik->dpp;
                    $totalPPH += $biayaListrik->pph;
                    $totalGrandTotal += $biayaListrik->grand_total;
                @endphp
            @endforeach
            
            <tr class="total-row">
                <td colspan="3" style="text-align: left; padding-left: 10px;">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($totalDPP, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalPPH, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- KETERANGAN BOX -->
    @if($invoice->deskripsi || $invoice->catatan)
    <div class="keterangan-box">
        <div class="keterangan-title">Keterangan:</div>
        <div>
            @if($invoice->deskripsi)
            <div style="margin-bottom: 5px;">
                <span class="font-bold">Deskripsi:</span> {{ $invoice->deskripsi }}
            </div>
            @endif
            
            @if($invoice->catatan)
            <div>
                <span class="font-bold">Catatan:</span> {{ $invoice->catatan }}
            </div>
            @endif
        </div>
    </div>
    @endif
    
    <!-- FOOTER SIGNATURES -->
    <div class="footer">
        <table class="signature-table">
            <tr>
                <td><strong>Dibuat Oleh:</strong></td>
                <td><strong>Diperiksa Oleh:</strong></td>
                <td><strong>Disetujui Oleh:</strong></td>
            </tr>
            <tr>
                <td class="signature-space">
                    @if($invoice->creator)
                        <br>{{ $invoice->creator->name }}
                    @else
                        ___________
                    @endif
                </td>
                <td class="signature-space">___________</td>
                <td class="signature-space">
                    @if($invoice->approver)
                        <br>{{ $invoice->approver->name }}
                    @else
                        ___________
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
