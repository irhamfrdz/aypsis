<!DOCTYPE html>
<html lang="id">
@php
    // Get paper size from request or default to Half-Folio
    $paperSize = request('paper_size', 'Half-Folio');

    // Define paper dimensions and styles
    $paperMap = [
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '210mm',
            'fontSize' => '12px',
            'headerH1' => '18px',
            'tableFont' => '12px',
            'signatureBottom' => '15mm'
        ],
        'Folio' => [
            'size' => '8.5in 13in',
            'width' => '8.5in',
            'height' => '13in',
            'containerWidth' => '8.5in',
            'fontSize' => '13px',
            'headerH1' => '20px',
            'tableFont' => '13px',
            'signatureBottom' => '20mm'
        ],
        'Half-A4' => [
            'size' => '210mm 148.5mm', // A4 width x half height (A5 landscape)
            'width' => '210mm',
            'height' => '148.5mm',
            'containerWidth' => '210mm',
            'fontSize' => '10px',
            'headerH1' => '14px',
            'tableFont' => '10px',
            'signatureBottom' => '5mm'
        ],
        'Half-Folio' => [
            'size' => '8.5in 6.5in', // Folio width x half height
            'width' => '8.5in',
            'height' => '6.5in',
            'containerWidth' => '8.5in',
            'fontSize' => '11px',
            'headerH1' => '16px',
            'tableFont' => '11px',
            'signatureBottom' => '5mm'
        ]
    ];

    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-Folio'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Print Permohonan Transfer - {{ $pranotaUangJalan->nomor_pranota }}</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.4;
            color: #333;
            background: white;
            position: relative;
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
            margin: 0;
            padding: 0;
        }

        /* Hide print dialogs and overlays that interfere with printing */
        .print-dialog,
        .print-warning,
        .chrome-print-dialog,
        .firefox-print-dialog,
        .edge-print-dialog,
        div[role="dialog"],
        .modal,
        .overlay {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            z-index: -1 !important;
        }

        /* Ensure print content is always on top */
        .container {
            z-index: 9999 !important;
            position: relative !important;
        }

        /* Print-specific styles */
        @media print {
            body * {
                visibility: visible;
            }
            
            .container,
            .container * {
                visibility: visible;
            }
            
            /* Hide any potential warning overlays during print */
            .print-dialog,
            .print-warning,
            div[role="dialog"],
            .modal,
            .overlay,
            [data-testid*="dialog"],
            [class*="dialog"],
            [class*="modal"],
            [class*="overlay"],
            [class*="warning"] {
                display: none !important;
                visibility: hidden !important;
            }
        }

        .container {
            width: {{ $currentPaper['containerWidth'] }};
            max-width: {{ $currentPaper['containerWidth'] }};
            height: {{ $currentPaper['height'] }};
            max-height: {{ $currentPaper['height'] }};
            margin: 0 auto;
            padding: 5mm 1mm 5mm 5mm;
            position: relative;
            padding-bottom: {{ $paperSize === 'Half-A4' ? '60px' : ($paperSize === 'Half-Folio' ? '60px' : ($paperSize === 'A4' ? '120px' : '150px')) }};
            box-sizing: border-box;
            overflow: hidden;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: {{ $paperSize === 'Half-A4' ? '5px' : ($paperSize === 'Half-Folio' ? '5px' : '10px') }};
            margin-bottom: {{ $paperSize === 'Half-A4' ? '8px' : ($paperSize === 'Half-Folio' ? '8px' : '15px') }};
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: {{ $paperSize === 'Half-A4' ? '3px' : ($paperSize === 'Half-Folio' ? '3px' : '5px') }};
            text-transform: uppercase;
        }

        .header p {
            font-size: {{ $paperSize === 'Half-A4' ? '9px' : ($paperSize === 'Half-Folio' ? '9px' : '11px') }};
            color: #666;
        }

        .table-container {
            margin: {{ $paperSize === 'Half-A4' ? '10px 0' : ($paperSize === 'Half-Folio' ? '10px 0' : '20px 0') }};
        }

        .table-title {
            font-size: {{ $paperSize === 'Half-A4' ? '12px' : ($paperSize === 'Half-Folio' ? '12px' : '14px') }};
            font-weight: bold;
            margin-bottom: {{ $paperSize === 'Half-A4' ? '8px' : ($paperSize === 'Half-Folio' ? '8px' : '10px') }};
            color: #495057;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: {{ $paperSize === 'Half-A4' ? '8px' : ($paperSize === 'Half-Folio' ? '8px' : '15px') }};
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #333;
            padding: {{ $paperSize === 'Half-A4' ? '4px 3px' : ($paperSize === 'Half-Folio' ? '4px 3px' : '6px 4px') }};
            text-align: left;
            vertical-align: middle;
            font-size: {{ $currentPaper['tableFont'] }};
            word-wrap: break-word;
            line-height: 1.2;
        }

        th {
            background-color: #ffffff;
            font-weight: bold;
            text-align: center;
            border: 1px solid #333;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: 600;
        }

        .summary {
            margin-top: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : '20px') }};
            padding: {{ $paperSize === 'Half-A4' ? '8px' : ($paperSize === 'Half-Folio' ? '8px' : '15px') }};
            background-color: #f8f9fa;
            border: 1px solid #333;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: {{ $paperSize === 'Half-A4' ? '3px 0' : ($paperSize === 'Half-Folio' ? '3px 0' : '5px 0') }};
            font-size: {{ $paperSize === 'Half-A4' ? '9px' : ($paperSize === 'Half-Folio' ? '9px' : '11px') }};
        }

        .summary-row.total {
            font-weight: bold;
            font-size: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : '12px') }};
            border-top: 1px solid #333;
            padding-top: {{ $paperSize === 'Half-A4' ? '5px' : ($paperSize === 'Half-Folio' ? '5px' : '8px') }};
            margin-top: {{ $paperSize === 'Half-A4' ? '8px' : ($paperSize === 'Half-Folio' ? '8px' : '10px') }};
        }

        .signature-section {
            margin-top: auto;
            text-align: center;
            page-break-inside: avoid;
            position: absolute;
            bottom: {{ $paperSize === 'Half-A4' ? '20px' : ($paperSize === 'Half-Folio' ? '20px' : '40px') }};
            left: 0;
            right: 0;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : '15px') }};
        }

        .signature-cell {
            width: 33.33%;
            padding: {{ $paperSize === 'Half-A4' ? '8px 4px' : ($paperSize === 'Half-Folio' ? '8px 4px' : '12px 8px') }};
            text-align: center;
            vertical-align: top;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: {{ $paperSize === 'Half-A4' ? '20px' : ($paperSize === 'Half-Folio' ? '20px' : '30px') }};
            font-size: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : '11px') }};
        }

        .signature-name {
            font-size: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : '11px') }};
            margin-bottom: 5px;
            font-weight: bold;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .print-button:hover {
            background: #0056b3;
        }

        .no-print {
            display: block;
            visibility: visible;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-approved {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-unpaid {
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        @media print {
            @page {
                size: {{ $currentPaper['size'] }} portrait;
                margin: 0;
            }

            .no-print {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                height: 0 !important;
                overflow: hidden !important;
                position: absolute !important;
                left: -9999px !important;
            }

            /* Force hide any fixed/absolute positioned elements except container */
            body > div:not(.container) {
                display: none !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html {
                width: {{ $currentPaper['width'] }};
                height: {{ $currentPaper['height'] }};
            }

            body {
                width: {{ $currentPaper['width'] }};
                height: {{ $currentPaper['height'] }};
                margin: 0;
                padding: 0;
                font-size: {{ $currentPaper['fontSize'] }};
                color: #000;
                position: relative;
                overflow: visible;
            }

            .container {
                width: {{ $currentPaper['containerWidth'] }};
                @if($paperSize === 'Half-A4')
                    height: 148.5mm;
                    max-height: 148.5mm;
                    border-bottom: 2px dashed #999;
                @elseif($paperSize === 'Half-Folio')
                    height: 6.5in;
                    max-height: 6.5in;
                    border-bottom: 2px dashed #999;
                @else
                    height: 287mm;
                    max-height: 287mm;
                @endif
                padding: 5mm 1mm 5mm 5mm;
                padding-bottom: {{ $paperSize === 'Half-A4' ? '40px' : ($paperSize === 'Half-Folio' ? '40px' : ($paperSize === 'A4' ? '120px' : '150px')) }};
                margin: 0;
                box-sizing: border-box;
                overflow: hidden;
                position: relative;
                page-break-after: avoid;
            }

            .no-print {
                display: none !important;
            }

            .header h1 {
                font-size: {{ $paperSize === 'A4' ? '16px' : ($paperSize === 'Folio' ? '20px' : '14px') }};
            }

            table {
                page-break-inside: avoid;
                margin-bottom: {{ $paperSize === 'Half-A4' ? '5px' : ($paperSize === 'Half-Folio' ? '5px' : '10px') }};
                font-size: {{ $currentPaper['tableFont'] }};
            }

            th, td {
                padding: {{ $paperSize === 'Half-A4' ? '3px 2px' : ($paperSize === 'Half-Folio' ? '3px 2px' : '5px 3px') }};
                font-size: {{ $currentPaper['tableFont'] }};
                border: 1px solid #000;
                line-height: 1.2;
            }

            th {
                background-color: #ffffff !important;
                color: #000 !important;
                border: 1px solid #000 !important;
                font-weight: bold !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .signature-section {
                @if($paperSize === 'Half-A4')
                    position: absolute;
                    bottom: 10mm;
                @elseif($paperSize === 'Half-Folio')
                    position: absolute;
                    bottom: 10mm;
                @else
                    position: fixed;
                    bottom: {{ $paperSize === 'A4' ? '15mm' : '20mm' }};
                @endif
            }

            .summary {
                margin-top: 5px;
                font-size: {{ $paperSize === 'Half-A4' ? '8px' : ($paperSize === 'Half-Folio' ? '8px' : '10px') }};
                padding: {{ $paperSize === 'Half-A4' ? '5px' : ($paperSize === 'Half-Folio' ? '5px' : '10px') }};
            }
        }
    </style>
</head>
<body>
    <!-- Print Instructions Banner (hidden when printing) -->
    <div class="no-print" style="position: fixed; top: 10px; left: 50%; transform: translateX(-50%); background: #fef3c7; padding: 15px 25px; border: 2px solid #f59e0b; border-radius: 8px; z-index: 1001; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 650px;">
        <div style="display: flex; align-items: start; gap: 12px;">
            <svg style="width: 28px; height: 28px; color: #f59e0b; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div style="flex: 1;">
                <strong style="color: #92400e; display: block; margin-bottom: 6px; font-size: 14px;">⚠️ PENTING - Setting Print untuk {{ $paperSize }}:</strong>
                <div style="color: #78350f; font-size: 13px; line-height: 1.6;">
                    @if($paperSize === 'Half-A4')
                        <strong>Ukuran: Setengah A4 (210 x 148.5 mm)</strong><br>
                        📌 Saat Print Dialog:<br>
                        &nbsp;&nbsp;&nbsp;1️⃣ Scale: <strong>None / 100% / Actual Size</strong><br>
                        &nbsp;&nbsp;&nbsp;2️⃣ Orientation: <strong>Portrait (Tegak)</strong><br>
                        &nbsp;&nbsp;&nbsp;3️⃣ Paper: <strong>A4</strong><br>
                        &nbsp;&nbsp;&nbsp;4️⃣ Margins: <strong>None / Minimal</strong><br>
                        ✂️ Setelah print, potong kertas A4 menjadi 2 bagian (setengah horizontal)
                    @elseif($paperSize === 'Half-Folio')
                        <strong>Ukuran: Setengah Folio (8.5 x 6.5 inch)</strong><br>
                        📌 Saat Print Dialog:<br>
                        &nbsp;&nbsp;&nbsp;1️⃣ Scale: <strong>None / 100% / Actual Size</strong><br>
                        &nbsp;&nbsp;&nbsp;2️⃣ Orientation: <strong>Portrait (Tegak)</strong><br>
                        &nbsp;&nbsp;&nbsp;3️⃣ Paper: <strong>Legal/Folio</strong><br>
                        &nbsp;&nbsp;&nbsp;4️⃣ Margins: <strong>None / Minimal</strong><br>
                        ✂️ Setelah print, potong kertas Folio menjadi 2 bagian (setengah horizontal)
                    @elseif($paperSize === 'Folio')
                        <strong>Ukuran: Legal/Folio (8.5 x 13 inch)</strong><br>
                        📌 Set Paper Size: <strong>Legal</strong> dan Scale: <strong>100%</strong>
                    @else
                        <strong>Ukuran: A4 (210 x 297 mm)</strong><br>
                        📌 Set Paper Size: <strong>A4</strong> dan Scale: <strong>100%</strong>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Paper Size Selector (hidden when printing) -->
    <div class="no-print" style="position: fixed; top: 10px; right: 10px; background: white; padding: 10px; border: 1px solid #ccc; border-radius: 5px; z-index: 1000; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
        @include('components.paper-selector', ['selectedSize' => $paperSize])
        <div class="mt-2">
            <small class="text-gray-600">
                Current: {{ $paperSize }}
                @if($paperSize === 'Half-A4')
                    (210mm × 148.5mm)
                @elseif($paperSize === 'Half-Folio')
                    (8.5in × 6.5in)
                @elseif($paperSize === 'A4')
                    (210mm × 297mm)
                @else
                    (8.5in × 13in)
                @endif
            </small>
        </div>
        <!-- Print Button -->
        <button class="print-button" onclick="window.print()" style="width: 100%; margin-top: 10px; background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 12px;">
            🖨️ Cetak Permohonan
        </button>
    </div>

    <div class="container">

        <!-- Header -->
        <div class="header">
            <h1>Form Permohonan Transfer</h1>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; font-size: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : '12px') }};">
                <span><strong>No Pranota : {{ $pranotaUangJalan->nomor_pranota }}</strong></span>
                <span><strong>Tgl Uang Jalan : {{ $pranotaUangJalan->tanggal_pranota->format('d-m-Y') }}</strong></span>
            </div>
        </div>

        <!-- Uang Jalan Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 15%">NO SJ</th>
                        <th style="width: 15%">Barang</th>
                        <th style="width: 8%">NIK</th>
                        <th style="width: 12%">Supir</th>
                        <th style="width: 15%">Pengirim</th>
                        <th style="width: 12%">Tujuan</th>
                        <th style="width: 10%">Uang Jalan</th>
                        <th style="width: 8%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pranotaUangJalan->uangJalans as $index => $uangJalan)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">
                                @if($uangJalan->suratJalan)
                                    {{ $uangJalan->suratJalan->no_surat_jalan }}
                                @else
                                    {{ $uangJalan->nomor_uang_jalan }}
                                @endif
                            </td>
                            <td>
                                @if($uangJalan->suratJalan)
                                    {{ $uangJalan->suratJalan->jenis_barang ?? 'PRODUK MINUMAN' }}
                                @else
                                    PRODUK MINUMAN
                                @endif
                            </td>
                            <td class="text-center">
                                @if($uangJalan->suratJalan && $uangJalan->suratJalan->supir_nik)
                                    {{ $uangJalan->suratJalan->supir_nik }}
                                @elseif($uangJalan->suratJalan && $uangJalan->suratJalan->kenek_nik)
                                    {{ $uangJalan->suratJalan->kenek_nik }}
                                @else
                                    1280
                                @endif
                            </td>
                            <td>
                                @if($uangJalan->suratJalan)
                                    {{ $uangJalan->suratJalan->supir ?? 'Jokaria' }}
                                @else
                                    Jokaria
                                @endif
                            </td>
                            <td>
                                @if($uangJalan->suratJalan)
                                    {{ $uangJalan->suratJalan->pengirim ?? 'PT CS2 POLA SEHAT' }}
                                @else
                                    PT CS2 POLA SEHAT
                                @endif
                            </td>
                            <td>
                                @if($uangJalan->suratJalan)
                                    {{ $uangJalan->suratJalan->tujuan_pengambilan ?? 'TATUNG' }}
                                @else
                                    TATUNG
                                @endif
                            </td>
                            <td class="text-right">
                                {{ number_format($uangJalan->jumlah_total, 0, ',', '.') }}
                            </td>
                            <td class="text-right">
                                {{ number_format($uangJalan->jumlah_total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    
                    <!-- Penyesuaian Row -->
                    @if($pranotaUangJalan->penyesuaian != 0)
                    <tr>
                        <td colspan="7" style="text-align: center; font-weight: bold;">Penyesuaian</td>
                        <td class="text-right">{{ number_format($pranotaUangJalan->penyesuaian, 0, ',', '.') }}</td>
                        <td class="text-right">0</td>
                    </tr>
                    @endif
                    
                    <!-- Total Row -->
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td colspan="7" style="text-align: center;">Total</td>
                        <td class="text-right">{{ number_format($pranotaUangJalan->total_with_penyesuaian, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($pranotaUangJalan->total_with_penyesuaian, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>



        <!-- Signature Section -->
        <div class="signature-section" style="margin-top: {{ $paperSize === 'Half-A4' ? '30px' : ($paperSize === 'Half-Folio' ? '30px' : '50px') }};">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 10px;">
                        <div style="border-bottom: 1px dotted #333; margin-bottom: 40px; height: 1px;"></div>
                        <div style="font-size: {{ $paperSize === 'Half-A4' ? '9px' : ($paperSize === 'Half-Folio' ? '9px' : '11px') }};">
                            (Pemohon)
                        </div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 10px;">
                        <div style="border-bottom: 1px dotted #333; margin-bottom: 40px; height: 1px;"></div>
                        <div style="font-size: {{ $paperSize === 'Half-A4' ? '9px' : ($paperSize === 'Half-Folio' ? '9px' : '11px') }};">
                            (Pemeriksa)
                        </div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 10px;">
                        <div style="border-bottom: 1px dotted #333; margin-bottom: 40px; height: 1px;"></div>
                        <div style="font-size: {{ $paperSize === 'Half-A4' ? '9px' : ($paperSize === 'Half-Folio' ? '9px' : '11px') }};">
                            (Kasir)
                        </div>
                    </td>
                </tr>
            </table>
        </div>


    </div>

    <!-- Enhanced Print Script -->
    <script>
        // Hide elements only during actual printing
        function hideElementsForPrint() {
            const selectors = [
                '.no-print',
                '.print-dialog',
                '.print-warning', 
                'div[role="dialog"]',
                '.modal',
                '.overlay'
            ];
            
            selectors.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(el => {
                    if (!el.closest('.container')) {
                        el.style.display = 'none';
                        el.style.visibility = 'hidden';
                    }
                });
            });
        }

        // Restore elements after printing
        function showElementsAfterPrint() {
            const selectors = ['.no-print'];
            
            selectors.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(el => {
                    if (!el.closest('.container')) {
                        el.style.display = 'block';
                        el.style.visibility = 'visible';
                    }
                });
            });
        }

        // Enhanced print function
        function initiatePrint() {
            hideElementsForPrint();
            setTimeout(() => {
                window.print();
            }, 100);
        }

        // Hide elements only when print dialog opens
        window.addEventListener('beforeprint', hideElementsForPrint);
        
        // Show elements back when print dialog closes
        window.addEventListener('afterprint', showElementsAfterPrint);

        // Keyboard shortcut for manual print
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                initiatePrint();
            }
        });

        // Auto print when page loads (delayed to show UI first)
        window.addEventListener('load', function() {
            setTimeout(() => {
                // Uncomment line below if you want auto-print on load
                // initiatePrint();
            }, 300);
        });
    </script>
</body>
</html>