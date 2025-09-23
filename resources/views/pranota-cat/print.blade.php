<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Pranota Tagihan CAT - {{ $pranota->no_invoice ?? 'Belum ada nomor' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            background: white;
        }

        .print-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border: 2px solid #000;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding: 15px;
            background: #f8f9fa;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #666;
        }

        .pranota-info {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            background: #fff;
        }

        .info-section {
            flex: 1;
        }

        .info-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            width: 120px;
            font-weight: bold;
        }

        .info-value {
            flex: 1;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background: #f8f9fa;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #ddd;
        }

        .signature-section {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 40px;
            padding-bottom: 5px;
        }

        @media print {
            body {
                padding: 0;
            }

            .print-container {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <h1>ALEXINDO YAKIN PRIMA</h1>
            <h2>PRANOTA TAGIHAN CONTAINER</h2>
        </div>

        <!-- Pranota Information -->
        <div class="pranota-info">
            <div class="info-section">
                <h3>Informasi Pranota</h3>
                <div class="info-row">
                    <div class="info-label">Nomor Pranota:</div>
                    <div class="info-value">{{ $pranota->no_invoice ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Pranota:</div>
                    <div class="info-value">{{ $pranota->tanggal_pranota ? \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d/m/Y') : '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Vendor/Bengkel:</div>
                    <div class="info-value">{{ $pranota->supplier ?? '-' }}</div>
                </div>
            </div>
            <div class="info-section">
                <h3>Ringkasan Biaya</h3>
                <div class="info-row">
                    <div class="info-label">Total Biaya:</div>
                    <div class="info-value">
                        @php
                            $total = $pranota->total_amount ?? 0;
                            if ($total == 0 && $tagihanItems) {
                                $total = $tagihanItems->sum('realisasi_biaya');
                            }
                        @endphp
                        {{ $total > 0 ? 'Rp ' . number_format(floatval($total), 0, ',', '.') : '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Tagihan CAT</th>
                    <th>Nomor Kontainer</th>
                    <th>Vendor</th>
                    <th>Tanggal CAT</th>
                    <th>Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihanItems as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nomor_tagihan_cat ?? $item->id }}</td>
                    <td>{{ $item->nomor_kontainer ?? '-' }}</td>
                    <td>{{ $item->vendor ?? '-' }}</td>
                    <td>{{ $item->tanggal_cat ? \Carbon\Carbon::parse($item->tanggal_cat)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->realisasi_biaya ? 'Rp ' . number_format($item->realisasi_biaya, 0, ',', '.') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada item tagihan CAT.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">Dibuat Oleh</div>
                    <div style="margin-top: 10px; font-size: 10px;">Tanggal: {{ now()->format('d/m/Y') }}</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Disetujui Oleh</div>
                    <div style="margin-top: 10px; font-size: 10px;">Tanggal: _______________</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Diterima Oleh</div>
                    <div style="margin-top: 10px; font-size: 10px;">Tanggal: _______________</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
