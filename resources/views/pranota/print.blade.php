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
        'Custom-215' => [
            'size' => '215mm 297mm', // Custom 21.5cm width
            'width' => '215mm',
            'height' => '297mm',
            'containerWidth' => '215mm',
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
        'Half-Custom-215' => [
            'size' => '215mm 148.5mm', // Custom 21.5cm width x half height
            'width' => '215mm',
            'height' => '148.5mm',
            'containerWidth' => '215mm',
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

    // Get unique invoices from tagihan items - define early for maxRowsPerPage calculation
    $invoices = $tagihanItems->map(function($item) {
        return $item->invoice;
    })->filter()->unique('id')->values();

    // Calculate maximum rows that can fit on one page (including headers, footers, etc.)
    $maxRowsPerPage = match($paperSize) {
        'Half-A4' => 20,     // Conservative but allows for headers/footers
        'Half-Custom-215' => 20,  // Same as Half-A4
        'Half-Folio' => 22,  // Slightly more space
        'A4' => 35,          // Full A4 has much more space
        'Custom-215' => 35,  // Same as A4
        'Folio' => ($invoices->isNotEmpty() ? 
            max(35, 55 - ($invoices->count() * 2)) : 55), // Much more space for Folio (8.5x13 in)
        default => 20
    };
    
    // Only create multiple pages if data actually exceeds the limit
    $totalItems = $tagihanItems->count();
    
    if ($totalItems <= $maxRowsPerPage) {
        // All data fits in one page
        $chunkedItems = collect([$tagihanItems]);
        $totalPages = 1;
    } else {
        // Split data across multiple pages
        $chunkedItems = $tagihanItems->chunk($maxRowsPerPage);
        $totalPages = $chunkedItems->count();
        
        // If last chunk has very few items (less than 5), merge with previous
        if ($totalPages > 1 && $chunkedItems->last()->count() < 5) {
            $lastChunk = $chunkedItems->pop();
            $secondLastChunk = $chunkedItems->pop();
            $mergedChunk = $secondLastChunk->merge($lastChunk);
            $chunkedItems->push($mergedChunk);
            $totalPages = $chunkedItems->count();
        }
    }
    
    $vendorList = $tagihanItems->pluck('vendor')->unique()->filter()->values();
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ in_array($paperSize, ['Half-A4', 'A4']) ? '210mm' : (in_array($paperSize, ['Half-Custom-215', 'Custom-215']) ? '215mm' : (in_array($paperSize, ['Half-Folio', 'Folio']) ? '8.5in' : '210mm')) }}, initial-scale=1.0">
    <title>Pranota {{ $pranota->no_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Remove any blue colors or default browser styling */
        input, select, button, a {
            color: inherit !important;
            outline: none !important;
            border-color: #ccc !important;
        }
        
        input:focus, select:focus {
            border-color: #999 !important;
            box-shadow: none !important;
        }

        @page {
            size: {{ in_array($paperSize, ['Half-A4']) ? '210mm 148.5mm' : (in_array($paperSize, ['Half-Custom-215']) ? '215mm 148.5mm' : (in_array($paperSize, ['Half-Folio']) ? '8.5in 6.5in' : (in_array($paperSize, ['A4']) ? 'A4' : (in_array($paperSize, ['Custom-215']) ? '215mm 297mm' : '8.5in 13in')))) }} portrait;
            margin: 0;
        }

        /* Prevent orphaned content and optimize page breaks */
        .page-container {
            position: relative;
            page-break-inside: avoid;
            min-height: {{ $paperSize === 'Folio' ? '200px' : '150px' }};
        }

        .page-container:last-child {
            page-break-after: avoid;
        }

        /* Prevent table rows from breaking across pages */
        .table tbody tr {
            page-break-inside: avoid;
        }

        /* Force new page when needed - only when content actually overflows */
        .force-new-page {
            page-break-before: always;
        }

        /* Let content flow naturally within page boundaries */
        .content-flow {
            page-break-inside: avoid;
        }

        html {
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
        }

        body {
            font-family: Arial, sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.2;
            color: #333;
            background: white;
            position: relative;
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
            margin: 0;
            padding: 0;
        }

        .container {
            width: {{ $currentPaper['containerWidth'] }};
            max-width: {{ $currentPaper['containerWidth'] }};
            height: {{ $currentPaper['height'] }};
            max-height: {{ $currentPaper['height'] }};
            margin: 0 auto;
            padding: 3mm 3mm 3mm 3mm;
            position: relative;
            padding-bottom: {{ $paperSize === 'Half-A4' ? '25px' : ($paperSize === 'Half-Folio' ? '25px' : ($paperSize === 'A4' ? '50px' : ($paperSize === 'Folio' ? '60px' : '70px'))) }};
            box-sizing: border-box;
            overflow: {{ $paperSize === 'Folio' ? 'visible' : 'hidden' }};
        }

        .header {
            text-align: center;
            margin-bottom: {{ $paperSize === 'Folio' ? '5px' : '6px' }};
            border-bottom: 2px solid #333;
            padding-bottom: {{ $paperSize === 'Folio' ? '4px' : '5px' }};
            display: block !important;
            visibility: visible !important;
            page-break-inside: avoid;
            page-break-after: avoid;
            opacity: 1 !important;
            position: relative !important;
            z-index: 999 !important;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
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
            margin-bottom: {{ $paperSize === 'Folio' ? '3px' : '5px' }};
        }

        .info-left, .info-right {
            width: 48%;
        }

        .info-item {
            margin-bottom: 2px;
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
            margin-bottom: 4px;
            table-layout: fixed;
        }

        .table th,
        .table td {
            border: 1px solid #333;
            padding: 2px 1px;
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            font-size: {{ $currentPaper['tableFont'] }};
            text-align: center;
            white-space: nowrap;
            border: 2px solid #333;
        }

        .table td {
            font-size: {{ $currentPaper['tableFont'] }};
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
            padding-right: 3px;
        }

        .summary-item {
            margin-bottom: 3px;
        }

        .summary-label {
            font-weight: bold;
            display: inline-block;
            width: 90px;
        }

        .total-amount {
            font-size: 10px;
            font-weight: bold;
            color: #000000;
            padding-top: 5px;
            margin-top: 5px;
        }

        /* Signature section for screen preview */
        .signature-section {
            margin-top: auto;
            text-align: center;
            page-break-inside: avoid;
            position: absolute;
            bottom: {{ in_array($paperSize, ['Half-A4', 'Half-Custom-215', 'Half-Folio']) ? '10px' : ($paperSize === 'Folio' ? '30px' : '20px') }};
            left: 0;
            right: 0;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: {{ in_array($paperSize, ['Half-A4', 'Half-Custom-215', 'Half-Folio']) ? '10px' : '15px' }};
        }

        .signature-cell {
            width: 33.33%;
            padding: {{ in_array($paperSize, ['Half-A4', 'Half-Custom-215', 'Half-Folio']) ? '8px 4px' : '12px 8px' }};
            text-align: center;
            vertical-align: top;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: {{ in_array($paperSize, ['Half-A4', 'Half-Custom-215', 'Half-Folio']) ? '15px' : '20px' }};
            font-size: {{ in_array($paperSize, ['Half-A4', 'Half-Custom-215', 'Half-Folio']) ? '10px' : '11px' }};
        }

        .signature-name {
            font-size: {{ in_array($paperSize, ['Half-A4', 'Half-Custom-215', 'Half-Folio']) ? '10px' : '11px' }};
            margin-bottom: 5px;
            font-weight: bold;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        @media print {
            @page {
                size: {{ $currentPaper['size'] }} portrait;
                margin: 0;
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
                min-height: {{ $currentPaper['height'] }};
                padding: 5mm 5mm 5mm 5mm;
                padding-bottom: {{ $paperSize === 'Half-A4' ? '40px' : ($paperSize === 'Half-Folio' ? '40px' : ($paperSize === 'A4' ? '120px' : ($paperSize === 'Folio' ? '60px' : '150px'))) }};
                margin: 0;
                box-sizing: border-box;
                position: relative;
                page-break-inside: avoid;
            }

            /* Multi-page layout - prevent unnecessary page breaks */
            .page-container {
                padding: 5mm 5mm 5mm 5mm;
                padding-bottom: {{ $paperSize === 'Half-A4' ? '20px' : ($paperSize === 'Half-Folio' ? '20px' : ($paperSize === 'A4' ? '60px' : ($paperSize === 'Folio' ? '30px' : '80px'))) }};
                margin: 0;
                box-sizing: border-box;
                position: relative;
                page-break-inside: avoid;
                display: block;
                visibility: visible;
            }

            .page-container:last-child {
                page-break-after: avoid;
                min-height: auto;
            }

            /* Only add page break when explicitly needed and content is substantial */
            .force-new-page {
                page-break-before: always;
            }

            /* Header styling - only appears on first page */
            .header {
                margin-bottom: {{ $paperSize === 'Folio' ? '6px' : '10px' }};
                padding-bottom: {{ $paperSize === 'Folio' ? '5px' : '8px' }};
                border-bottom: 2px solid #333;
                text-align: center;
                background: white;
                width: 100%;
            }

            .header h1 {
                font-size: {{ $paperSize === 'A4' ? '16px' : ($paperSize === 'Folio' ? '20px' : '14px') }};
                margin-bottom: {{ $paperSize === 'Half-A4' ? '2px' : ($paperSize === 'Half-Folio' ? '2px' : '3px') }};
                font-weight: bold;
                color: #000;
            }

            /* Prevent orphaned elements */
            .invoice-section {
                page-break-inside: avoid;
                orphans: 3;
                widows: 3;
            }

            .table {
                page-break-inside: avoid;
                orphans: 4;
                widows: 4;
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
                margin-bottom: {{ $paperSize === 'Folio' ? '6px' : '10px' }};
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
                margin-bottom: {{ $paperSize === 'Folio' ? '3px' : ($paperSize === 'Half-A4' ? '5px' : ($paperSize === 'Half-Folio' ? '5px' : '10px')) }};
                width: 100%;
                font-size: {{ $paperSize === 'Half-A4' ? '7px' : ($paperSize === 'Half-Folio' ? '7px' : ($paperSize === 'A4' ? '9px' : ($paperSize === 'Folio' ? '8px' : '10px'))) }};
            }

            .table th,
            .table td {
                padding: {{ $paperSize === 'Half-A4' ? '1px' : ($paperSize === 'Half-Folio' ? '1px' : '2px 1px') }};
                font-size: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : ($paperSize === 'A4' ? '9px' : '10px')) }};
                border: 1px solid #000;
                word-wrap: break-word;
            }

            .table th {
                font-size: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : ($paperSize === 'A4' ? '9px' : '10px')) }};
                background-color: #f8f9fa !important;
                color: #333 !important;
                border: 2px solid #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Specific column widths for better spacing */
            .table th:nth-child(1) { width: 3%; }  /* No */
            .table th:nth-child(2) { width: 10%; } /* No. Kontainer */
            .table th:nth-child(3) { width: 4%; }  /* Size */
            .table th:nth-child(4) { width: 13%; } /* Masa */
            .table th:nth-child(5) { width: 9%; } /* DPP */
            .table th:nth-child(6) { width: 9%; } /* Adjustment */
            .table th:nth-child(7) { width: 7%; }  /* PPN */
            .table th:nth-child(8) { width: 7%; }  /* PPH */
            .table th:nth-child(9) { width: 9%; } /* Grand Total */
            .table th:nth-child(10) { width: 15%; } /* Invoice Vendor */

            .table td:nth-child(1) { width: 3%; text-align: center; }
            .table td:nth-child(2) { width: 10%; }
            .table td:nth-child(3) { width: 4%; text-align: center; }
            .table td:nth-child(4) { width: 13%; }
            .table td:nth-child(5) { width: 9%; text-align: right; }
            .table td:nth-child(6) { width: 9%; text-align: right; }
            .table td:nth-child(7) { width: 7%; text-align: right; }
            .table td:nth-child(8) { width: 7%; text-align: right; }
            .table td:nth-child(9) { width: 9%; text-align: right; }
            .table td:nth-child(10) { width: 15%; text-align: center; }

            .masa-display {
                padding: 0;
                font-size: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : '8px') }};
                line-height: 1.1;
            }

            .masa-display small {
                font-size: {{ $paperSize === 'Half-A4' ? '8px' : ($paperSize === 'Half-Folio' ? '8px' : '6px') }};
            }

            .col-vendor {
                max-width: none;
                font-size: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : '8px') }};
            }

            .col-nomor {
                font-size: {{ $paperSize === 'Half-A4' ? '10px' : ($paperSize === 'Half-Folio' ? '10px' : '8px') }};
                font-weight: 500;
            }

            .summary {
                margin-top: 5px;
                font-size: 9px;
                text-align: right;
                padding-right: 2px;
            }

            .summary-label {
                width: 70px;
            }

            .total-amount {
                font-size: 10px;
                padding-top: 3px;
                margin-top: 3px;
            }

            .signature-section {
                margin-top: auto;
                page-break-inside: avoid;
                position: absolute;
                @if($paperSize === 'Half-A4')
                    bottom: 5mm;
                @elseif($paperSize === 'Half-Folio')
                    bottom: 5mm;
                @elseif($paperSize === 'Folio')
                    bottom: 15mm;
                @else
                    bottom: {{ $paperSize === 'A4' ? '8mm' : '10mm' }};
                @endif
                left: 0;
                right: 0;
                width: 100%;
            }

            .signature-section table {
                width: 100% !important;
                border-collapse: collapse !important;
                border: 2px solid #000 !important;
                margin-top: 8px !important;
            }

            .signature-section td {
                width: 33.33% !important;
                border: 1px solid #000 !important;
                padding: 10px 8px !important;
                text-align: center !important;
                vertical-align: top !important;
            }

            .signature-section td div:first-child {
                font-weight: bold !important;
                margin-bottom: 25px !important;
                font-size: 10px !important;
            }

            .signature-section td div:nth-child(2) {
                height: 35px !important;
                border-bottom: 1px solid #000 !important;
                margin-bottom: 5px !important;
            }

            .signature-section td div:last-child {
                font-size: 9px !important;
                font-weight: bold !important;
            }

            .footer {
                margin-top: 10px;
                padding-top: 5px;
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
                margin-top: 8px;
                margin-bottom: 8px;
            }

            .keterangan-table td {
                border: 2px solid #000 !important;
                height: 55px !important;
                min-height: 55px !important;
                padding: 8px !important;
                font-size: 10px !important;
                line-height: 1.4 !important;
                vertical-align: top !important;
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
                    @if($paperSize === 'Custom-215')
                        <strong>Ukuran: Custom 21.5cm x 29.7cm</strong><br>
                        @if($totalPages > 1)<span style="color: #dc2626;">📄 {{ $totalPages }} halaman - Data dibagi karena terlalu banyak</span><br>@endif
                        📌 Saat Print Dialog:<br>
                        &nbsp;&nbsp;&nbsp;1️⃣ Scale: <strong>None / 100% / Actual Size</strong><br>
                        &nbsp;&nbsp;&nbsp;2️⃣ Orientation: <strong>Portrait (Tegak)</strong><br>
                        &nbsp;&nbsp;&nbsp;3️⃣ Paper: <strong>Custom (21.5cm x 29.7cm)</strong><br>
                        &nbsp;&nbsp;&nbsp;4️⃣ Margins: <strong>None / Minimal</strong>
                    @elseif($paperSize === 'Half-Custom-215')
                        <strong>Ukuran: Setengah Custom (21.5 x 14.85 cm)</strong><br>
                        @if($totalPages > 1)<span style="color: #dc2626;">📄 {{ $totalPages }} halaman - Data dibagi karena terlalu banyak</span><br>@endif
                        📌 Saat Print Dialog:<br>
                        &nbsp;&nbsp;&nbsp;1️⃣ Scale: <strong>None / 100% / Actual Size</strong><br>
                        &nbsp;&nbsp;&nbsp;2️⃣ Orientation: <strong>Portrait (Tegak)</strong><br>
                        &nbsp;&nbsp;&nbsp;3️⃣ Paper: <strong>Custom (21.5cm x 29.7cm)</strong><br>
                        &nbsp;&nbsp;&nbsp;4️⃣ Margins: <strong>None / Minimal</strong><br>
                        ✂️ Setelah print, potong kertas menjadi 2 bagian (setengah horizontal)
                    @elseif($paperSize === 'Half-A4')
                        <strong>Ukuran: Setengah A4 (210 x 148.5 mm)</strong><br>
                        @if($totalPages > 1)<span style="color: #dc2626;">� {{ $totalPages }} halaman - Data dibagi karena terlalu banyak</span><br>@endif
                        �📌 Saat Print Dialog:<br>
                        &nbsp;&nbsp;&nbsp;1️⃣ Scale: <strong>None / 100% / Actual Size</strong><br>
                        &nbsp;&nbsp;&nbsp;2️⃣ Orientation: <strong>Portrait (Tegak)</strong><br>
                        &nbsp;&nbsp;&nbsp;3️⃣ Paper: <strong>A4</strong><br>
                        &nbsp;&nbsp;&nbsp;4️⃣ Margins: <strong>None / Minimal</strong><br>
                        ✂️ Setelah print, potong kertas A4 menjadi 2 bagian (setengah horizontal)
                    @elseif($paperSize === 'Half-Folio')
                        <strong>Ukuran: Setengah Folio (8.5 x 6.5 inch)</strong><br>
                        @if($totalPages > 1)<span style="color: #dc2626;">📄 {{ $totalPages }} halaman - Data dibagi karena terlalu banyak</span><br>@endif
                        📌 Saat Print Dialog:<br>
                        &nbsp;&nbsp;&nbsp;1️⃣ Scale: <strong>None / 100% / Actual Size</strong><br>
                        &nbsp;&nbsp;&nbsp;2️⃣ Orientation: <strong>Portrait (Tegak)</strong><br>
                        &nbsp;&nbsp;&nbsp;3️⃣ Paper: <strong>Legal/Folio</strong><br>
                        &nbsp;&nbsp;&nbsp;4️⃣ Margins: <strong>None / Minimal</strong><br>
                        ✂️ Setelah print, potong kertas Folio menjadi 2 bagian (setengah horizontal)
                    @elseif($paperSize === 'Folio')
                        <strong>Ukuran: Legal/Folio (8.5 x 13 inch)</strong><br>
                        @if($totalPages > 1)<span style="color: #dc2626;">� {{ $totalPages }} halaman - Data dibagi karena terlalu banyak</span><br>@endif
                        �📌 Set Paper Size: <strong>Legal</strong> dan Scale: <strong>100%</strong>
                    @else
                        <strong>Ukuran: A4 (210 x 297 mm)</strong><br>
                        @if($totalPages > 1)<span style="color: #dc2626;">📄 {{ $totalPages }} halaman - Data dibagi karena terlalu banyak</span><br>@endif
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
                @if($paperSize === 'Custom-215')
                    (215mm × 297mm)
                @elseif($paperSize === 'Half-Custom-215')
                    (215mm × 148.5mm)
                @elseif($paperSize === 'Half-A4')
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



    @php
        $globalRowNumber = 0; // Initialize global row counter
    @endphp
    @foreach($chunkedItems as $pageIndex => $pageItems)
    <div class="page-container {{ $pageIndex > 0 ? 'force-new-page' : '' }}">
        <!-- Header only on first page -->
        @if($pageIndex === 0)
        <div class="header" style="display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important; width: 100% !important; background: white !important; z-index: 9999 !important;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; visibility: visible !important;">
                <div style="text-align: left; visibility: visible !important;">
                    <strong style="font-size: 12px; visibility: visible !important; display: inline !important;">PT. ALEXINDO YAKINPRIMA</strong><br>
                    <span style="font-size: 10px; visibility: visible !important; display: inline !important;">Jalan Pluit Raya No.8 Blok B No.12, Jakarta Utara 14440</span><br>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; visibility: visible !important;">
                <span style="font-size: 10px; font-weight: bold; visibility: visible !important; display: inline !important;">
                    Tanggal: {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d-M-Y') }}
                </span>
                <div style="text-align: right; visibility: visible !important;">
                    <span style="font-size: 10px; font-weight: bold; visibility: visible !important; display: inline !important;">{{ $pranota->no_invoice }}</span>
                    @if($totalPages > 1)
                    <br><span style="font-size: 8px; color: #666; visibility: visible !important; display: inline !important;">Hal {{ $pageIndex + 1 }} dari {{ $totalPages }}</span>
                    @endif
                </div>
            </div>
            <h1 style="visibility: visible !important; display: block !important; font-size: {{ $paperSize === 'Folio' ? '20px' : '16px' }} !important;">PRANOTA TAGIHAN KONTAINER</h1>
        </div>
        @else
        <!-- Simple page indicator for subsequent pages -->
        <div style="text-align: right; margin-bottom: 10px; padding: 5px 0; font-size: 10px; color: #666;">
            Halaman {{ $pageIndex + 1 }} dari {{ $totalPages }}
        </div>
        @endif

        <!-- Info Section (on every page for consistency) -->
        <div class="info-section">
            <div class="info-left">
                @if($vendorList->isNotEmpty())
                <div class="info-item">
                    <span class="info-label">Vendor:</span>
                    <span>{{ $vendorList->implode(', ') }}</span>
                </div>
                @endif
            </div>
            <div class="info-right">
                <!-- Info right can be used for other information if needed -->
            </div>
        </div>

        <!-- Invoice Table (only on first page and if not too many invoices) -->
        @if($pageIndex === 0 && $invoices->isNotEmpty())
        <div class="invoice-section" style="margin-bottom: {{ $paperSize === 'Folio' ? '3px' : '8px' }};">
            <h3 style="font-size: {{ $paperSize === 'Folio' ? '10px' : '12px' }}; font-weight: bold; margin-bottom: {{ $paperSize === 'Folio' ? '3px' : '8px' }}; color: #333;">INVOICE YANG DIGUNAKAN:</h3>
            <table class="table" style="margin-bottom: {{ $paperSize === 'Folio' ? '3px' : '6px' }}; {{ $paperSize === 'Folio' ? 'font-size: 9px;' : '' }}">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: {{ $invoices->count() > 3 ? '25%' : '20%' }};">Nomor Invoice</th>
                        <th style="width: {{ $invoices->count() > 3 ? '20%' : '20%' }};">Vendor</th>
                        <th style="width: {{ $invoices->count() > 3 ? '15%' : '15%' }};">Invoice Vendor</th>
                        <th style="width: {{ $invoices->count() > 3 ? '15%' : '15%' }};">Tanggal Invoice</th>
                        <th style="width: {{ $invoices->count() > 3 ? '15%' : '15%' }};">Total Invoice</th>
                        <th style="width: {{ $invoices->count() > 3 ? '5%' : '10%' }};">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $index => $invoice)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td style="{{ $paperSize === 'Folio' ? 'font-size: 8px;' : '' }}">{{ $invoice->nomor_invoice }}</td>
                        <td style="{{ $paperSize === 'Folio' ? 'font-size: 8px;' : '' }}">{{ $invoice->vendor_name ?: '-' }}</td>
                        <td style="{{ $paperSize === 'Folio' ? 'font-size: 8px;' : '' }}">{{ $invoice->invoice_vendor ?: '-' }}</td>
                        <td class="text-center" style="{{ $paperSize === 'Folio' ? 'font-size: 9px;' : '' }}">{{ $invoice->tanggal_invoice ? $invoice->tanggal_invoice->format('d-M-Y') : '-' }}</td>
                        <td class="text-right" style="{{ $paperSize === 'Folio' ? 'font-size: 9px;' : '' }}">{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @php
                                $statusLabels = [
                                    'draft' => 'Draft',
                                    'submitted' => 'Submitted', 
                                    'approved' => 'Approved',
                                    'paid' => 'Paid',
                                    'cancelled' => 'Cancelled',
                                ];
                            @endphp
                            <span style="padding: {{ $paperSize === 'Folio' ? '1px 4px' : '2px 6px' }}; font-size: {{ $paperSize === 'Folio' ? '7px' : '8px' }}; border: 1px solid #333; border-radius: 3px;">
                                {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="5" class="text-center" style="font-weight: bold;">TOTAL INVOICE</td>
                        <td class="text-right">{{ number_format($invoices->sum('total'), 0, ',', '.') }}</td>
                        <td class="text-center">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Table for current page -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 10%;">No. Kontainer</th>
                    <th style="width: 4%;">Size</th>
                    <th style="width: 13%;">Masa</th>
                    <th style="width: 9%;">DPP</th>
                    <th style="width: 9%;">Adjustment</th>
                    <th style="width: 7%;">PPN</th>
                    <th style="width: 7%;">PPH</th>
                    <th style="width: 9%;">Grand Total</th>
                    <th style="width: 15%;">Invoice Vendor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pageItems as $index => $item)
                @php
                    $globalRowNumber++; // Increment for each row across all pages
                @endphp
                <tr>
                    <td class="text-center">{{ $globalRowNumber }}</td>
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
                    <td class="text-center">{{ $item->invoice_vendor ?: '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada tagihan ditemukan</td>
                </tr>
                @endforelse
                
                <!-- Show total only on last page -->
                @if($pageIndex === $totalPages - 1)
                <tr class="total-row">
                    <td colspan="4" class="text-center" style="font-weight: bold; text-align: center;">TOTAL</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('dpp'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('adjustment'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('ppn'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('pph'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('grand_total'), 0, ',', '.') }}</td>
                    <td class="text-center">-</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Show summary, keterangan, and signature only on last page -->
        @if($pageIndex === $totalPages - 1)
        <!-- Summary -->
        <div class="summary">
            <div class="summary-item total-amount" style="margin-top: 10px;">
                <span class="summary-label">PEMBAYARAN:</span>
                <span>Rp {{ number_format($tagihanItems->sum('grand_total'), 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Keterangan Table -->
        <div class="keterangan-table" style="margin-top: 5px; margin-bottom: 5px;">
            <table style="width: 100%; border-collapse: collapse; border: 2px solid #333;">
                <tbody>
                    <tr>
                        <td style="padding: 6px; border: 2px solid #333; font-size: 11px; height: 35px; min-height: 35px; vertical-align: top; line-height: 1.4;">
                            {{ $pranota->keterangan ?: '' }}
                            @if(!$pranota->keterangan)
                                <div style="border-bottom: 1px solid #ccc; margin-bottom: 4px; height: 12px;"></div>
                                <div style="border-bottom: 1px solid #ccc; margin-bottom: 4px; height: 12px;"></div>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <table style="width: 100%; border-collapse: collapse; border: 2px solid #333; margin-top: 10px;">
                <tr>
                    <td style="width: 33.33%; border: 1px solid #333; padding: 15px 10px; text-align: center; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 30px; font-size: 11px;">Dibuat Oleh</div>
                        <div style="height: 40px; border-bottom: 1px solid #333; margin-bottom: 5px;"></div>
                        <div style="font-size: 10px; font-weight: bold;">(_____________)</div>
                    </td>
                    <td style="width: 33.33%; border: 1px solid #333; padding: 15px 10px; text-align: center; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 30px; font-size: 11px;">Disetujui Oleh</div>
                        <div style="height: 40px; border-bottom: 1px solid #333; margin-bottom: 5px;"></div>
                        <div style="font-size: 10px; font-weight: bold;">(_____________)</div>
                    </td>
                    <td style="width: 33.33%; border: 1px solid #333; padding: 15px 10px; text-align: center; vertical-align: top;">
                        <div style="font-weight: bold; margin-bottom: 30px; font-size: 11px;">Diterima Oleh</div>
                        <div style="height: 40px; border-bottom: 1px solid #333; margin-bottom: 5px;"></div>
                        <div style="font-size: 10px; font-weight: bold;">(_____________)</div>
                    </td>
                </tr>
            </table>
        </div>
        @endif
    </div>
    @endforeach

    <!-- Print Script -->
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
