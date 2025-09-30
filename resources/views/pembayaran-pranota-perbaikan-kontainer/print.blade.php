<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Pembayaran Pranota Perbaikan Kontainer - {{ $pembayaran->nomor_pembayaran ?? $pembayaran->nomor_invoice }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 5px;
            background: white;
            line-height: 1.2;
        }

        .print-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border: 1px solid #000;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding: 8px;
            background: #f8f9fa;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 2px 0 0 0;
            font-size: 12px;
            color: #666;
        }

        .pembayaran-info {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            background: #fff;
        }

        .info-left, .info-right {
            width: 48%;
        }

        .info-row {
            display: flex;
            margin-bottom: 4px;
            align-items: center;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            flex-shrink: 0;
            font-size: 9px;
        }

        .info-value {
            flex: 1;
            border-bottom: 1px solid #333;
            min-height: 14px;
            padding-left: 3px;
            font-size: 9px;
        }

        .content-section {
            padding: 8px;
        }

        .section-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 6px;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 3px;
        }

        .perbaikan-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9px;
        }

        .perbaikan-table th,
        .perbaikan-table td {
            border: 1px solid #333;
            padding: 4px;
            text-align: left;
        }

        .perbaikan-table th {
            background: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding: 10px;
        }

        .signature-box {
            text-align: center;
            width: 150px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 40px;
            margin-bottom: 5px;
        }

        .total-section {
            background: #f8f9fa;
            border: 1px solid #333;
            padding: 8px;
            margin: 10px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }

        .total-final {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 5px;
        }

        @media print {
            body {
                margin: 0;
                padding: 2px;
            }

            .print-container {
                border: none;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 0.5cm;
            }
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
            z-index: 1000;
        }

        .print-button:hover {
            background: #0056b3;
        }

        .status-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
        }

        .status-completed { background: #28a745; color: #fff; }
        .status-pending { background: #ffc107; color: #000; }
        .status-cancelled { background: #dc3545; color: #fff; }

        .compact-table {
            font-size: 8px;
        }

        .compact-table th,
        .compact-table td {
            padding: 2px 3px;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Dokumen</button>

    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <h1>Bukti Pembayaran Pranota Perbaikan Kontainer</h1>
            <h2>{{ config('app.name', 'PT. AYPSIS') }}</h2>
        </div>

<!-- Informasi Pranota -->
        <div class="content-section">
            <div class="section-title">Informasi Pranota & Pembayaran</div>
            <table style="width: 100%; border-collapse: collapse; font-size: 9px;">
                <tr>
                    <td style="width: 20%; padding: 3px; font-weight: bold;">Nomor Pranota:</td>
                    <td style="width: 30%; padding: 3px; border-bottom: 1px solid #333;">{{ $pembayaran->pranotaPerbaikanKontainers->first()?->nomor_pranota ?? '-' }}</td>
                    <td style="width: 20%; padding: 3px; font-weight: bold;">Nomor Pembayaran:</td>
                    <td style="width: 30%; padding: 3px; border-bottom: 1px solid #333;">{{ $pembayaran->nomor_pembayaran ?? $pembayaran->nomor_invoice }}</td>
                </tr>
                <tr>
                    <td style="padding: 3px; font-weight: bold;">Tanggal Pranota:</td>
                    <td style="padding: 3px; border-bottom: 1px solid #333;">{{ $pembayaran->pranotaPerbaikanKontainers->first()?->tanggal_pranota ? \Carbon\Carbon::parse($pembayaran->pranotaPerbaikanKontainers->first()?->tanggal_pranota)->format('d/m/Y') : '-' }}</td>
                    <td style="padding: 3px; font-weight: bold;">Tanggal Pembayaran:</td>
                    <td style="padding: 3px; border-bottom: 1px solid #333;">{{ $pembayaran->tanggal_kas ? \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 3px; font-weight: bold;">Nama Teknisi:</td>
                    <td style="padding: 3px; border-bottom: 1px solid #333;">{{ $pembayaran->pranotaPerbaikanKontainers->first()?->nama_teknisi ?? '-' }}</td>
                    <td style="padding: 3px; font-weight: bold;">Bank:</td>
                    <td style="padding: 3px; border-bottom: 1px solid #333;">{{ $pembayaran->bank ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 3px; font-weight: bold;">Total Biaya:</td>
                    <td style="padding: 3px; border-bottom: 1px solid #333;">Rp {{ number_format($pembayaran->pranotaPerbaikanKontainers->first()?->total_biaya ?? 0, 0, ',', '.') }}</td>
                    <td style="padding: 3px; font-weight: bold;">Nominal Bayar:</td>
                    <td style="padding: 3px; border-bottom: 1px solid #333;">Rp {{ number_format($pembayaran->nominal_pembayaran, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Deskripsi Pekerjaan -->
        @if($pembayaran->pranotaPerbaikanKontainers->first()?->deskripsi_pekerjaan)
        <div class="content-section">
            <div class="section-title">Deskripsi Pekerjaan</div>
            <div style="border: 1px solid #ddd; padding: 5px; min-height: 25px; background: #f9f9f9; font-size: 8px;">
                {{ Str::limit($pembayaran->pranotaPerbaikanKontainers->first()?->deskripsi_pekerjaan, 200) }}
            </div>
        </div>
        @endif

        <!-- Daftar Tagihan Perbaikan Kontainer -->
        <div class="content-section">
            <div class="section-title">Daftar Tagihan Perbaikan Kontainer</div>

                        @if($pembayaran->pranotaPerbaikanKontainers->first()?->perbaikanKontainers?->count() > 0)
                <table class="perbaikan-table compact-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 15%;">No.Tagihan</th>
                            <th style="width: 15%;">Kontainer</th>
                            <th style="width: 10%;">Tanggal</th>
                            <th style="width: 35%;">Deskripsi</th>
                            <th style="width: 20%;">Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembayaran->pranotaPerbaikanKontainers->first()?->perbaikanKontainers ?? [] as $index => $perbaikan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $perbaikan->nomor_tagihan ?? '-' }}</td>
                            <td>{{ $perbaikan->nomor_kontainer ?? '-' }}</td>
                            <td>{{ $perbaikan->tanggal_perbaikan ? \Carbon\Carbon::parse($perbaikan->tanggal_perbaikan)->format('d/m') : '-' }}</td>
                            <td style="font-size: 7px;">{{ Str::limit($perbaikan->deskripsi_perbaikan ?? '-', 40) }}</td>
                            <td style="text-align: right; font-weight: bold;">
                                {{ $perbaikan->realisasi_biaya_perbaikan ? 'Rp ' . number_format($perbaikan->realisasi_biaya_perbaikan, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Total Biaya -->
                <div class="total-section">
                    <div class="total-row total-final">
                        <span>TOTAL BIAYA PERBAIKAN:</span>
                        <span>Rp. {{ number_format($pembayaran->pranotaPerbaikanKontainers->first()?->total_biaya ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            @else
                <div style="text-align: center; color: #666; font-style: italic; padding: 20px; border: 1px solid #ddd;">
                    Tidak ada tagihan perbaikan kontainer yang terkait dengan pranota ini.
                </div>
            @endif
        </div>

        <!-- Keterangan Pembayaran -->
        @if($pembayaran->keterangan)
        <div class="content-section">
            <div class="section-title">Keterangan</div>
            <div style="border: 1px solid #ddd; padding: 5px; min-height: 25px; background: #f9f9f9; font-size: 8px;">
                {{ Str::limit($pembayaran->keterangan, 150) }}
            </div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div style="font-size: 9px;"><strong>Mengetahui</strong></div>
                <div style="font-size: 8px;">Kepala Bagian</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div style="font-size: 9px;"><strong>Pembayar</strong></div>
                <div style="font-size: 8px;">Admin</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div style="font-size: 9px;"><strong>Penerima</strong></div>
                <div style="font-size: 8px;">{{ $pembayaran->pranotaPerbaikanKontainers->first()?->nama_teknisi ?? 'Teknisi' }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="content-section" style="text-align: center; font-size: 7px; color: #666; border-top: 1px solid #ddd; padding-top: 5px;">
            <p>Dicetak: {{ now()->format('d/m/Y H:i:s') }} | {{ config('app.name', 'PT. AYPSIS') }}</p>
        </div>
    </div>

    <script>
        // Auto-focus untuk print
        window.onload = function() {
            // Auto print jika ada parameter print
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('auto_print') === '1') {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        };
    </script>
</body>
</html>
