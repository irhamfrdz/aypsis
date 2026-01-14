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
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        
        .header h1 {
            font-size: 16px;
            margin-bottom: 3px;
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
            padding: 2px 0;
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
        }
        
        .section-title {
            font-size: 11px;
            font-weight: bold;
            margin-top: 12px;
            margin-bottom: 6px;
            padding: 5px 8px;
            background-color: #f0f0f0;
            border-left: 3px solid #2563eb;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9px;
        }
        
        .details-table th,
        .details-table td {
            padding: 5px 6px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        .details-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #1a1a1a;
            font-size: 9px;
        }
        
        .details-table tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        .details-table .label-col {
            width: 60%;
            font-weight: 500;
        }
        
        .details-table .value-col {
            width: 40%;
            text-align: right;
            font-weight: 500;
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
            font-size: 10px;
        }
        
        .number {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        
        .signature-box {
            text-align: center;
            font-size: 9px;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 3px;
            font-weight: bold;
        }
        
        .notes {
            margin-top: 12px;
            padding: 8px;
            background-color: #f8f9fa;
            border-left: 3px solid #ffc107;
            font-size: 9px;
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
                margin: 0.8cm;
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
        <h1>INVOICE BIAYA LISTRIK</h1>
        <p>PT. YOUR COMPANY NAME<br>
        Alamat Perusahaan | Telp: (021) xxx-xxxx | Email: info@company.com</p>
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
            <span class="info-value">{{ $invoice->tanggal_invoice->format('d/m/Y') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Jenis Aktivitas</span>
            <span class="info-separator">:</span>
            <span class="info-value">{{ $invoice->jenis_aktivitas }}</span>
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

    <div class="section-title">DETAIL PERHITUNGAN BIAYA LISTRIK</div>

    <table class="details-table">
        <thead>
            <tr>
                <th class="label-col">Keterangan</th>
                <th class="value-col">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <!-- Input Fields -->
            <tr>
                <td class="label-col">LWBP Baru</td>
                <td class="value-col number">{{ number_format($biayaListrik->lwbp_baru, 2, ',', '.') }} kWh</td>
            </tr>
            <tr>
                <td class="label-col">LWBP Lama</td>
                <td class="value-col number">{{ number_format($biayaListrik->lwbp_lama, 2, ',', '.') }} kWh</td>
            </tr>
            
            <!-- Calculated Fields -->
            <tr class="calculation-row">
                <td class="label-col">WBP (Waktu Beban Puncak)</td>
                <td class="value-col number">{{ number_format($biayaListrik->wbp, 2, ',', '.') }} kWh</td>
            </tr>
            <tr class="calculation-row">
                <td class="label-col">LWBP (Luar Waktu Beban Puncak)</td>
                <td class="value-col number">{{ number_format($biayaListrik->lwbp, 2, ',', '.') }} kWh</td>
            </tr>
            
            <!-- Tariff Fields -->
            <tr>
                <td class="label-col">LWBP Tarif</td>
                <td class="value-col number">Rp {{ number_format($biayaListrik->lwbp_tarif, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label-col">WBP Tarif</td>
                <td class="value-col number">Rp {{ number_format($biayaListrik->wbp_tarif, 2, ',', '.') }}</td>
            </tr>
            
            <!-- Tarif Calculations -->
            <tr class="calculation-row">
                <td class="label-col">Tarif 1</td>
                <td class="value-col number">Rp {{ number_format($biayaListrik->tarif_1, 0, ',', '.') }}</td>
            </tr>
            <tr class="calculation-row">
                <td class="label-col">Tarif 2</td>
                <td class="value-col number">Rp {{ number_format($biayaListrik->tarif_2, 0, ',', '.') }}</td>
            </tr>
            
            <!-- Biaya Beban -->
            <tr>
                <td class="label-col">Biaya Beban</td>
                <td class="value-col number">Rp {{ number_format($biayaListrik->biaya_beban, 0, ',', '.') }}</td>
            </tr>
            
            <!-- PPJU -->
            <tr class="calculation-row">
                <td class="label-col">PPJU (Pajak Penerangan Jalan Umum)</td>
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
            
            <!-- Grand Total -->
            <tr class="grand-total-row">
                <td class="label-col">GRAND TOTAL</td>
                <td class="value-col number">Rp {{ number_format($biayaListrik->grand_total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

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
