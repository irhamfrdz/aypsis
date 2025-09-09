<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Memo Surat Jalan - {{ $permohonan->nomor_memo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            background: white;
        }

        .print-container {
            max-width: 800px;
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

        .memo-info {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            background: #fff;
        }

        .memo-left, .memo-right {
            width: 48%;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            align-items: center;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            border-bottom: 1px solid #333;
            min-height: 18px;
            padding-left: 5px;
        }

        .content-section {
            padding: 15px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }

        .kontainer-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .kontainer-table th,
        .kontainer-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        .kontainer-table th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding: 20px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 60px;
            margin-bottom: 10px;
        }

        .total-section {
            background: #f8f9fa;
            border: 2px solid #333;
            padding: 15px;
            margin: 20px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .total-final {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
            }

            .print-container {
                border: none;
                box-shadow: none;
            }

            .no-print {
                display: none !important;
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
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Dokumen</button>

    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <h1>Memo Surat Jalan</h1>
            <h2>{{ config('app.name', 'PT. AYPSIS') }}</h2>
        </div>

        <!-- Memo Information -->
        <div class="memo-info">
            <div class="memo-left">
                <div class="info-row">
                    <span class="info-label">Nomor Memo:</span>
                    <span class="info-value">{{ $permohonan->nomor_memo }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value">{{ $permohonan->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kegiatan:</span>
                    <span class="info-value">{{ $kegiatan->nama_kegiatan ?? $permohonan->kegiatan }}</span>
                </div>
            </div>
            <div class="memo-right">
                <div class="info-row">
                    <span class="info-label">Supir:</span>
                    <span class="info-value">{{ $permohonan->supir->nama_panggilan ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Krani:</span>
                    <span class="info-value">{{ $permohonan->krani->nama_panggilan ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tujuan:</span>
                    <span class="info-value">{{ $permohonan->tujuan }}</span>
                </div>
            </div>
        </div>

        <!-- Kontainer Details -->
        <div class="content-section">
            <div class="section-title">Detail Kontainer</div>
            <table class="kontainer-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Seri Kontainer</th>
                        <th>Ukuran</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @if($permohonan->kontainers && $permohonan->kontainers->count() > 0)
                        @foreach($permohonan->kontainers as $index => $kontainer)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $kontainer->nomor_seri_gabungan }}</td>
                                <td>{{ $kontainer->ukuran }}</td>
                                <td>{{ $kontainer->status }}</td>
                                <td>{{ $kontainer->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" style="text-align: center; color: #666;">Tidak ada kontainer terdaftar</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="info-row">
                <span class="info-label">Jumlah Kontainer:</span>
                <span class="info-value">{{ $permohonan->jumlah_kontainer }} unit</span>
            </div>
        </div>

        <!-- Total Biaya -->
        <div class="total-section">
            <div class="section-title">Rincian Biaya</div>
            <div class="total-row">
                <span>Total Harga Awal:</span>
                <span>Rp. {{ number_format($permohonan->total_harga ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Penyesuaian:</span>
                <span>Rp. {{ number_format(($permohonan->total_harga_setelah_adj ?? 0) - ($permohonan->total_harga ?? 0), 0, ',', '.') }}</span>
            </div>
            <div class="total-row total-final">
                <span>TOTAL SETELAH PENYESUAIAN:</span>
                <span>Rp. {{ number_format($permohonan->total_harga_setelah_adj ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="content-section">
            @if($permohonan->keterangan)
                <div class="section-title">Keterangan</div>
                <div style="border: 1px solid #ddd; padding: 10px; min-height: 60px; background: #f9f9f9;">
                    {{ $permohonan->keterangan }}
                </div>
            @endif
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Mengetahui</strong></div>
                <div>Kepala Bagian</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Dibuat Oleh</strong></div>
                <div>Admin</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Diterima Oleh</strong></div>
                <div>{{ $permohonan->supir->nama_panggilan ?? 'Supir' }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="content-section" style="text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd;">
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | {{ config('app.name') }}</p>
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
