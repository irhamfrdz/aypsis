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
            line-height: 1.4;
            color: #333;
            padding: 15px;
            font-weight: bold;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 4px;
            color: #1a1a1a;
        }
        
        .header p {
            font-size: 9px;
            color: #666;
            line-height: 1.2;
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
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 8px;
            padding: 6px 10px;
            background-color: #f0f0f0;
            border-left: 3px solid #2563eb;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10px;
        }
        
        .details-table th,
        .details-table td {
            padding: 6px 8px;
            border: 2px solid #333;
            text-align: left;
            font-weight: bold;
        }
        
        .details-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #1a1a1a;
            font-size: 10px;
        }
        
        .details-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .details-table .label-col {
            width: 60%;
            font-weight: bold;
        }
        
        .details-table .value-col {
            width: 40%;
            text-align: right;
            font-weight: bold;
        }
        
        .calculation-row {
            background-color: #e3f2fd !important;
        }
        
        .total-row {
            background-color: #fff3cd !important;
            font-weight: bold;
        }
        
        .grand-total-row {
            background-color: #d4edda !important;
            font-weight: bold;
            font-size: 11px;
        }
        
        .number {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .footer {
            margin-top: 15px;
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
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 3px;
            font-weight: bold;
        }
        
        .notes {
            margin-top: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border-left: 3px solid #ffc107;
            font-size: 9px;
            font-weight: bold;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none;
            }
            
            @page {
                margin: 1cm;
                size: 21.6cm 33cm;
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
            font-size: 12px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-button:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Cetak Invoice</button>

    <div class="header">
        <h1>PERMOHONAN TRANSFER</h1>
    </div>

    <div class="invoice-info">
        <div class="info-item">
            <span class="info-label">Nomor Invoice</span>
            <span class="info-separator">:</span>
            <span class="info-value"><strong>{{ $invoice->nomor_invoice }}</strong></span>
        </div>
        <div class="info-item">
            <span class="info-label">Tanggal Invoice</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $invoice->tanggal_invoice->format('d/M/Y') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Jenis Biaya</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $invoice->klasifikasiBiayaUmum->nama ?? 'Biaya Listrik' }}</span>
        </div>
        @if($invoice->referensi)
        <div class="info-item">
            <span class="info-label">Referensi</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $invoice->referensi }}</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Penerima</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $invoice->penerima }}</span>
        </div>
    </div>

    @php
        $totalDPP = 0;
        $totalPPH = 0;
        $totalGrandTotal = 0;
    @endphp

    @foreach($biayaListrikEntries as $index => $biayaListrik)
        <div class="section-title">DETAIL BIAYA LISTRIK #{{ $index + 1 }}{{ $biayaListrik->referensi ? ' - ' . $biayaListrik->referensi : '' }}</div>

        <table class="details-table">
            <thead>
                <tr>
                    <th class="label-col">Keterangan</th>
                    <th class="value-col">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <!-- LWBP Baru -->
                <tr>
                    <td class="label-col">LWBP Baru</td>
                    <td class="value-col number">{{ number_format($biayaListrik->lwbp_baru, 0, ',', '.') }}</td>
                </tr>
                
                <!-- LWBP Lama -->
                <tr>
                    <td class="label-col">LWBP Lama</td>
                    <td class="value-col number">{{ number_format($biayaListrik->lwbp_lama, 0, ',', '.') }}</td>
                </tr>
                
                <!-- WBP -->
                <tr class="calculation-row">
                    <td class="label-col">WBP (17% dari selisih)</td>
                    <td class="value-col number">{{ number_format($biayaListrik->wbp, 0, ',', '.') }}</td>
                </tr>
                
                <!-- LWBP -->
                <tr class="calculation-row">
                    <td class="label-col">LWBP (LWBP Baru - LWBP Lama - WBP)</td>
                    <td class="value-col number">{{ number_format($biayaListrik->lwbp, 0, ',', '.') }}</td>
                </tr>
                
                <!-- LWBP Tarif -->
                <tr>
                    <td class="label-col">LWBP Tarif</td>
                    <td class="value-col number">Rp {{ number_format($biayaListrik->lwbp_tarif, 0, ',', '.') }}</td>
                </tr>
                
                <!-- WBP Tarif -->
                <tr>
                    <td class="label-col">WBP Tarif</td>
                    <td class="value-col number">Rp {{ number_format($biayaListrik->wbp_tarif, 0, ',', '.') }}</td>
                </tr>
                
                <!-- Tarif 1 -->
                <tr class="calculation-row">
                    <td class="label-col">Tarif 1 (LWBP × LWBP Tarif)</td>
                    <td class="value-col number">Rp {{ number_format($biayaListrik->tarif_1, 0, ',', '.') }}</td>
                </tr>
                
                <!-- Tarif 2 -->
                <tr class="calculation-row">
                    <td class="label-col">Tarif 2 (WBP × WBP Tarif)</td>
                    <td class="value-col number">Rp {{ number_format($biayaListrik->tarif_2, 0, ',', '.') }}</td>
                </tr>
                
                <!-- Biaya Beban -->
                <tr>
                    <td class="label-col">Biaya Beban</td>
                    <td class="value-col number">Rp {{ number_format($biayaListrik->biaya_beban, 0, ',', '.') }}</td>
                </tr>
                
                <!-- PPJU -->
                <tr class="calculation-row">
                    <td class="label-col">PPJU (3% dari Tarif 1 + Tarif 2 + Biaya Beban)</td>
                    <td class="value-col number">Rp {{ number_format($biayaListrik->ppju, 0, ',', '.') }}</td>
                </tr>
                
                <!-- DPP -->
                <tr class="total-row">
                    <td class="label-col">DPP (Dasar Pengenaan Pajak)</td>
                    <td class="value-col number">Rp {{ number_format($biayaListrik->dpp, 0, ',', '.') }}</td>
                </tr>
                
                <!-- PPH -->
                <tr class="calculation-row">
                    <td class="label-col">PPH (10%)</td>
                    <td class="value-col number">Rp {{ number_format($biayaListrik->pph, 0, ',', '.') }}</td>
                </tr>
                
                <!-- Grand Total per entry -->
                <tr class="grand-total-row">
                    <td class="label-col">GRAND TOTAL (DPP - PPH)</td>
                    <td class="value-col number">Rp {{ number_format($biayaListrik->grand_total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        
        @php
            $totalDPP += $biayaListrik->dpp;
            $totalPPH += $biayaListrik->pph;
            $totalGrandTotal += $biayaListrik->grand_total;
        @endphp
    @endforeach

    @if($biayaListrikEntries->count() > 1)
        <div class="section-title" style="background-color: #d4edda; font-size: 13px;">TOTAL KESELURUHAN</div>
        
        <table class="details-table">
            <tbody>
                <tr class="total-row">
                    <td class="label-col">Total DPP</td>
                    <td class="value-col number">Rp {{ number_format($totalDPP, 0, ',', '.') }}</td>
                </tr>
                <tr class="calculation-row">
                    <td class="label-col">Total PPH (10%)</td>
                    <td class="value-col number">Rp {{ number_format($totalPPH, 0, ',', '.') }}</td>
                </tr>
                <tr class="grand-total-row" style="font-size: 12px;">
                    <td class="label-col">TOTAL GRAND TOTAL</td>
                    <td class="value-col number">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    @if($invoice->deskripsi || $invoice->catatan)
    <div class="notes">
        @if($invoice->deskripsi)
        <div>
            <span class="notes-title">Deskripsi:</span> {{ $invoice->deskripsi }}
        </div>
        @endif
        
        @if($invoice->catatan)
        <div style="margin-top: 5px;">
            <span class="notes-title">Catatan:</span> {{ $invoice->catatan }}
        </div>
        @endif
    </div>
    @endif

    <div class="footer">
        <div class="signatures">
            <div class="signature-box">
                <div>Dibuat Oleh</div>
                <div class="signature-line">
                    {{ $invoice->creator->name ?? '-' }}
                </div>
            </div>
            
            <div class="signature-box">
                <div>Diperiksa Oleh</div>
                <div class="signature-line">
                    &nbsp;
                </div>
            </div>
            
            <div class="signature-box">
                <div>Disetujui Oleh</div>
                <div class="signature-line">
                    {{ $invoice->approver->name ?? '-' }}
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 15px; font-size: 8px; color: #999;">
            Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
