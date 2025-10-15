<!DOCTYPE html>
<html lang="id">
@php
    // Get paper size from request or default to Half-A4
    $paperSize = request('paper_size', 'Half-A4');

    // Define paper dimensions and styles
    $paperMap = [
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '210mm',
            'fontSize' => '11px',
            'headerH1' => '18px',
            'tableFont' => '9px',
            'signatureBottom' => '15mm'
        ],
        'Folio' => [
            'size' => '8.5in 13in',
            'width' => '8.5in',
            'height' => '13in',
            'containerWidth' => '8.5in',
            'fontSize' => '12px',
            'headerH1' => '20px',
            'tableFont' => '10px',
            'signatureBottom' => '20mm'
        ],
        'Half-A4' => [
            'size' => '210mm 148.5mm', // A4 width x half height (A5 landscape)
            'width' => '210mm',
            'height' => '148.5mm',
            'containerWidth' => '210mm',
            'fontSize' => '9px',
            'headerH1' => '14px',
            'tableFont' => '7px',
            'signatureBottom' => '5mm'
        ],
        'Half-Folio' => [
            'size' => '8.5in 6.5in', // Folio width x half height
            'width' => '8.5in',
            'height' => '6.5in',
            'containerWidth' => '8.5in',
            'fontSize' => '9px',
            'headerH1' => '14px',
            'tableFont' => '7px',
            'signatureBottom' => '5mm'
        ]
    ];

    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-A4'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $paperSize === 'Half-A4' ? '210mm' : ($paperSize === 'Half-Folio' ? '8.5in' : ($paperSize === 'A4' ? '210mm' : '8.5in')) }}, initial-scale=1.0">
    <title>Pranota {{ $pranota->no_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: {{ $paperSize === 'Half-A4' ? '210mm 148.5mm' : ($paperSize === 'Half-Folio' ? '8.5in 6.5in' : ($paperSize === 'A4' ? 'A4' : '8.5in 13in')) }} portrait;
            margin: 0;
        }

        html {
            width: {{ $paperSize === 'Half-A4' ? '210mm' : ($paperSize === 'Half-Folio' ? '8.5in' : ($paperSize === 'A4' ? '210mm' : '8.5in')) }};
            height: {{ $paperSize === 'Half-A4' ? '148.5mm' : ($paperSize === 'Half-Folio' ? '6.5in' : ($paperSize === 'A4' ? '297mm' : '13in')) }};
        }

        body {
            font-family: Arial, sans-serif;
            font-size: {{ $paperSize === 'Half-A4' ? '9px' : ($paperSize === 'Half-Folio' ? '9px' : ($paperSize === 'A4' ? '11px' : '12px')) }};
            line-height: 1.4;
            color: #333;
            background: white;
            position: relative;
            width: {{ $paperSize === 'Half-A4' ? '210mm' : ($paperSize === 'Half-Folio' ? '8.5in' : ($paperSize === 'A4' ? '210mm' : '8.5in')) }};
            height: {{ $paperSize === 'Half-A4' ? '148.5mm' : ($paperSize === 'Half-Folio' ? '6.5in' : ($paperSize === 'A4' ? '297mm' : '13in')) }};
            margin: 0;
            padding: 0;
        }

        .container {
            width: {{ $paperSize === 'Half-A4' ? '210mm' : ($paperSize === 'Half-Folio' ? '8.5in' : ($paperSize === 'A4' ? '210mm' : '8.5in')) }};
            max-width: {{ $paperSize === 'Half-A4' ? '210mm' : ($paperSize === 'Half-Folio' ? '8.5in' : ($paperSize === 'A4' ? '210mm' : '8.5in')) }};
            height: {{ $paperSize === 'Half-A4' ? '148.5mm' : ($paperSize === 'Half-Folio' ? '6.5in' : ($paperSize === 'A4' ? '297mm' : '13in')) }};
            max-height: {{ $paperSize === 'Half-A4' ? '148.5mm' : ($paperSize === 'Half-Folio' ? '6.5in' : ($paperSize === 'A4' ? '297mm' : '13in')) }};
            margin: 0 auto;
            padding: 5mm;
            position: relative;
            padding-bottom: {{ $paperSize === 'Half-A4' ? '60px' : ($paperSize === 'Half-Folio' ? '60px' : ($paperSize === 'A4' ? '120px' : '150px')) }};
            box-sizing: border-box;
            overflow: hidden;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: {{ $paperSize === 'Half-A4' ? '14px' : ($paperSize === 'Half-Folio' ? '14px' : ($paperSize === 'A4' ? '18px' : '20px')) }};
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-left, .info-right {
            width: 48%;
        }

        .info-item {
            margin-bottom: 4px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #ffffff;
            color: #000000;
            border: 1px solid #000000;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
        }

        .table th,
        .table td {
            border: 1px solid #333;
            padding: 4px 2px;
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            font-size: {{ $paperSize === 'Half-A4' ? '8px' : ($paperSize === 'Half-Folio' ? '8px' : ($paperSize === 'A4' ? '10px' : '11px')) }};
            text-align: center;
            white-space: nowrap;
            border: 2px solid #333;
        }

        .table td {
            font-size: {{ $paperSize === 'Half-A4' ? '8px' : ($paperSize === 'Half-Folio' ? '8px' : ($paperSize === 'A4' ? '10px' : '11px')) }};
        }

        .table .text-right {
            text-align: right;
        }

        .table .text-center {
            text-align: center;
        }

        .table .col-masa {
            text-align: center;
            font-weight: 500;
            min-width: 80px;
        }

        .table .col-periode {
            text-align: center;
            min-width: 60px;
        }

        .table .col-tarif {
            text-align: center;
            min-width: 80px;
        }

        .table .col-vendor {
            max-width: 120px;
            word-wrap: break-word;
            white-space: normal;
        }

        .table .col-nomor {
            text-align: left;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            font-weight: 500;
            padding-left: 8px;
        }

        .masa-display {
            display: inline-block;
            padding: 0;
            background-color: transparent;
            border: none;
            font-weight: 500;
            font-size: 10px;
        }

        .masa-display small {
            font-size: 8px;
            color: #6b7280;
            margin-left: 2px;
        }

        .tarif-harian {
            background-color: #ffffff;
            color: #000000;
            border-color: #000000;
        }

        .tarif-bulanan {
            background-color: #ffffff;
            color: #000000;
            border-color: #000000;
        }

        /* Responsive table adjustments */
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .table tr:hover {
            background-color: #e9ecef;
        }

        .total-row td {
            background-color: #e9ecef !important;
            color: #333 !important;
            font-weight: bold !important;
            border: 2px solid #333 !important;
        }

        .total-row td.text-right {
            text-align: right !important;
        }

        .total-row td.text-center {
            text-align: center !important;
        }

        .summary {
            margin-top: 10px;
            text-align: right;
        }

        .summary-item {
            margin-bottom: 3px;
        }

        .summary-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .total-amount {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            border-top: 2px solid #000000;
            padding-top: 5px;
            margin-top: 5px;
        }

        /* Signature section for screen preview */
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

        .signature-line {
            border-bottom: 2px solid #333;
            margin-bottom: {{ $paperSize === 'Half-Folio' ? '5px' : '8px' }};
            height: 2px;
            width: {{ $paperSize === 'Half-A4' ? '100px' : ($paperSize === 'Half-Folio' ? '100px' : '150px') }};
            margin-left: auto;
            margin-right: auto;
        }

        .signature-name {
            font-size: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : '11px') }};
            margin-bottom: 5px;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html {
                width: 210mm;
                height: 297mm;
            }

            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                font-size: {{ $paperSize === 'Half-A4' ? '9px' : ($paperSize === 'Half-Folio' ? '9px' : ($paperSize === 'A4' ? '11px' : '12px')) }};
                color: #000;
                position: relative;
                overflow: visible;
            }

            .container {
                width: 210mm;
                @if($paperSize === 'Half-A4')
                    /* Half-A4: Scale content to fit in half page */
                    height: 148.5mm;
                    max-height: 148.5mm;
                    border-bottom: 2px dashed #999;
                    /* Visual guide for cutting line */
                @elseif($paperSize === 'Half-Folio')
                    /* Half-Folio: Scale content to fit in half page */
                    height: 6.5in;
                    max-height: 6.5in;
                    border-bottom: 2px dashed #999;
                    /* Visual guide for cutting line */
                @else
                    height: 287mm;
                    max-height: 287mm;
                @endif
                padding: 5mm;
                padding-bottom: {{ $paperSize === 'Half-A4' ? '40px' : ($paperSize === 'Half-Folio' ? '40px' : ($paperSize === 'A4' ? '120px' : '150px')) }};
                margin: 0;
                box-sizing: border-box;
                overflow: hidden;
                position: relative;
                page-break-after: avoid;
            }

            .header {
                margin-bottom: 10px;
                padding-bottom: 8px;
            }

            .header h1 {
                font-size: {{ $paperSize === 'A4' ? '16px' : ($paperSize === 'Folio' ? '20px' : '14px') }};
                margin-bottom: {{ $paperSize === 'Half-A4' ? '2px' : ($paperSize === 'Half-Folio' ? '2px' : '3px') }};
            }

            .header h2 {
                font-size: {{ $paperSize === 'A4' ? '12px' : ($paperSize === 'Folio' ? '16px' : '10px') }};
                margin-bottom: {{ $paperSize === 'Half-A4' ? '3px' : ($paperSize === 'Half-Folio' ? '3px' : '5px') }};
            }

            .header div strong {
                font-size: {{ $paperSize === 'A4' ? '11px' : ($paperSize === 'Folio' ? '12px' : '10px') }};
            }

            .header div span {
                font-size: {{ $paperSize === 'A4' ? '9px' : ($paperSize === 'Folio' ? '10px' : '8px') }};
            }

            /* Header layout for print */
            .header > div:first-child {
                display: flex !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
            }

            .info-section {
                margin-bottom: 10px;
                font-size: 9px;
            }

            .info-label {
                width: 80px;
                font-size: 9px;
            }

            .no-print {
                display: none;
            }

            .table {
                page-break-inside: avoid;
                margin-bottom: {{ $paperSize === 'Half-A4' ? '5px' : ($paperSize === 'Half-Folio' ? '5px' : '10px') }};
                width: 100%;
                font-size: {{ $paperSize === 'Half-A4' ? '7px' : ($paperSize === 'Half-Folio' ? '7px' : ($paperSize === 'A4' ? '9px' : '10px')) }};
            }

            .table th,
            .table td {
                padding: {{ $paperSize === 'Half-A4' ? '1px' : ($paperSize === 'Half-Folio' ? '1px' : '2px 1px') }};
                font-size: {{ $paperSize === 'Half-A4' ? '7px' : ($paperSize === 'Half-Folio' ? '7px' : ($paperSize === 'A4' ? '9px' : '10px')) }};
                border: 1px solid #000;
                word-wrap: break-word;
            }

            .table th {
                font-size: {{ $paperSize === 'Half-A4' ? '7px' : ($paperSize === 'Half-Folio' ? '7px' : ($paperSize === 'A4' ? '9px' : '10px')) }};
                background-color: #f8f9fa !important;
                color: #333 !important;
                border: 2px solid #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Specific column widths for better spacing */
            .table th:nth-child(1) { width: 4%; }  /* No */
            .table th:nth-child(2) { width: 22%; } /* No. Kontainer */
            .table th:nth-child(3) { width: 8%; }  /* Size */
            .table th:nth-child(4) { width: 15%; } /* Masa */
            .table th:nth-child(5) { width: 12%; } /* DPP */
            .table th:nth-child(6) { width: 12%; } /* Adjustment */
            .table th:nth-child(7) { width: 10%; } /* PPN */
            .table th:nth-child(8) { width: 10%; } /* PPH */
            .table th:nth-child(9) { width: 15%; } /* Grand Total */

            .table td:nth-child(1) { width: 4%; text-align: center; }
            .table td:nth-child(2) { width: 22%; }
            .table td:nth-child(3) { width: 8%; text-align: center; }
            .table td:nth-child(4) { width: 15%; }
            .table td:nth-child(5) { width: 12%; text-align: right; }
            .table td:nth-child(6) { width: 12%; text-align: right; }
            .table td:nth-child(7) { width: 10%; text-align: right; }
            .table td:nth-child(8) { width: 10%; text-align: right; }
            .table td:nth-child(9) { width: 15%; text-align: right; }

            .masa-display {
                padding: 0;
                font-size: 8px;
                line-height: 1.1;
            }

            .masa-display small {
                font-size: 6px;
            }

            .col-vendor {
                max-width: none;
                font-size: 8px;
            }

            .col-nomor {
                font-size: 8px;
                font-weight: 500;
            }

            .summary {
                margin-top: 5px;
                font-size: 9px;
            }

            .summary-label {
                width: 80px;
            }

            .total-amount {
                font-size: 10px;
                padding-top: 3px;
                margin-top: 3px;
            }

            .signature-section {
                margin-top: auto;
                page-break-inside: avoid;
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
                left: 0;
                right: 0;
                width: 100%;
            }

            .signature-table {
                margin-top: 5px;
            }

            .signature-cell {
                padding: 5px 3px;
            }

            .signature-label {
                margin-bottom: 15px;
                font-size: 8px;
            }

            .signature-line {
                margin-bottom: 3px;
                border-bottom: 2px solid #000 !important;
                width: 80px;
                margin-left: auto;
                margin-right: auto;
            }

            .signature-name {
                font-size: 8px;
                margin-bottom: 3px;
                font-weight: bold;
            }

            .footer {
                margin-top: 20px;
                padding-top: 10px;
                font-size: 8px;
            }

            .status-badge {
                font-size: 8px;
                padding: 2px 8px;
            }

            /* Force table layout and prevent column collapsing */
            .table {
                table-layout: fixed;
            }

            /* Total row styling for print */
            .table tr:last-child td {
                background-color: #e9ecef !important;
                color: #333 !important;
                font-weight: bold !important;
                border: 2px solid #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table tr:last-child td.text-right {
                text-align: right !important;
            }

            .table tr:last-child td.text-center {
                text-align: center !important;
            }

            /* Merged cell styling for total */
            .table tr:last-child td[colspan] {
                text-align: center !important;
                font-weight: bold !important;
            }

            /* Keterangan table for print */
            .keterangan-table {
                page-break-inside: avoid;
                margin-top: 10px;
                margin-bottom: 10px;
            }

            .keterangan-table th {
                background-color: #f8f9fa !important;
                color: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                border: 1px solid #000 !important;
            }

            .keterangan-table td {
                border: 1px solid #000 !important;
                min-height: 40px;
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
                        � Set Paper Size: <strong>A4</strong> dan Scale: <strong>100%</strong>
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
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                <div style="text-align: left;">
                    <strong style="font-size: 12px;">PT. ALEXINDO YAKINPRIMA</strong><br>
                    <span style="font-size: 10px;">Jalan Pluit Raya No.8 Blok B No.12</span><br>
                    <span style="font-size: 10px; font-weight: bold; margin-top: 5px; display: inline-block;">
                        Tanggal: {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d-M-Y') }}
                    </span>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 10px; font-weight: bold;">
                        No. Pranota: {{ $pranota->no_invoice }}
                    </span>
                </div>
            </div>
            <h1>PRANOTA TAGIHAN KONTAINER</h1>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-left">
                @php
                    $vendorList = $tagihanItems->pluck('vendor')->unique()->filter()->values();
                @endphp
                @if($vendorList->isNotEmpty())
                <div class="info-item">
                    <span class="info-label">Vendor:</span>
                    <span>{{ $vendorList->implode(', ') }}</span>
                </div>
                @endif
                @if($pranota->no_invoice_vendor)
                <div class="info-item">
                    <span class="info-label">Invoice Vendor:</span>
                    <span>{{ $pranota->no_invoice_vendor }}</span>
                </div>
                @endif
            </div>
            <div class="info-right">
                <!-- Info right can be used for other information if needed -->
            </div>
        </div>

        <!-- Table -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 22%;">No. Kontainer</th>
                    <th style="width: 8%;">Size</th>
                    <th style="width: 15%;">Masa</th>
                    <th style="width: 12%;">DPP</th>
                    <th style="width: 12%;">Adjustment</th>
                    <th style="width: 10%;">PPN</th>
                    <th style="width: 10%;">PPH</th>
                    <th style="width: 15%;">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihanItems as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="col-nomor">{{ $item->nomor_kontainer }}</td>
                    <td class="text-center">{{ $item->size }}</td>
                    <td class="col-masa">
                        @if($item->masa)
                            <span class="masa-display">
                                @php
                                    // Check if masa contains date range format like "22 Jan 2025 - 20 Feb 2025"
                                    if(strpos($item->masa, ' - ') !== false) {
                                        $dates = explode(' - ', $item->masa);
                                        if(count($dates) == 2) {
                                            try {
                                                $startDate = \Carbon\Carbon::parse($dates[0])->format('d-M-y');
                                                $endDate = \Carbon\Carbon::parse($dates[1])->format('d-M-y');
                                                echo $startDate . ' - ' . $endDate;
                                            } catch (Exception $e) {
                                                echo $item->masa;
                                            }
                                        } else {
                                            echo $item->masa;
                                        }
                                    } else {
                                        echo $item->masa;
                                        if(strpos($item->masa, 'bulan') === false && strpos($item->masa, 'hari') === false && is_numeric($item->masa)) {
                                            echo '<small> hari</small>';
                                        }
                                    }
                                @endphp
                            </span>
                        @else
                            <span class="masa-display">-</span>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->dpp ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->adjustment ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->ppn ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->pph ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->grand_total ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada tagihan ditemukan</td>
                </tr>
                @endforelse
                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="4" class="text-center" style="font-weight: bold; text-align: center;">TOTAL</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('dpp'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('adjustment'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('ppn'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('pph'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('grand_total'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-item total-amount" style="margin-top: 10px;">
                <span class="summary-label">TOTAL AMOUNT:</span>
                <span>Rp {{ number_format((float)$pranota->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Keterangan Table -->
        <div class="keterangan-table" style="margin-top: 8px; margin-bottom: 8px;">
            <table style="width: 100%; border-collapse: collapse; border: 2px solid #333;">
                <thead>
                    <tr>
                        <th style="background-color: #f8f9fa; color: #333; font-weight: bold; font-size: 10px; text-align: center; padding: 4px; border: 1px solid #333;">
                            KETERANGAN
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 6px; border: 1px solid #333; font-size: 10px; min-height: 30px; vertical-align: top;">
                            {{ $pranota->keterangan ?: 'Tidak ada keterangan khusus' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td class="signature-cell">
                        <div class="signature-label">Dibuat Oleh</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">&nbsp;</div>
                    </td>
                    <td class="signature-cell">
                        <div class="signature-label">Disetujui Oleh</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">&nbsp;</div>
                    </td>
                    <td class="signature-cell">
                        <div class="signature-label">Diterima Oleh</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">&nbsp;</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Print Script -->
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
