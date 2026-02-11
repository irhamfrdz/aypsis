<!DOCTYPE html>
<html lang="id">
@php
    $paperSize = request('paper_size', 'Half Folio');
    $paperMap = [
        'Half Folio' => [
            'size' => '215.9mm 165.1mm',
            'width' => '215.9mm',
            'height' => '165.1mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '10px',
            'headerH1' => '16px',
            'tableFont' => '9px',
        ],
        'Folio' => [
            'size' => '215.9mm 330.2mm',
            'width' => '215.9mm',
            'height' => '330.2mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '11px',
            'headerH1' => '18px',
            'tableFont' => '10px',
        ],
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '210mm',
            'fontSize' => '11px',
            'headerH1' => '18px',
            'tableFont' => '10px',
        ],
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half Folio'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Biaya Trucking - {{ $biayaKapal->nomor_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: {{ $currentPaper['size'] }} portrait;
            margin: 10mm;
        }

        html, body {
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
            font-family: 'Arial', sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.3;
            color: #000;
            background: white;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: calc({{ $currentPaper['containerWidth'] }} - 20mm);
            padding: 0 10mm;
            margin: 0 auto;
            box-sizing: border-box;
            min-height: calc({{ $currentPaper['height'] }} - 20mm);
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .document-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            margin-bottom: 15px;
            padding: 0;
            border: none;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .document-info .info-row {
            display: flex;
            padding: 2px 0;
        }

        .document-info .label {
            font-weight: normal;
            width: 140px;
            flex-shrink: 0;
        }

        .document-info .separator {
            margin: 0 8px;
            flex-shrink: 0;
        }

        .document-info .value {
            flex: 1;
            font-weight: normal;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: {{ $currentPaper['tableFont'] }};
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #333;
            padding: 5px;
            vertical-align: top;
        }

        .detail-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .detail-table td.center { text-align: center; }
        .detail-table td.right { text-align: right; }

        .summary-box {
            margin-top: 10px;
            padding: 0;
            background-color: transparent;
            border: none;
            width: 100%;
            display: flex;
            justify-content: flex-end;
        }

        .summary-table {
            width: 50%;
            border-collapse: collapse;
        }
        
        .summary-table td {
            padding: 3px 0;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .summary-table .label {
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
        }

        .summary-table .value {
            font-weight: bold;
            text-align: right;
            width: 120px;
        }

        .summary-table .total-row .label,
        .summary-table .total-row .value {
            border-top: 2px solid #000;
            padding-top: 5px;
            padding-bottom: 5px;
            font-size: calc({{ $currentPaper['fontSize'] }} + 1px);
        }

        .footer {
            margin-top: 20px;
            clear: both;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
            margin-bottom: 20px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box .title {
            font-weight: normal;
            margin-bottom: 60px;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .signature-box .line {
            border-top: 1px solid #000;
            margin: 0 auto;
            width: 80%;
            margin-bottom: 3px;
        }

        .signature-box .name {
            font-weight: normal;
            padding-top: 0;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .notes {
            margin-top: 10px;
            padding: 8px;
            border: 1px dashed #999;
            font-size: {{ $currentPaper['tableFont'] }};
            background-color: #fdfdfd;
        }

        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            
            /* Ensure page breaks don't cut rows */
            tr { page-break-inside: avoid; }
        }

        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .container-chip {
            display: inline-block;
            background: #eee;
            padding: 1px 4px;
            border-radius: 3px;
            margin: 1px;
            font-size: {{ $currentPaper['tableFont'] }};
            border: 1px solid #ddd;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <select id="paperSizeSelect" onchange="changePaperSize()" style="padding: 5px; margin-bottom: 5px; width: 100%;">
            <option value="Half Folio" {{ $paperSize == 'Half Folio' ? 'selected' : '' }}>Setengah Folio</option>
            <option value="Folio" {{ $paperSize == 'Folio' ? 'selected' : '' }}>Folio</option>
            <option value="A4" {{ $paperSize == 'A4' ? 'selected' : '' }}>A4</option>
        </select>
        <button onclick="window.print()" style="width: 100%; padding: 5px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;">🖨️ Cetak</button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PERMOHONAN BIAYA TRUCKING</h1>
        </div>

        <!-- Document Info -->
        <div class="document-info">
            <div>
                <div class="info-row">
                    <span class="label">Tanggal</span>
                    <span class="separator">:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Nomor</span>
                    <span class="separator">:</span>
                    <span class="value"><strong>{{ $biayaKapal->nomor_invoice }}</strong></span>
                </div>
                @if($biayaKapal->nomor_referensi)
                <div class="info-row">
                    <span class="label">Nomor Referensi</span>
                    <span class="separator">:</span>
                    <span class="value">{{ $biayaKapal->nomor_referensi }}</span>
                </div>
                @else
                <div class="info-row">
                    <span class="label">Nomor Referensi</span>
                    <span class="separator">:</span>
                    <span class="value">-</span>
                </div>
                @endif
            </div>
            <div>
                <div class="info-row">
                    <span class="label">Vendor</span>
                    <span class="separator">:</span>
                    <span class="value">
                        @php
                            $vendors = $biayaKapal->truckingDetails->pluck('nama_vendor')->unique()->values();
                        @endphp
                        {{ $vendors->count() > 0 ? $vendors->join(', ') : '-' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Kapal</span>
                    <span class="separator">:</span>
                    <span class="value">
                        @php
                            $kapals = $biayaKapal->truckingDetails->pluck('kapal')->unique()->values();
                        @endphp
                        {{ $kapals->count() > 0 ? $kapals->join(', ') : '-' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 45%;">Kapal / Voyage</th>
                    <th style="width: 30%;">Vendor</th>
                    <th style="width: 20%;">Biaya</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $grandTotalSubtotal = 0; 
                    $grandTotalPph = 0;
                    $grandTotalFinal = 0;
                @endphp
                
                @if($biayaKapal->truckingDetails->count() > 0)
                    @foreach($biayaKapal->truckingDetails as $index => $detail)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $detail->kapal }}</strong><br>
                            <span style="color: #555;">Voy: {{ $detail->voyage }}</span>
                        </td>
                        <td>{{ $detail->nama_vendor }}</td>

                        <td class="right">
                            <div style="font-weight: bold;">Rp {{ number_format($detail->total_biaya, 0, ',', '.') }}</div>
                            
                            @if($detail->pph > 0)
                            <div style="margin-top: 4px; border-top: 1px dotted #ccc; padding-top: 2px; font-size: 0.9em; color: #666;">
                                Subtotal: {{ number_format($detail->subtotal, 0, ',', '.') }}<br>
                                PPh: {{ number_format($detail->pph, 0, ',', '.') }}
                            </div>
                            @endif
                        </td>
                    </tr>
                    @php 
                        $grandTotalSubtotal += $detail->subtotal;
                        $grandTotalPph += $detail->pph;
                        $grandTotalFinal += $detail->total_biaya;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="center">Tidak ada detail trucking.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-box">
            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">Rp {{ number_format($grandTotalSubtotal, 0, ',', '.') }}</td>
                </tr>
                @if($grandTotalPph > 0)
                <tr>
                    <td class="label">Total PPh (2%):</td>
                    <td class="value" style="color: #d00;">(Rp {{ number_format($grandTotalPph, 0, ',', '.') }})</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label">TOTAL BIAYA:</td>
                    <td class="value">Rp {{ number_format($grandTotalFinal, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="signatures">
                <div class="signature-box">
                    <div class="title">Dibuat Oleh</div>
                    <div class="line"></div>
                    <div class="name"></div>
                </div>
                <div class="signature-box">
                    <div class="title">Diperiksa Oleh</div>
                    <div class="line"></div>
                    <div class="name"></div>
                </div>
                <div class="signature-box">
                    <div class="title">Disetujui Oleh</div>
                    <div class="line"></div>
                    <div class="name"></div>
                </div>
            </div>

            @if($biayaKapal->keterangan)
            <div class="notes">
                <strong>Catatan:</strong><br>
                {!! nl2br(e($biayaKapal->keterangan)) !!}
            </div>
            @endif
        </div>
    </div>

    <script>
        function changePaperSize() {
            const paperSize = document.getElementById('paperSizeSelect').value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('paper_size', paperSize);
            window.location.href = currentUrl.toString();
        }
    </script>
</body>
</html>
