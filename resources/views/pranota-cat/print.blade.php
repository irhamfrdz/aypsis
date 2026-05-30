<!DOCTYPE html>
<html lang="id">
@php
    // Fixed paper size: Half-Folio only
    $paperSize = 'Half-Folio';
    
    // Half-Folio paper dimensions and styles
    $currentPaper = [
        'size' => '8.5in 6.5in', // Folio width x half height
        'width' => '8.5in',
        'height' => '6.5in',
        'containerWidth' => '8.5in',
        'fontSize' => '9px',
        'headerH1' => '14px',
        'tableFont' => '8px',
        'signatureBottom' => '3mm'
    ];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>PRANOTA TAGIHAN CAT - {{ $pranota->no_invoice ?? 'Belum ada nomor' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: {{ $currentPaper['size'] }} portrait;
            margin: 0;
        }

        html {
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
        }

        body {
            font-family: Arial, sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.3;
            color: #333;
            background: white;
            position: relative;
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
            margin: 0;
            padding: 0;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body { margin: 0; padding: 0; }
            .container {
                border: none !important;
                box-shadow: none !important;
            }
        }

        .container {
            width: {{ $currentPaper['containerWidth'] }};
            min-height: {{ $currentPaper['height'] }};
            margin: 0 auto;
            padding: 5mm 8mm;
            position: relative;
            box-sizing: border-box;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 8px;
        }

        .info-table .label {
            width: 110px;
            font-weight: bold;
        }

        .info-table .separator {
            width: 15px;
            text-align: center;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
        }

        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            font-size: {{ $currentPaper['tableFont'] }};
            vertical-align: middle;
        }

        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .items-table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .keterangan-section {
            margin-top: 5px;
            padding: 5px;
            border: 1px dashed #ccc;
            background-color: #fdfdfd;
            font-size: 8px;
        }

        .footer-signatures {
            margin-top: 20px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding-top: 30px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 140px;
            margin: 0 auto 3px;
        }

        .signature-label {
            font-size: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Instruction Banner -->
    <div class="no-print" style="background: #fef3c7; padding: 10px; border: 1px solid #f59e0b; margin: 10px; font-size: 11px; border-radius: 5px;">
        <strong>⚠️ PENTING - Setting Print untuk Half-Folio:</strong><br>
        Setting Printer: Paper Size <b>Folio/Legal</b>, Scale: <b>100%</b>, Orientation: <b>Portrait</b>.<br>
        Potong kertas Folio menjadi 2 bagian secara horizontal setelah dicetak.
    </div>

    <!-- Print Button -->
    <div class="no-print" style="text-align: right; margin: 0 10px 10px 0;">
        <button onclick="window.print()" style="padding: 6px 15px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ Cetak Laporan
        </button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PRANOTA TAGIHAN CONTAINER ANNUAL TEST (CAT)</h1>
        </div>

        <!-- Info Section -->
        <table class="info-table">
            <tr>
                <td class="label">Nomor Pranota</td>
                <td class="separator">:</td>
                <td><strong>{{ $pranota->no_invoice ?? '-' }}</strong></td>
                <td class="label">Vendor/Bengkel</td>
                <td class="separator">:</td>
                <td>{{ $pranota->supplier ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pranota</td>
                <td class="separator">:</td>
                <td>{{ $pranota->tanggal_pranota ? \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d F Y') : '-' }}</td>
                <td class="label">Jumlah Tagihan</td>
                <td class="separator">:</td>
                <td>{{ $pranota->jumlah_tagihan ?? count($tagihanItems) }} Item</td>
            </tr>
            @if($pranota->due_date)
            <tr>
                <td class="label">Jatuh Tempo</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($pranota->due_date)->format('d F Y') }}</td>
                <td class="label"></td>
                <td class="separator"></td>
                <td></td>
            </tr>
            @endif
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 25%;">NOMOR TAGIHAN CAT</th>
                    <th style="width: 20%;">NOMOR KONTAINER</th>
                    <th style="width: 20%;">VENDOR</th>
                    <th style="width: 15%;">TANGGAL CAT</th>
                    <th style="width: 15%;">BIAYA</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; $grandTotal = 0; @endphp
                @forelse($tagihanItems as $item)
                    @php
                        $biaya = floatval($item->realisasi_biaya ?? 0);
                        $grandTotal += $biaya;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i++ }}</td>
                        <td class="text-center font-bold">{{ $item->nomor_tagihan_cat ?? $item->id }}</td>
                        <td class="text-center">{{ $item->nomor_kontainer ?? '-' }}</td>
                        <td>{{ $item->vendor ?? '-' }}</td>
                        <td class="text-center">{{ $item->tanggal_cat ? \Carbon\Carbon::parse($item->tanggal_cat)->format('d/m/Y') : '-' }}</td>
                        <td class="text-right font-bold">{{ number_format($biaya, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center" style="color: #999;">Tidak ada data item</td></tr>
                @endforelse
                @if(count($tagihanItems) > 0)
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                        <td colspan="5" class="text-right px-4">TOTAL</td>
                        <td class="text-right">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if($pranota->keterangan)
        <div class="keterangan-section">
            <strong>Catatan Tambahan:</strong> {{ $pranota->keterangan }}
        </div>
        @endif

        <div class="footer-signatures">
            <table class="signature-table">
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-label">Dibuat Oleh</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-label">Diperiksa Oleh</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-label">Disetujui Oleh</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
