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
        }

        .status-unpaid { background-color: #f3f4f6; color: #374151; }
        .status-sent { background-color: #fef3c7; color: #92400e; }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 10px 8px;
            text-align: left;
            vertical-align: middle;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            white-space: nowrap;
        }

        .table td {
            font-size: 10px;
            white-space: nowrap;
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
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 10px;
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
            background-color: #fef3c7;
            color: #92400e;
            border-color: #f59e0b;
        }

        .tarif-bulanan {
            background-color: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }

        /* Responsive table adjustments */
        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .table tr:hover {
            background-color: #f3f4f6;
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
            color: #059669;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
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
                size: 165mm 215mm; /* Setengah Folio portrait */
                margin: 8mm;
            }

            body {
                font-size: 9px;
                width: 149mm;
            }

            .container {
                padding: 6px;
                max-width: 100%;
            }

            .header {
                margin-bottom: 12px;
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
                font-size: 11px;
            }

            .header div span {
                font-size: 9px;
            }

            .info-section {
                margin-bottom: 12px;
                font-size: 8px;
            }

            .info-label {
                width: 80px;
                font-size: 8px;
            }

            .no-print {
                display: none;
            }

            .table {
                page-break-inside: avoid;
                margin-bottom: 12px;
            }

            .table th,
            .table td {
                padding: 2px 1px;
                font-size: 10px;
            }

            .table th {
                font-size: 10px;
            }

            .masa-display {
                padding: 0;
                font-size: 10px;
            }

            .masa-display small {
                font-size: 5px;
            }

            .col-vendor {
                max-width: 60px;
                font-size: 10px;
            }

            .col-nomor {
                font-size: 10px;
            }

            .summary {
                margin-top: 12px;
                font-size: 8px;
            }

            .summary-label {
                width: 100px;
            }

            .total-amount {
                font-size: 10px;
                padding-top: 6px;
                margin-top: 6px;
            }

            .footer {
                margin-top: 15px;
                padding-top: 8px;
                font-size: 7px;
            }

            .status-badge {
                font-size: 7px;
                padding: 1px 6px;
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
                    <span>{{ $pranota->tanggal_pranota->format('d-M-y') }}</span>
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
                    <th style="width: 5%;">No</th>
                    <th style="width: 18%;">No. Kontainer</th>
                    <th style="width: 8%;">Size</th>
                    <th style="width: 10%;">Masa</th>
                    <th style="width: 13%;">DPP</th>
                    <th style="width: 10%;">Adjustment</th>
                    <th style="width: 9%;">PPN</th>
                    <th style="width: 9%;">PPH</th>
                    <th style="width: 18%;">Grand Total</th>
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
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td class="text-center">TOTAL</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('dpp'), 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('adjustment'), 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('ppn'), 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('pph'), 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('grand_total'), 2, ',', '.') }}</td>
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
