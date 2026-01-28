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
        'fontSize' => '10px',
        'headerH1' => '14px',
        'tableFont' => '10px',
        'signatureBottom' => '3mm'
    ];
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
            min-height: {{ $currentPaper['height'] }}; /* Use min-height instead of fixed height */
            margin: 0 auto;
            padding: 3mm 4mm;
            position: relative;
            padding-bottom: 10px; /* Reduced further */
            box-sizing: border-box;
            background: white;
            /* Removed overflow: hidden to allow multi-page */
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: 2px;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 9px;
            color: #666;
        }

        .table-container {
            margin: 10px 0;
        }

        .table-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #495057;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #333;
            padding: 2px 2px;
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
            margin-top: 10px;
            padding: 8px;
            background-color: #f8f9fa;
            border: 1px solid #333;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 9px;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 10px;
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 8px;
        }

        .signature-section {
            margin-top: 15px; /* Add space above signature */
            text-align: center;
            page-break-inside: avoid;
            width: 100%;
            /* Removed absolute positioning so it flows naturally */
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .signature-cell {
            width: 33.33%;
            padding: 4px 2px;
            text-align: center;
            vertical-align: top;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 9px;
        }

        .signature-name {
            font-size: 10px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .print-button {
            display: none !important;
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

            html, body {
                width: {{ $currentPaper['width'] }};
                height: auto; /* Allow dynamic height */
                min-height: {{ $currentPaper['height'] }};
                margin: 0;
                padding: 0;
                font-size: {{ $currentPaper['fontSize'] }};
                color: #000;
                overflow: visible;
            }

            .container {
                width: {{ $currentPaper['containerWidth'] }};
                height: auto; /* Allow dynamic height */
                min-height: {{ $currentPaper['height'] }};
                max-height: none; /* Remove max-height constraint */
                border-bottom: none; /* Remove dashed border for actual print */
                padding: 3mm;
                margin: 0;
                box-sizing: border-box;
                overflow: visible; /* Allow overflow */
                position: relative;
            }

            .header h1 {
                font-size: 12px;
            }

            table {
                page-break-inside: auto; /* Allow page break inside table */
                margin-bottom: 5px;
                font-size: {{ $currentPaper['tableFont'] }};
            }

            tr {
                page-break-inside: avoid; /* Keep rows together */
                page-break-after: auto;
            }

            th, td {
                padding: 3px 2px;
                font-size: {{ $currentPaper['tableFont'] }};
                border: 1px solid #000;
                line-height: 1.2;
            }

            th {
                background-color: #ffffff !important;
                color: #000 !important;
                border: 1px solid #000 !important;
                font-weight: bold !important;
            }

            .signature-section {
                position: static; /* Flow naturally */
                margin-top: 20px;
                page-break-inside: avoid;
            }

            .summary {
                margin-top: 5px;
                font-size: 8px;
                padding: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Print Instructions Banner (hidden when printing) -->
    <div class="no-print" style="position: fixed; top: 10px; left: 10px; right: 10px; background: #fef3c7; padding: 15px 20px; border: 2px solid #f59e0b; border-radius: 8px; z-index: 1001; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: calc(100% - 20px); margin: 0 auto;">
        <div style="display: flex; align-items: start; gap: 12px;">
            <svg style="width: 28px; height: 28px; color: #f59e0b; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div style="flex: 1; min-width: 0;">
                <strong style="color: #92400e; display: block; margin-bottom: 6px; font-size: 14px;">⚠️ PENTING - Setting Print untuk Half-Folio:</strong>
                <div style="color: #78350f; font-size: 13px; line-height: 1.6;">
                    <strong>Ukuran: Setengah Folio (8.5 x 6.5 inch)</strong><br>
                    📌 Saat Print Dialog:<br>
                    &nbsp;&nbsp;&nbsp;1️⃣ Scale: <strong>None / 100% / Actual Size</strong><br>
                    &nbsp;&nbsp;&nbsp;2️⃣ Orientation: <strong>Portrait (Tegak)</strong><br>
                    &nbsp;&nbsp;&nbsp;3️⃣ Paper: <strong>Legal/Folio</strong><br>
                    &nbsp;&nbsp;&nbsp;4️⃣ Margins: <strong>None / Minimal</strong><br>
                    ✂️ Setelah print, potong kertas Folio menjadi 2 bagian (setengah horizontal)
                </div>
            </div>
        </div>
    </div>

    <!-- Print Button (hidden when printing) -->
    <div class="no-print" style="position: fixed; top: 10px; right: 10px; background: white; padding: 15px; border: 1px solid #ccc; border-radius: 5px; z-index: 1000; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
        <div style="margin-bottom: 10px; text-align: center;">
            <strong style="display: block; margin-bottom: 5px;">Half-Folio</strong>
            <small style="color: #666;">(8.5in × 6.5in)</small>
        </div>
        <button class="print-button" onclick="window.print()" style="width: 100%; background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 12px;">
            🖨️ Cetak Permohonan
        </button>
    </div>

    <div class="container">

        <!-- Header -->
        <div class="header">
            <h1>Form Permohonan Transfer</h1>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; font-size: 10px;">
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
                        <th style="width: 15%">Pengirim/Penerima</th>
                        <th style="width: 12%">Tujuan</th>
                        <th style="width: 10%">Uang Jalan</th>
                        <th style="width: 8%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pranotaUangJalan->uangJalans as $index => $uangJalan)
                        @php
                            $surat = $uangJalan->suratJalan ?? $uangJalan->suratJalanBongkaran;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">
                                @if($surat)
                                    {{ $surat->no_surat_jalan ?? $surat->nomor_surat_jalan ?? $uangJalan->nomor_uang_jalan }}
                                @else
                                    {{ $uangJalan->nomor_uang_jalan }}
                                @endif
                            </td>
                            <td>
                                @if($surat)
                                    {{ $surat->jenis_barang ?? 'PRODUK MINUMAN' }}
                                @else
                                    PRODUK MINUMAN
                                @endif
                            </td>
                            <td class="text-center">
                                @if($surat && isset($surat->supir_nik) && $surat->supir_nik)
                                    {{ $surat->supir_nik }}
                                @elseif($surat && isset($surat->kenek_nik) && $surat->kenek_nik)
                                    {{ $surat->kenek_nik }}
                                @else
                                    1280
                                @endif
                            </td>
                            <td>
                                @if($surat)
                                    {{ $surat->supir ?? 'Jokaria' }}
                                @else
                                    Jokaria
                                @endif
                            </td>
                            <td>
                                @if($uangJalan->suratJalan)
                                    @php
                                        $pengirimFull = $uangJalan->suratJalan->pengirim ?? 'PT CS2 POLA SEHAT';
                                        $pengirimNama = explode(',', $pengirimFull)[0];
                                    @endphp
                                    {{ trim($pengirimNama) }}
                                @elseif($uangJalan->suratJalanBongkaran)
                                    @php
                                        $penerimaFull = $uangJalan->suratJalanBongkaran->penerima ?? 'PT CS2 POLA SEHAT';
                                        $penerimaNama = explode(',', $penerimaFull)[0];
                                    @endphp
                                    {{ trim($penerimaNama) }}
                                @else
                                    PT CS2 POLA SEHAT
                                @endif
                            </td>
                            <td>
                                @if($surat)
                                    {{ $surat->tujuan_pengambilan ?? $surat->tujuan_pengiriman ?? 'TATUNG' }}
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
        <div class="signature-section" style="margin-top: 10px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 5px;">
                        <div style="border-bottom: 1px dotted #333; margin-bottom: 25px; height: 1px;"></div>
                        <div style="font-size: 9px;">
                            (Pemohon)
                        </div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 5px;">
                        <div style="border-bottom: 1px dotted #333; margin-bottom: 25px; height: 1px;"></div>
                        <div style="font-size: 9px;">
                            (Pemeriksa)
                        </div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 5px;">
                        <div style="border-bottom: 1px dotted #333; margin-bottom: 25px; height: 1px;"></div>
                        <div style="font-size: 9px;">
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