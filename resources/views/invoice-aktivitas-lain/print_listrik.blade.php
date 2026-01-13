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
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }
        
        .header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #000;
        }
        
        .company-address {
            font-size: 11px;
            color: #555;
        }
        
        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 12px;
            border-radius: 5px;
        }
        
        .invoice-subtitle {
            text-align: center;
            font-size: 13px;
            color: #666;
            margin-top: -15px;
            margin-bottom: 20px;
            font-style: italic;
        }
        
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .invoice-info-left,
        .invoice-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .invoice-info-right {
            text-align: right;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 130px;
            color: #555;
        }
        
        .info-value {
            display: inline-block;
            color: #000;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th,
        table td {
            border: 1px solid #2563eb;
            padding: 10px;
            text-align: left;
        }
        
        table th {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-section {
            margin-top: 20px;
            float: right;
            width: 350px;
            border: 2px solid #2563eb;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .total-row:last-child {
            border-bottom: none;
        }
        
        .total-row.subtotal {
            background-color: #f8f9fa;
        }
        
        .total-row.pph {
            background-color: #dbeafe;
            color: #1e40af;
            font-weight: 600;
        }
        
        .total-row.grand-total {
            font-weight: bold;
            font-size: 15px;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
        }
        
        .total-label {
            font-weight: bold;
        }
        
        .pph-note {
            font-size: 10px;
            color: #666;
            text-align: right;
            margin-top: 5px;
            font-style: italic;
        }
        
        .footer {
            clear: both;
            margin-top: 60px;
            padding-top: 20px;
        }
        
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        
        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 70px;
            color: #555;
        }
        
        .signature-name {
            border-top: 2px solid #333;
            display: inline-block;
            padding-top: 5px;
            min-width: 150px;
            font-weight: 600;
        }
        
        .notes {
            margin-top: 20px;
            padding: 15px;
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 3px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #92400e;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-draft {
            background-color: #e0e0e0;
            color: #333;
        }
        
        .status-submitted {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background-color: #cfe2ff;
            color: #084298;
        }
        
        .status-paid {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #842029;
        }
        
        .highlight-box {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 5px;
            padding: 10px;
            margin: 15px 0;
            text-align: center;
        }
        
        .highlight-title {
            font-weight: bold;
            color: #92400e;
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .highlight-value {
            font-size: 18px;
            font-weight: bold;
            color: #15803d;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
            
            @page {
                margin: 15mm;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-weight: 600;
        }
        
        .print-button:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(37, 99, 235, 0.05);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="watermark">BIAYA LISTRIK</div>
    <button onclick="window.print()" class="print-button no-print">🖨️ Print Invoice</button>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">PT. NAMA PERUSAHAAN</div>
                <div class="company-address">
                    Alamat Perusahaan<br>
                    Telp: (021) 1234567 | Email: info@perusahaan.com
                </div>
            </div>
        </div>
        
        <!-- Invoice Title -->
        <div class="invoice-title">
            INVOICE BIAYA LISTRIK
        </div>
        <div class="invoice-subtitle">
            (Dengan Perhitungan PPH 2%)
        </div>
        
        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="invoice-info-left">
                <div class="info-row">
                    <span class="info-label">Nomor Invoice:</span>
                    <span class="info-value"><strong>{{ $invoice->nomor_invoice }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Invoice:</span>
                    <span class="info-value">{{ $invoice->tanggal_invoice->format('d F Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jenis Aktivitas:</span>
                    <span class="info-value">{{ $invoice->jenis_aktivitas ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jenis Biaya:</span>
                    <span class="info-value">
                        @if($invoice->klasifikasiBiayaUmum)
                            <strong>{{ $invoice->klasifikasiBiayaUmum->nama }}</strong>
                        @else
                            -
                        @endif
                    </span>
                </div>
            </div>
            <div class="invoice-info-right">
                <div class="info-row">
                    <span class="info-label">Penerima:</span>
                    <span class="info-value"><strong>{{ $invoice->penerima ?? '-' }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        @php
                            $statusClasses = [
                                'draft' => 'status-draft',
                                'submitted' => 'status-submitted',
                                'approved' => 'status-approved',
                                'paid' => 'status-paid',
                                'cancelled' => 'status-cancelled',
                            ];
                            $statusLabels = [
                                'draft' => 'Draft',
                                'submitted' => 'Submitted',
                                'approved' => 'Approved',
                                'paid' => 'Lunas',
                                'cancelled' => 'Dibatalkan',
                            ];
                        @endphp
                        <span class="status-badge {{ $statusClasses[$invoice->status] ?? 'status-draft' }}">
                            {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dibuat Oleh:</span>
                    <span class="info-value">{{ $invoice->createdBy->name ?? 'System' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Dibuat:</span>
                    <span class="info-value">{{ $invoice->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Invoice Details Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 60%;">Deskripsi</th>
                    <th style="width: 35%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <strong>Biaya Listrik</strong>
                        @if($invoice->klasifikasiBiayaUmum)
                            <br><em>{{ $invoice->klasifikasiBiayaUmum->nama }}</em>
                        @endif
                        @if($invoice->deskripsi)
                            <br>{{ $invoice->deskripsi }}
                        @endif
                        @if($invoice->catatan)
                            <br><small style="color: #666;">Catatan: {{ $invoice->catatan }}</small>
                        @endif
                    </td>
                    <td class="text-right" style="font-size: 14px; font-weight: 600;">
                        Rp {{ number_format($invoice->total, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- Total Section with PPH -->
        <div class="total-section">
            <!-- Subtotal -->
            <div class="total-row subtotal">
                <span class="total-label">Subtotal:</span>
                <span style="font-weight: 600;">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
            </div>
            
            <!-- PPH 2% -->
            <div class="total-row pph">
                <span class="total-label">PPH (2%):</span>
                <span style="font-weight: 700;">- Rp {{ number_format($invoice->pph ?? 0, 0, ',', '.') }}</span>
            </div>
            
            <!-- Grand Total -->
            <div class="total-row grand-total">
                <span class="total-label">GRAND TOTAL:</span>
                <span style="font-size: 17px;">Rp {{ number_format($invoice->grand_total ?? ($invoice->total - ($invoice->pph ?? 0)), 0, ',', '.') }}</span>
            </div>
        </div>
        
        <div style="clear: both;">
            <div class="pph-note">
                * PPH 2% dipotong sesuai peraturan perpajakan yang berlaku
            </div>
        </div>
        
        <!-- Highlight Box -->
        <div class="highlight-box">
            <div class="highlight-title">Jumlah yang Harus Dibayarkan</div>
            <div class="highlight-value">
                Rp {{ number_format($invoice->grand_total ?? ($invoice->total - ($invoice->pph ?? 0)), 0, ',', '.') }}
            </div>
            <small style="color: #666;">(Setelah Potongan PPH 2%)</small>
        </div>
        
        <!-- Notes -->
        @if($invoice->catatan || $invoice->deskripsi)
        <div class="notes">
            <div class="notes-title">📝 Catatan:</div>
            <div>
                @if($invoice->deskripsi)
                    <strong>Deskripsi:</strong> {{ $invoice->deskripsi }}<br>
                @endif
                @if($invoice->catatan)
                    <strong>Catatan:</strong> {{ $invoice->catatan }}
                @endif
            </div>
        </div>
        @endif
        
        <!-- Signature Section -->
        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-title">Dibuat Oleh,</div>
                    <div class="signature-name">{{ $invoice->createdBy->name ?? '_______________' }}</div>
                </div>
                <div class="signature-box">
                    <div class="signature-title">Disetujui Oleh,</div>
                    <div class="signature-name">_______________</div>
                </div>
                <div class="signature-box">
                    <div class="signature-title">Penerima,</div>
                    <div class="signature-name">{{ $invoice->penerima ?? '_______________' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Footer Info -->
        <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666; border-top: 2px solid #2563eb; padding-top: 15px;">
            <strong>Invoice Biaya Listrik dengan Perhitungan PPH</strong><br>
            Dicetak pada: {{ now()->format('d F Y H:i:s') }} | Dokumen ini sah tanpa tanda tangan dan stempel
        </div>
    </div>
    
    <script>
        // Auto print on load (optional)
        // window.onload = function() { 
        //     setTimeout(function() {
        //         window.print(); 
        //     }, 500);
        // }
    </script>
</body>
</html>
