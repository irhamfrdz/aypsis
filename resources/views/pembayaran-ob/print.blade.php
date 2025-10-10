<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran OB - {{ $pembayaran->nomor_pembayaran }}</title>
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
            <h1>BUKTI PEMBAYARAN OUT BOUND (OB)</h1>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-item">
                    <span class="info-label">No. Pembayaran:</span>
                    <span>{{ $pembayaran->nomor_pembayaran }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal:</span>
                    <span>{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d-M-y') }}</span>
                </div>
            </div>
            <div class="info-right">
                @if($pembayaran->keterangan)
                <div class="info-item">
                    <span class="info-label">Keterangan:</span>
                    <span>{{ $pembayaran->keterangan }}</span>
                </div>
                @endif
                @if($dpData)
                <div class="info-item">
                    <span class="info-label">DP Terkait:</span>
                    <span>{{ $dpData->nomor_pembayaran }}</span>
                </div>
                @endif
                @if($pembayaran->kasBankAkun)
                <div class="info-item">
                    <span class="info-label">Akun Kas/Bank:</span>
                    <span>{{ $pembayaran->kasBankAkun->nomor_akun . ' - ' . $pembayaran->kasBankAkun->nama_akun }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Supir Table -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">NIK</th>
                    <th style="width: 45%;">Nama Lengkap</th>
                    <th style="width: 25%;">Pembayaran per Supir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supirList as $index => $supir)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $supir->nik }}</td>
                    <td>{{ $supir->nama_lengkap }}</td>
                    <td class="text-right">{{ number_format($pembayaran->jumlah_per_supir ?? 0, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada supir ditemukan</td>
                </tr>
                @endforelse
                <!-- Total Row -->
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td class="text-center">TOTAL</td>
                    <td></td>
                    <td class="text-center">{{ count($supirList) }} Supir</td>
                    <td class="text-right">{{ number_format($pembayaran->total_pembayaran ?? 0, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-item">
                <span class="summary-label">Jumlah Supir:</span>
                <span>{{ count($supirList) }} orang</span>
            </div>
            @if($dpData && $pembayaran->subtotal_pembayaran)
            <div class="summary-item">
                <span class="summary-label">Subtotal Pembayaran:</span>
                <span>Rp {{ number_format($pembayaran->subtotal_pembayaran ?? 0, 2, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">DP yang Digunakan:</span>
                <span>- Rp {{ number_format($dpData->total_pembayaran ?? 0, 2, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Sisa Setelah DP:</span>
                <span>Rp {{ number_format(($pembayaran->subtotal_pembayaran ?? 0) - ($dpData->total_pembayaran ?? 0), 2, ',', '.') }}</span>
            </div>
            @endif
            <div class="summary-item">
                <span class="summary-label">Pembayaran per Supir:</span>
                <span>Rp {{ number_format($pembayaran->jumlah_per_supir ?? 0, 2, ',', '.') }}</span>
            </div>
            <div class="summary-item total-amount" style="margin-top: 15px;">
                <span class="summary-label">TOTAL AMOUNT:</span>
                <span>Rp {{ number_format((float)($pembayaran->total_pembayaran ?? 0), 2, ',', '.') }}</span>
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
