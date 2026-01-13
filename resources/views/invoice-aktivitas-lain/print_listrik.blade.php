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
            line-height: 1.3;
            color: #000;
            padding: 15px;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }
        
        .header {
            margin-bottom: 15px;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .invoice-number {
            font-size: 10px;
            text-align: right;
        }
        
        .invoice-date {
            font-size: 10px;
            margin-top: 5px;
        }
        
        .invoice-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
            text-decoration: underline;
        }
        
        .doc-info {
            font-size: 10px;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        table th,
        table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            font-size: 10px;
        }
        
        table th {
            background: #fff;
            font-weight: bold;
            text-align: center;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-row td {
            font-weight: bold;
            background: #f5f5f5;
        }
        
        .account-info {
            margin: 10px 0;
            font-size: 10px;
        }
        
        .account-info div {
            margin-bottom: 3px;
        }
        
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        
        .signature-title {
            font-size: 10px;
            margin-bottom: 50px;
        }
        
        .signature-line {
            border-top: 1px dotted #000;
            display: inline-block;
            min-width: 120px;
            padding-top: 3px;
            font-size: 10px;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none;
            }
            
            @page {
                margin: 10mm;
            }
        }
        
        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 8px 16px;
            background: #333;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 12px;
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #000;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Print</button>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="company-name">BIAYA LISTRIK</div>
                <div class="invoice-number">{{ $invoice->nomor_invoice }}</div>
            </div>
            <div class="invoice-date">INVOICE TGL: {{ $invoice->tanggal_invoice->format('d M Y') }}</div>
        </div>
        
        <!-- Invoice Title -->
        <div class="invoice-title">
            Form Permohonan Transfer
        </div>
        
        <!-- Document Info -->
        <div class="doc-info">
            <strong>No Memo : {{ $invoice->nomor_invoice }}</strong>
            <span style="margin-left: 100px;">Penerima: <strong>{{ $invoice->penerima ?? '-' }}</strong></span>
        </div>
        
        <!-- Invoice Details Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 10%;">Tgl Req</th>
                    <th style="width: 37%;">Referensi</th>
                    <th style="width: 10%;">Voyage</th>
                    <th style="width: 20%;">Jenis Biaya</th>
                    <th style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>{{ $invoice->tanggal_invoice->format('Y-m-d') }}</td>
                    <td>LISTRIK BULAN {{ strtoupper($invoice->tanggal_invoice->format('F Y')) }}</td>
                    <td></td>
                    <td>PPH</td>
                    <td class="text-right">{{ number_format(-($invoice->pph ?? 0), 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>{{ $invoice->tanggal_invoice->format('Y-m-d') }}</td>
                    <td>LISTRIK BULAN {{ strtoupper($invoice->tanggal_invoice->format('F Y')) }}</td>
                    <td></td>
                    <td>Biaya Listrik</td>
                    <td class="text-right">{{ number_format($invoice->total, 2, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="5" class="text-right"><strong>Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($invoice->grand_total ?? ($invoice->total - ($invoice->pph ?? 0)), 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Account Information -->
        <div class="account-info">
            <div>No Rekening :</div>
            <div>Nama Pemilik :</div>
            <div>Bank Tujuan :</div>
            <div>Lokasi :</div>
        </div>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">(Pemohon)</div>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box">
                <div class="signature-title">(Pemeriksa)</div>
                <div class="signature-line">{{ $invoice->penerima ?? '' }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">(Kasir)</div>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>
</body>
</html>