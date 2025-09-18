<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Pranota Perbaikan Kontainer - {{ $pranotaPerbaikanKontainer->nomor_pranota ?? 'Belum ada nomor' }}</title>
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

        .info-left, .info-right {
            width: 48%;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            align-items: center;
        }

        .info-label {
            font-weight: bold;
            width: 140px;
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

        .perbaikan-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .perbaikan-table th,
        .perbaikan-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        .perbaikan-table th {
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

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-draft { background: #ffc107; color: #000; }
        .status-approved { background: #28a745; color: #fff; }
        .status-in_progress { background: #17a2b8; color: #fff; }
        .status-completed { background: #6f42c1; color: #fff; }
        .status-cancelled { background: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Dokumen</button>

    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <h1>Pranota Perbaikan Kontainer</h1>
            <h2>{{ config('app.name', 'PT. AYPSIS') }}</h2>
        </div>

        <!-- Pranota Information -->
        <div class="pranota-info">
            <div class="info-left">
                <div class="info-row">
                    <span class="info-label">Nomor Pranota:</span>
                    <span class="info-value">{{ $pranotaPerbaikanKontainer->nomor_pranota ?? 'Belum ada nomor' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Pranota:</span>
                    <span class="info-value">{{ $pranotaPerbaikanKontainer->tanggal_pranota ? \Carbon\Carbon::parse($pranotaPerbaikanKontainer->tanggal_pranota)->format('d/m/Y') : '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nama Teknisi/Vendor:</span>
                    <span class="info-value">{{ $pranotaPerbaikanKontainer->nama_teknisi ?? '-' }}</span>
                </div>
            </div>
            <div class="info-right">
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        @if($pranotaPerbaikanKontainer->status == 'draft')
                            <span class="status-badge status-draft">Draft</span>
                        @elseif($pranotaPerbaikanKontainer->status == 'approved')
                            <span class="status-badge status-approved">Disetujui</span>
                        @elseif($pranotaPerbaikanKontainer->status == 'in_progress')
                            <span class="status-badge status-in_progress">Dalam Proses</span>
                        @elseif($pranotaPerbaikanKontainer->status == 'completed')
                            <span class="status-badge status-completed">Selesai</span>
                        @elseif($pranotaPerbaikanKontainer->status == 'cancelled')
                            <span class="status-badge status-cancelled">Dibatalkan</span>
                        @else
                            <span class="status-badge">{{ ucfirst($pranotaPerbaikanKontainer->status ?? 'Unknown') }}</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dibuat Oleh:</span>
                    <span class="info-value">{{ $pranotaPerbaikanKontainer->creator->name ?? 'Unknown' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Dibuat:</span>
                    <span class="info-value">{{ $pranotaPerbaikanKontainer->created_at ? $pranotaPerbaikanKontainer->created_at->format('d/m/Y H:i') : '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Deskripsi Pekerjaan -->
        @if($pranotaPerbaikanKontainer->deskripsi_pekerjaan)
        <div class="content-section">
            <div class="section-title">Deskripsi Pekerjaan</div>
            <div style="border: 1px solid #ddd; padding: 10px; min-height: 40px; background: #f9f9f9;">
                {{ $pranotaPerbaikanKontainer->deskripsi_pekerjaan }}
            </div>
        </div>
        @endif

        <!-- Daftar Tagihan Perbaikan Kontainer -->
        <div class="content-section">
            <div class="section-title">Daftar Tagihan Perbaikan Kontainer</div>

            @if($pranotaPerbaikanKontainer->perbaikanKontainers->count() > 0)
                <table class="perbaikan-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 15%;">Nomor Tagihan</th>
                            <th style="width: 15%;">Nomor Kontainer</th>
                            <th style="width: 12%;">Tanggal</th>
                            <th style="width: 35%;">Deskripsi Perbaikan</th>
                            <th style="width: 18%;">Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pranotaPerbaikanKontainer->perbaikanKontainers as $index => $perbaikan)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $perbaikan->nomor_tagihan ?? '-' }}</td>
                            <td>{{ $perbaikan->nomor_kontainer ?? '-' }}</td>
                            <td>{{ $perbaikan->tanggal_perbaikan ? \Carbon\Carbon::parse($perbaikan->tanggal_perbaikan)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $perbaikan->deskripsi_perbaikan ?? '-' }}</td>
                            <td style="text-align: right; font-weight: bold;">
                                {{ $perbaikan->realisasi_biaya_perbaikan ? 'Rp ' . number_format($perbaikan->realisasi_biaya_perbaikan, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                        @if($perbaikan->pivot->catatan_item)
                        <tr>
                            <td colspan="6" style="font-style: italic; font-size: 11px; background: #f9f9f9;">
                                <strong>Catatan:</strong> {{ $perbaikan->pivot->catatan_item }}
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>

                <!-- Total Biaya -->
                <div class="total-section">
                    <div class="total-row total-final">
                        <span>TOTAL BIAYA PERBAIKAN:</span>
                        <span>Rp. {{ number_format($pranotaPerbaikanKontainer->total_biaya ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            @else
                <div style="text-align: center; color: #666; font-style: italic; padding: 20px; border: 1px solid #ddd;">
                    Tidak ada tagihan perbaikan kontainer yang terkait dengan pranota ini.
                </div>
            @endif
        </div>

        <!-- Catatan Tambahan -->
        @if($pranotaPerbaikanKontainer->catatan)
        <div class="content-section">
            <div class="section-title">Catatan Tambahan</div>
            <div style="border: 1px solid #ddd; padding: 10px; min-height: 40px; background: #f9f9f9;">
                {{ $pranotaPerbaikanKontainer->catatan }}
            </div>
        </div>
        @endif

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
                <div>{{ $pranotaPerbaikanKontainer->creator->name ?? 'Admin' }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Disetujui Oleh</strong></div>
                <div>Manager</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="content-section" style="text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd;">
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | {{ config('app.name', 'PT. AYPSIS') }}</p>
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