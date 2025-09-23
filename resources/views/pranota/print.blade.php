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
            font-size: 11px;
            text-align: center;
            white-space: nowrap;
        }

        .table td {
            font-size: 11px;
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
            font-family: 'Courier New', monospace;
            font-size: 10px;
        }

        .masa-display {
            display: inline-block;
            padding: 2px 6px;
            background-color: #f3f4f6;
            border-radius: 3px;
            border: 1px solid #d1d5db;
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
            body {
                font-size: 11px;
            }

            .container {
                padding: 10px;
            }

            .no-print {
                display: none;
            }

            .table {
                page-break-inside: avoid;
            }

            .table th,
            .table td {
                padding: 6px 4px;
                font-size: 10px;
            }

            .masa-display {
                padding: 1px 4px;
                font-size: 9px;
            }

            .masa-display small {
                font-size: 7px;
            }

            .col-vendor {
                max-width: 100px;
                font-size: 9px;
            }

            .col-nomor {
                font-size: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PRANOTA TAGIHAN KONTAINER</h1>
            <h2>No. {{ $pranota->no_invoice }}</h2>
            <p>Tanggal: {{ $pranota->tanggal_pranota->format('d/m/Y') }}</p>
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
                    <span>{{ $pranota->tanggal_pranota->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Due Date:</span>
                    <span>
                        @if($pranota->due_date)
                            {{ $pranota->due_date->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </span>
                </div>
            </div>
            <div class="info-right">
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="status-badge status-{{ $pranota->status }}">
                        @if($pranota->status == 'unpaid')
                            Belum Lunas
                        @elseif($pranota->status == 'sent')
                            Terkirim
                        @elseif($pranota->status == 'paid')
                            Lunas
                        @elseif($pranota->status == 'cancelled')
                            Dibatalkan
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jumlah Tagihan:</span>
                    <span>{{ $pranota->jumlah_tagihan }} item</span>
                </div>
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
                    <th style="width: 15%;">Vendor</th>
                    <th style="width: 15%;">No. Kontainer</th>
                    <th style="width: 8%;">Size</th>
                    <th style="width: 10%;">Periode</th>
                    <th style="width: 10%;">Masa</th>
                    <th style="width: 12%;">Tarif</th>
                    <th style="width: 12%;">DPP</th>
                    <th style="width: 13%;">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihanItems as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="col-vendor">{{ $item->vendor }}</td>
                    <td class="col-nomor">{{ $item->nomor_kontainer }}</td>
                    <td class="text-center">{{ $item->size }}</td>
                    <td class="col-periode">{{ $item->periode }}</td>
                    <td class="col-masa">
                        @if($item->masa)
                            <span class="masa-display">
                                {{ $item->masa }}
                                @if(strpos($item->masa, 'bulan') === false && strpos($item->masa, 'hari') === false && is_numeric($item->masa))
                                    <small>hari</small>
                                @endif
                            </span>
                        @else
                            <span class="masa-display">-</span>
                        @endif
                    </td>
                    <td class="col-tarif">
                        @if($item->tarif)
                            @if(strtolower($item->tarif) == 'harian')
                                <span class="masa-display tarif-harian">Harian</span>
                            @elseif(strtolower($item->tarif) == 'bulanan')
                                <span class="masa-display tarif-bulanan">Bulanan</span>
                            @else
                                <span class="masa-display">{{ $item->tarif }}</span>
                            @endif
                        @else
                            <span class="masa-display">-</span>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($item->dpp ?? 0, 2, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->grand_total ?? 0, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada tagihan ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-item">
                <span class="summary-label">Subtotal:</span>
                <span>Rp {{ number_format($tagihanItems->sum('grand_total'), 2, ',', '.') }}</span>
            </div>
            <div class="summary-item total-amount">
                <span class="summary-label">TOTAL AMOUNT:</span>
                <span>Rp {{ number_format((float)$pranota->total_amount, 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dicetak pada {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Sistem Pranota Tagihan Kontainer - {{ config('app.name') }}</p>
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
