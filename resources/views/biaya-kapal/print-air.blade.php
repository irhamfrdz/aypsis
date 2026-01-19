<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Biaya Air - {{ $biayaKapal->nomor_invoice }}</title>
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
            background-color: #e0f7fa;
            border-left: 3px solid #0097a7;
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
            border-left: 3px solid #0097a7;
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
            background-color: #0097a7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .print-button:hover {
            background-color: #00796b;
        }

        .back-button {
            position: fixed;
            top: 15px;
            right: 130px;
            padding: 8px 16px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <a href="{{ route('biaya-kapal.index') }}" class="back-button no-print">← Kembali</a>
    <button class="print-button no-print" onclick="window.print()">🖨️ Cetak Invoice</button>

    <div class="header">
        <h1>PERMOHONAN TRANSFER</h1>
        <p>BIAYA AIR TAWAR</p>
    </div>

    <div class="invoice-info">
        <div class="info-item">
            <span class="info-label">Nomor Invoice</span>
            <span class="info-separator">:</span>
            <span class="info-value"><strong>{{ $biayaKapal->nomor_invoice }}</strong></span>
        </div>
        <div class="info-item">
            <span class="info-label">Tanggal</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $biayaKapal->tanggal->format('d/M/Y') }}</span>
        </div>
        @if($biayaKapal->klasifikasiBiaya)
        <div class="info-item">
            <span class="info-label">Jenis Biaya</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $biayaKapal->klasifikasiBiaya->nama }}</span>
        </div>
        @endif
        @php
            // Show overall penerima if set, otherwise try to use first penerima from airDetails
            $penerimaDisplay = $biayaKapal->penerima ?? ($biayaKapal->airDetails->pluck('penerima')->filter()->unique()->values()->first() ?? null);
        @endphp
        @if($penerimaDisplay)
        <div class="info-item">
            <span class="info-label">Penerima</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $penerimaDisplay }}</span>
        </div>
        @endif
        @if($biayaKapal->nomor_referensi)
        <div class="info-item">
            <span class="info-label">Referensi</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $biayaKapal->nomor_referensi }}</span>
        </div>
        @endif
    </div>

    @php
        $totalSubTotal = 0;
        $totalJasaAir = 0;
        $totalPPH = 0;
        $totalGrandTotal = 0;
    @endphp

    <div class="section-title">
        <i class="fas fa-water"></i> Detail Biaya Air Tawar
    </div>

    <!-- Detail Table -->
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kapal</th>
                <th style="width: 10%;">Voyage</th>
                <th style="width: 12%;">Vendor</th>
                <th style="width: 8%;">Qty (Ton)</th>
                <th style="width: 10%;">Jasa Air</th>
                <th style="width: 12%;">Sub Total</th>
                <th style="width: 10%;">PPH (2%)</th>
                <th style="width: 12%;">Grand Total</th>
                <th style="width: 12%;">Penerima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($biayaKapal->airDetails as $index => $detail)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $detail->kapal ?? '-' }}</td>
                    <td>{{ $detail->voyage ?? '-' }}</td>
                    <td>{{ $detail->vendor ?? '-' }}</td>
                    <td class="number">{{ number_format($detail->kuantitas, 2, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($detail->jasa_air, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($detail->pph, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($detail->grand_total, 0, ',', '.') }}</td>
                    <td>{{ $detail->penerima ?? '-' }}</td>
                </tr>
                @php
                    $totalJasaAir += $detail->jasa_air;
                    $totalSubTotal += $detail->sub_total;
                    $totalPPH += $detail->pph;
                    $totalGrandTotal += $detail->grand_total;
                @endphp
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #666;">Tidak ada detail biaya air</td>
                </tr>
            @endforelse
            
            @if($biayaKapal->airDetails->count() > 0)
                <!-- Total Row -->
                <tr class="grand-total-row">
                    <td colspan="5" style="text-align: center; font-weight: bold;">TOTAL KESELURUHAN</td>
                    <td class="number">Rp {{ number_format($totalJasaAir, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($totalSubTotal, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($totalPPH, 0, ',', '.') }}</td>
                    <td class="number">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="section-title">
        Ringkasan Pembayaran
    </div>
    
    <table class="details-table" style="width: 50%; margin-left: auto;">
        <tbody>
            <tr>
                <td class="label-col">Total Sub Total (DPP)</td>
                <td class="value-col number">Rp {{ number_format($totalSubTotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label-col">Total Jasa Air</td>
                <td class="value-col number">Rp {{ number_format($totalJasaAir, 0, ',', '.') }}</td>
            </tr>
            <tr class="calculation-row">
                <td class="label-col">Total PPH (2%)</td>
                <td class="value-col number">Rp {{ number_format($totalPPH, 0, ',', '.') }}</td>
            </tr>
            <tr class="grand-total-row">
                <td class="label-col">TOTAL YANG DIBAYARKAN</td>
                <td class="value-col number">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($biayaKapal->keterangan)
    <div class="notes">
        <div>
            <span class="notes-title">Keterangan:</span> {{ $biayaKapal->keterangan }}
        </div>
    </div>
    @endif

    <div class="footer">
        <div class="signatures">
            <div class="signature-box">
                <div>Dibuat Oleh</div>
                <div class="signature-line">
                    &nbsp;
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
                    &nbsp;
                </div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 15px; font-size: 8px; color: #999;">
            Dicetak: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>
