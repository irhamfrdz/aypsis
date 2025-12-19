<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->nomor_invoice }}</title>
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
            border-bottom: 3px solid #333;
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
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
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
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 130px;
        }
        
        .info-value {
            display: inline-block;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th,
        table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        
        table th {
            background-color: #f0f0f0;
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
            width: 300px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        .total-row.grand-total {
            font-weight: bold;
            font-size: 14px;
            background-color: #f0f0f0;
            border: 2px solid #333;
            border-bottom: 2px solid #333;
        }
        
        .total-label {
            font-weight: bold;
        }
        
        .footer {
            clear: both;
            margin-top: 40px;
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
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 60px;
        }
        
        .signature-name {
            border-top: 1px solid #333;
            display: inline-block;
            padding-top: 5px;
            min-width: 150px;
        }
        
        .notes {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 3px solid #333;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
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
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">🖨️ Print</button>
    
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
            INVOICE AKTIVITAS LAIN
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
                    <span class="info-value">{{ $invoice->tanggal_invoice->format('d/M/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jenis Aktivitas:</span>
                    <span class="info-value">{{ $invoice->jenis_aktivitas ?? '-' }}</span>
                </div>
            </div>
            <div class="invoice-info-right">
                <div class="info-row">
                    <span class="info-label">Penerima:</span>
                    <span class="info-value">{{ $invoice->penerima ?? '-' }}</span>
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
                                'paid' => 'Paid',
                                'cancelled' => 'Cancelled',
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
            </div>
        </div>
        
        <!-- Invoice Details Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 45%;">Deskripsi</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 15%;">Harga Satuan</th>
                    <th style="width: 25%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->details ?? [] as $index => $detail)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $detail->deskripsi ?? '-' }}</td>
                        <td class="text-center">{{ $detail->qty ?? 1 }}</td>
                        <td class="text-right">Rp {{ number_format($detail->harga_satuan ?? 0, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($detail->total ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada detail item</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Subtotal:</span>
                <span>Rp {{ number_format($invoice->subtotal ?? 0, 0, ',', '.') }}</span>
            </div>
            @if(($invoice->ppn ?? 0) > 0)
            <div class="total-row">
                <span class="total-label">PPN ({{ $invoice->ppn_persen ?? 11 }}%):</span>
                <span>Rp {{ number_format($invoice->ppn ?? 0, 0, ',', '.') }}</span>
            </div>
            @endif
            @if(($invoice->pph ?? 0) > 0)
            <div class="total-row">
                <span class="total-label">PPH ({{ $invoice->pph_persen ?? 2 }}%):</span>
                <span>Rp {{ number_format($invoice->pph ?? 0, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span class="total-label">TOTAL:</span>
                <span>Rp {{ number_format($invoice->total ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <!-- Notes -->
        @if($invoice->keterangan)
        <div class="notes">
            <div class="notes-title">Catatan:</div>
            <div>{{ $invoice->keterangan }}</div>
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
        <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 10px;">
            Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
    
    <script>
        // Auto print on load (optional, uncomment if needed)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
