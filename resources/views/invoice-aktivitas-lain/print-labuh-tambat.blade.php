<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Labuh Tambat - {{ $invoice->nomor_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 15px;
            font-weight: bold;
        }
        
        .invoice-info {
            margin-bottom: 12px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 20px;
        }
        
        .info-item {
            display: flex;
            padding: 3px 0;
        }
        
        .info-label {
            width: 120px;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .info-separator {
            margin: 0 5px;
            flex-shrink: 0;
        }
        
        .info-value {
            flex: 1;
            font-weight: bold;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 11px;
        }
        
        .details-table th,
        .details-table td {
            padding: 8px 10px;
            border: 2px solid #333;
            text-align: left;
            font-weight: bold;
        }
        
        .details-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #1a1a1a;
        }
        
        .number {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 12px;
        }
        
        .signature-box {
            text-align: center;
            font-size: 10px;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 3px;
            font-weight: bold;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            @page {
                margin: 1cm;
                size: A4;
            }
        }
        
        .print-button {
            position: fixed;
            top: 15px;
            right: 15px;
            padding: 8px 16px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Cetak Invoice</button>

    <div class="invoice-info">
        <div class="info-item">
            <span class="info-label">Nomor Invoice</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $invoice->nomor_invoice }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Tanggal Invoice</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $invoice->tanggal_invoice->format('d/M/Y') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Vendor</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $invoice->vendor_labuh_tambat ?? '-' }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Nomor Rekening</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $invoice->nomor_rekening_labuh ?? '-' }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Voyage</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $invoice->nomor_voyage ?? '-' }}</span>
        </div>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 70%;">Deskripsi</th>
                <th style="width: 30%; text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sub Total Biaya Labuh Tambat</td>
                <td class="number">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>PPH 2%</td>
                <td class="number">Rp {{ number_format($invoice->pph, 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #f0f0f0; font-size: 12px;">
                <td style="font-weight: bold;">TOTAL PEMBAYARAN (Sub Total - PPH)</td>
                <td class="number" style="font-weight: bold;">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($invoice->deskripsi || $invoice->catatan)
    <div style="margin-top: 15px; padding: 10px; background-color: #f8f9fa; border: 1px solid #ddd;">
        @if($invoice->deskripsi)
            <p><strong>Deskripsi:</strong> {{ $invoice->deskripsi }}</p>
        @endif
        @if($invoice->catatan)
            <p><strong>Catatan:</strong> {{ $invoice->catatan }}</p>
        @endif
    </div>
    @endif

    <div class="footer">
        <div class="signatures">
            <div class="signature-box">
                <div>Dibuat Oleh</div>
                <div class="signature-line">{{ $invoice->creator->name ?? '-' }}</div>
            </div>
            <div class="signature-box">
                <div>Diperiksa Oleh</div>
                <div class="signature-line">&nbsp;</div>
            </div>
            <div class="signature-box">
                <div>Disetujui Oleh</div>
                <div class="signature-line">{{ $invoice->approver->name ?? '-' }}</div>
            </div>
        </div>
        <div style="text-align: center; margin-top: 20px; font-size: 8px; color: #999;">
            Dicetak pada: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
