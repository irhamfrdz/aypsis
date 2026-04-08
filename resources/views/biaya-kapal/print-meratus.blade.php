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
            'fontSize' => '13px',
            'headerH1' => '20px',
            'tableFont' => '12px',
        ],
        'Folio' => [
            'size' => '215.9mm 330.2mm',
            'width' => '215.9mm',
            'height' => '330.2mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '15px',
            'headerH1' => '24px',
            'tableFont' => '13px',
        ],
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '210mm',
            'fontSize' => '15px',
            'headerH1' => '24px',
            'tableFont' => '13px',
        ],
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half Folio'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Biaya Meratus - {{ $biayaKapal->nomor_invoice }}</title>
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
            font-weight: bold;
            line-height: 1.4;
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
            margin-bottom: 8px;
            border-bottom: 2px double #000;
            padding-bottom: 6px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header p {
            font-size: {{ $currentPaper['fontSize'] }};
            color: #000;
            margin: 1px 0;
        }

        .document-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 6px 8px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: transparent;
        }

        .info-item {
            display: flex;
            gap: 10px;
        }

        .info-label {
            font-weight: 600;
            min-width: 90px;
        }

        .info-value {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        th, td {
            border: 1px solid #333;
            padding: 4px;
            text-align: left;
            font-size: {{ $currentPaper['tableFont'] }};
        }

        th {
            background-color: #000;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-box {
            margin-top: 8px;
            padding: 0;
            background-color: transparent;
            border: none;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: {{ $currentPaper['fontSize'] }};
            padding: 2px 0;
        }

        .summary-row.total {
            border-top: 2px solid #333;
            padding-top: 4px;
            margin-top: 4px;
            font-weight: bold;
            font-size: calc({{ $currentPaper['fontSize'] }} + 2px);
        }

        .summary-row .label {
            font-weight: 600;
        }

        .summary-row .value {
            font-weight: bold;
            text-align: right;
            margin-left: 20px;
        }

        .footer {
            margin-top: 35px;
            clear: both;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box .title {
            font-weight: bold;
            margin-bottom: 85px;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .signature-box .name {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            display: inline-block;
            min-width: 140px;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .print-controls button {
            background-color: #0066cc;
            color: white;
            border: none;
            padding: 8px 15px;
            font-size: 12px;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-size: 12px; margin-bottom: 5px; color: #666;">Ukuran Kertas:</label>
            <select id="paper_size_select" onchange="window.location.href = window.location.pathname + '?paper_size=' + this.value" style="width: 100%; padding: 5px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                <option value="Half Folio" {{ $paperSize == 'Half Folio' ? 'selected' : '' }}>Half Folio (Default)</option>
                <option value="Folio" {{ $paperSize == 'Folio' ? 'selected' : '' }}>Folio</option>
                <option value="A4" {{ $paperSize == 'A4' ? 'selected' : '' }}>A4</option>
            </select>
        </div>
        <button onclick="window.print()">Cetak Dokumen</button>
        <button onclick="window.history.back()" style="background-color: #666; margin-top: 10px; width: 100%;">Kembali</button>
    </div>

    <div class="container">
        <div class="header">
            <h1>BUKTI PENGELUARAN BIAYA MERATUS</h1>
            <p>PT. AYP LOGISTICS INDONESIA</p>
        </div>

        <div class="document-info">
            <div class="info-item">
                <span class="info-label">NO. INVOICE:</span>
                <span class="info-value">{{ $biayaKapal->nomor_invoice }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">TANGGAL:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($biayaKapal->created_at)->format('d/m/Y') }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>KAPAL / VOYAGE</th>
                    <th>JENIS BIAYA</th>
                    <th>DETAIL</th>
                    <th>QTY</th>
                    <th>HARGA</th>
                    <th>SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalSubtotal = 0;
                    $totalPph = 0;
                    $totalMaterai = 0;
                @endphp
                @foreach($meratusDetails as $index => $detail)
                    @php
                        $totalSubtotal += $detail->sub_total;
                        $totalPph += $detail->pph;
                        $totalMaterai += $detail->biaya_materai;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            {{ $detail->kapal }}<br>
                            <small>{{ $detail->voyage }}</small>
                        </td>
                        <td>
                            {{ $detail->jenis_biaya }}
                        </td>
                        <td>
                            @if($detail->lokasi) {{ $detail->lokasi }} @endif
                            @if($detail->size) ({{ $detail->size }}) @endif
                            @if($detail->keterangan) <br><small>{{ $detail->keterangan }}</small> @endif
                        </td>
                        <td class="text-center">{{ number_format($detail->kuantitas, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-box">
            <div class="summary-row">
                <span class="label">SUB TOTAL:</span>
                <span class="value">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">PPH:</span>
                <span class="value">Rp {{ number_format($totalPph, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">BIAYA MATERAI:</span>
                <span class="value">Rp {{ number_format($totalMaterai, 0, ',', '.') }}</span>
            </div>
            @php
                $totalGrandTotal = $totalSubtotal - $totalPph + $totalMaterai;
            @endphp
            <div class="summary-row total">
                <span class="label">GRAND TOTAL:</span>
                <span class="value">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer">
            <div class="signatures">
                <div class="signature-box">
                    <div class="title">Disetujui Oleh,</div>
                    <div class="name">OPERATIONAL MANAGER</div>
                </div>
                <div class="signature-box">
                    <div class="title">Diperiksa Oleh,</div>
                    <div class="name">FINANCE / ACCOUNTING</div>
                </div>
                <div class="signature-box">
                    <div class="title">Dibuat Oleh,</div>
                    <div class="name">STAFF OPERATIONAL</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
