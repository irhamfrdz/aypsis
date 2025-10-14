<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pranota {{ $pranota->no_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: 20px;
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
            font-size: 10px;
            text-align: center;
            white-space: nowrap;
            border: 2px solid #333;
        }

        .table td {
            font-size: 10px;
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

        .signature-section {
            margin-top: 12px;
            text-align: center;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .signature-cell {
            width: 33.33%;
            padding: 12px 8px;
            text-align: center;
            vertical-align: top;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 30px;
            font-size: 11px;
        }

        .signature-line {
            border-bottom: 2px solid #333;
            margin-bottom: 8px;
            height: 2px;
            width: 150px;
            margin-left: auto;
            margin-right: auto;
        }

        .signature-name {
            font-size: 11px;
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
                size: A4;
                margin: 10mm;
            }

            body {
                font-size: 11px;
                width: 100%;
                margin: 0;
                padding: 0;
                color: #000;
            }

            .container {
                padding: 0;
                max-width: 100%;
                width: 100%;
            }

            .header {
                margin-bottom: 10px;
                padding-bottom: 8px;
            }

            .header h1 {
                font-size: 16px;
                margin-bottom: 3px;
            }

            .header h2 {
                font-size: 12px;
                margin-bottom: 5px;
            }

            .header div strong {
                font-size: 12px;
            }

            .header div span {
                font-size: 10px;
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
                margin-bottom: 10px;
                width: 100%;
                font-size: 9px;
            }

            .table th,
            .table td {
                padding: 2px 1px;
                font-size: 9px;
                border: 1px solid #000;
                word-wrap: break-word;
            }

            .table th {
                font-size: 9px;
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
                font-size: 9px;
                line-height: 1.2;
            }

            .masa-display small {
                font-size: 7px;
            }

            .col-vendor {
                max-width: none;
                font-size: 10px;
            }

            .col-nomor {
                font-size: 10px;
                font-weight: 500;
            }

            .summary {
                margin-top: 8px;
                font-size: 10px;
            }

            .summary-label {
                width: 100px;
            }

            .total-amount {
                font-size: 11px;
                padding-top: 5px;
                margin-top: 5px;
            }

            .signature-section {
                margin-top: 15px;
                page-break-inside: avoid;
            }

            .signature-table {
                margin-top: 10px;
            }

            .signature-cell {
                padding: 10px 6px;
            }

            .signature-label {
                margin-bottom: 25px;
                font-size: 9px;
            }

            .signature-line {
                margin-bottom: 5px;
                border-bottom: 2px solid #000 !important;
                width: 120px;
                margin-left: auto;
                margin-right: auto;
            }

            .signature-name {
                font-size: 9px;
                margin-bottom: 5px;
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
                    <td class="text-center" style="font-weight: bold; text-align: center;">TOTAL</td>
                    <td style="font-weight: bold;"></td>
                    <td style="font-weight: bold;"></td>
                    <td style="font-weight: bold;"></td>
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
