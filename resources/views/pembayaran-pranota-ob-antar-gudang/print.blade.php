<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Pembayaran Pranota OB Antar Gudang - {{ $pembayaran->nomor_pembayaran }}</title>
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
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 2px 0 0 0;
            font-size: 11px;
            color: #444;
        }

        .content-section {
            padding: 8px;
        }

        .section-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 6px;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 3px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 10px;
        }

        .info-table td {
            padding: 3px;
        }

        .info-label {
            font-weight: bold;
            width: 15%;
        }

        .info-value {
            width: 35%;
            border-bottom: 1px solid #ddd;
        }

        .ob-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9px;
        }

        .ob-table th,
        .ob-table td {
            border: 1px solid #333;
            padding: 4px;
            text-align: left;
        }

        .ob-table th {
            background: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            padding: 10px;
        }

        .signature-box {
            text-align: center;
            width: 180px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 45px;
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
            font-size: 10px;
        }

        .total-final {
            font-weight: bold;
            font-size: 11px;
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
            background: #0d9488;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .print-button:hover {
            background: #0f766e;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Dokumen</button>

    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <h1>Bukti Pembayaran Pranota OB Antar Gudang</h1>
            <h2>{{ config('app.name', 'PT. Alexindo YakinPrima Shipping') }}</h2>
        </div>

        <!-- Informasi Pembayaran -->
        <div class="content-section">
            <div class="section-title">Informasi Pembayaran</div>
            <table class="info-table">
                <tr>
                    <td class="info-label">No. Pembayaran:</td>
                    <td class="info-value"><strong>{{ $pembayaran->nomor_pembayaran }}</strong></td>
                    <td class="info-label">Tanggal Kas:</td>
                    <td class="info-value">{{ \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="info-label">No. Accurate:</td>
                    <td class="info-value">{{ $pembayaran->nomor_accurate ?: '-' }}</td>
                    <td class="info-label">Akun Bank:</td>
                    <td class="info-value">{{ $pembayaran->akunBank->nama_akun ?? ($pembayaran->bank ?? '-') }}</td>
                </tr>
                <tr>
                    <td class="info-label">Jenis Transaksi:</td>
                    <td class="info-value">{{ ucfirst($pembayaran->jenis_transaksi) }}</td>
                    <td class="info-label">Akun Biaya:</td>
                    <td class="info-value">{{ $pembayaran->akunCoa->nama_akun ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="info-label">Status:</td>
                    <td class="info-value">{{ ucfirst($pembayaran->status) }}</td>
                    <td class="info-label">Dibuat Oleh:</td>
                    <td class="info-value">{{ $pembayaran->creator->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- Daftar Pranota OB Antar Gudang -->
        <div class="content-section">
            <div class="section-title">Daftar Pranota OB Antar Gudang yang Dibayar</div>

            @php
                $pranotas = $pembayaran->pranota_ob_antar_gudangs;
            @endphp
            @if($pranotas->count() > 0)
                <table class="ob-table">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center;">No</th>
                            <th style="width: 30%;">Nomor Pranota</th>
                            <th style="width: 25%;">Tanggal</th>
                            <th style="width: 20%; text-align: center;">Jumlah Item</th>
                            <th style="width: 20%; text-align: right;">Total Tagihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pranotas as $index => $item)
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td><strong>{{ $item->nomor_pranota }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pranota)->format('d/m/Y') }}</td>
                                <td style="text-align: center;">{{ $item->items->count() }} kontainer</td>
                                <td style="text-align: right; font-weight: bold;">
                                    Rp {{ number_format($item->grand_total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Total section -->
                <div class="total-section">
                    <div class="total-row">
                        <span>Total Tagihan:</span>
                        <span>Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</span>
                    </div>
                    @if($pembayaran->penyesuaian != 0)
                    <div class="total-row">
                        <span>Penyesuaian (Adjustment):</span>
                        <span>{{ $pembayaran->penyesuaian > 0 ? '+' : '' }}Rp {{ number_format($pembayaran->penyesuaian, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="total-row total-final">
                        <span>TOTAL SETELAH PENYESUAIAN:</span>
                        <span>Rp {{ number_format($pembayaran->total_setelah_penyesuaian, 0, ',', '.') }}</span>
                    </div>
                </div>
            @else
                <div style="text-align: center; color: #666; font-style: italic; padding: 20px; border: 1px solid #ddd;">
                    Tidak ada data pranota yang terkait dengan pembayaran ini.
                </div>
            @endif
        </div>

        <!-- Catatan -->
        @if($pembayaran->keterangan || $pembayaran->alasan_penyesuaian)
        <div class="content-section">
            <div class="section-title">Catatan / Keterangan</div>
            <div style="border: 1px solid #ddd; padding: 6px; min-height: 30px; background: #f9f9f9; font-size: 8px;">
                @if($pembayaran->keterangan)
                    <strong>Keterangan:</strong> {{ $pembayaran->keterangan }}
                @endif
                @if($pembayaran->alasan_penyesuaian)
                    @if($pembayaran->keterangan)<br>@endif
                    <strong>Alasan Penyesuaian:</strong> {{ $pembayaran->alasan_penyesuaian }}
                @endif
            </div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div style="font-size: 9px;"><strong>Penyusun</strong></div>
                <div class="signature-line"></div>
                <div style="font-size: 8px;">Kasir / Admin</div>
            </div>
            <div class="signature-box">
                <div style="font-size: 9px;"><strong>Menyetujui</strong></div>
                <div class="signature-line"></div>
                <div style="font-size: 8px;">Manajer Keuangan</div>
            </div>
            <div class="signature-box">
                <div style="font-size: 9px;"><strong>Penerima</strong></div>
                <div class="signature-line"></div>
                <div style="font-size: 8px;">Pelanggan / Pihak Gudang</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="content-section" style="text-align: center; font-size: 7px; color: #666; border-top: 1px solid #ddd; padding-top: 5px; margin-top: 15px;">
            <p>Dicetak: {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }} | {{ config('app.name', 'PT. AYPSIS') }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
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
