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
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-left, .info-right {
            width: 48%;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
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
            margin-bottom: 30px;
            table-layout: fixed;
        }

        .table th,
        .table td {
            border: 1px solid #333;
            padding: 8px 6px;
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .table th {
            background-color: #000000;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            white-space: nowrap;
        }

        .table td {
            font-size: 11px;
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
            background-color: #ffffff;
        }

        .table tr:hover {
            background-color: #f8f8f8;
        }

        .total-row td {
            background-color: #000000 !important;
            color: #ffffff !important;
            font-weight: bold !important;
        }

        .summary {
            margin-top: 20px;
            text-align: right;
        }

        .summary-item {
            margin-bottom: 5px;
        }

        .summary-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }

        .total-amount {
            font-size: 16px;
            font-weight: bold;
            color: #000000;
            border-top: 2px solid #000000;
            padding-top: 10px;
            margin-top: 10px;
        }

        .signature-section {
            margin-top: 50px;
            text-align: center;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .signature-cell {
            width: 33.33%;
            padding: 20px;
            text-align: center;
            vertical-align: top;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 40px;
            font-size: 12px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 1px;
        }

        .signature-name {
            font-size: 11px;
            margin-bottom: 5px;
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
                margin-bottom: 15px;
                padding-bottom: 10px;
            }

            .header h1 {
                font-size: 18px;
                margin-bottom: 5px;
            }

            .header h2 {
                font-size: 14px;
                margin-bottom: 8px;
            }

            .header div strong {
                font-size: 12px;
            }

            .header div span {
                font-size: 10px;
            }

            .info-section {
                margin-bottom: 15px;
                font-size: 10px;
            }

            .info-label {
                width: 100px;
                font-size: 10px;
            }

            .no-print {
                display: none;
            }

            .table {
                page-break-inside: avoid;
                margin-bottom: 15px;
                width: 100%;
                font-size: 10px;
            }

            .table th,
            .table td {
                padding: 6px 4px;
                font-size: 10px;
                border: 1px solid #000;
                word-wrap: break-word;
            }

            .table th {
                font-size: 10px;
                background-color: #000 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Specific column widths for better spacing */
            .table th:nth-child(1) { width: 8%; }  /* No */
            .table th:nth-child(2) { width: 20%; } /* No. Kontainer */
            .table th:nth-child(3) { width: 8%; }  /* Size */
            .table th:nth-child(4) { width: 15%; } /* Masa */
            .table th:nth-child(5) { width: 12%; } /* DPP */
            .table th:nth-child(6) { width: 12%; } /* Adjustment */
            .table th:nth-child(7) { width: 10%; } /* PPN */
            .table th:nth-child(8) { width: 10%; } /* PPH */
            .table th:nth-child(9) { width: 15%; } /* Grand Total */

            .table td:nth-child(1) { width: 8%; text-align: center; }
            .table td:nth-child(2) { width: 20%; }
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
                margin-top: 15px;
                font-size: 11px;
            }

            .summary-label {
                width: 120px;
            }

            .total-amount {
                font-size: 12px;
                padding-top: 8px;
                margin-top: 8px;
            }

            .signature-section {
                margin-top: 40px;
            }

            .signature-table {
                margin-top: 15px;
            }

            .signature-cell {
                padding: 15px 8px;
            }

            .signature-label {
                margin-bottom: 25px;
                font-size: 10px;
            }

            .signature-line {
                margin-bottom: 5px;
            }

            .signature-name {
                font-size: 9px;
                margin-bottom: 5px;
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
                background-color: #000 !important;
                color: #fff !important;
                font-weight: bold !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div style="text-align: left; margin-bottom: 15px;">
                <strong style="font-size: 14px;">PT. ALEXINDO YAKINPRIMA</strong><br>
                <span style="font-size: 11px;">Jalan Pluit Raya No.8 Blok B No.12</span>
            </div>
            <h1>PRANOTA TAGIHAN KONTAINER</h1>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-item">
                    <span class="info-label">No. Pranota:</span>
                    <span>{{ $pranota->no_invoice }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Pranota:</span>
                    <span>{{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d-M-y') }}</span>
                </div>
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
                <div class="info-item">
                    <span class="info-label">Keterangan:</span>
                    <span>{{ $pranota->keterangan ?: '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 20%;">No. Kontainer</th>
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
                    <td class="text-right">{{ number_format($item->dpp ?? 0, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->adjustment ?? 0, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->ppn ?? 0, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->pph ?? 0, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->grand_total ?? 0, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada tagihan ditemukan</td>
                </tr>
                @endforelse
                <!-- Total Row -->
                <tr class="total-row" style="background-color: #000000; color: #ffffff; font-weight: bold;">
                    <td class="text-center" style="background-color: #000000; color: #ffffff; font-weight: bold;">TOTAL</td>
                    <td style="background-color: #000000; color: #ffffff;"></td>
                    <td style="background-color: #000000; color: #ffffff;"></td>
                    <td style="background-color: #000000; color: #ffffff;"></td>
                    <td class="text-right" style="background-color: #000000; color: #ffffff; font-weight: bold;">{{ number_format($tagihanItems->sum('dpp'), 2, ',', '.') }}</td>
                    <td class="text-right" style="background-color: #000000; color: #ffffff; font-weight: bold;">{{ number_format($tagihanItems->sum('adjustment'), 2, ',', '.') }}</td>
                    <td class="text-right" style="background-color: #000000; color: #ffffff; font-weight: bold;">{{ number_format($tagihanItems->sum('ppn'), 2, ',', '.') }}</td>
                    <td class="text-right" style="background-color: #000000; color: #ffffff; font-weight: bold;">{{ number_format($tagihanItems->sum('pph'), 2, ',', '.') }}</td>
                    <td class="text-right" style="background-color: #000000; color: #ffffff; font-weight: bold;">{{ number_format($tagihanItems->sum('grand_total'), 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-item total-amount" style="margin-top: 15px;">
                <span class="summary-label">TOTAL AMOUNT:</span>
                <span>Rp {{ number_format((float)$pranota->total_amount, 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td class="signature-cell">
                        <div class="signature-label">Dibuat Oleh</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $pranota->created_by ?? 'Admin' }}</div>
                    </td>
                    <td class="signature-cell">
                        <div class="signature-label">Disetujui Oleh</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $pranota->approved_by ?? 'Manager' }}</div>
                    </td>
                    <td class="signature-cell">
                        <div class="signature-label">Diterima Oleh</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $pranota->received_by ?? '' }}</div>
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
