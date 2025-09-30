<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Bulk Perbaikan Kontainer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            background: white;
        }

        .print-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding: 15px;
            background: #f8f9fa;
            margin-bottom: 20px;
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

        .print-date {
            text-align: right;
            margin-bottom: 20px;
            font-size: 11px;
            color: #666;
        }

        .record-container {
            border: 2px solid #000;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .record-header {
            background: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .perbaikan-info {
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

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .info-item {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            min-width: 150px;
        }

        .info-value {
            flex: 1;
        }

        .content-section {
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .content-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .content-text {
            line-height: 1.5;
        }

        .footer {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
        }

        .signature-section {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin: 40px 0 5px 0;
        }

        @media print {
            body {
                padding: 0;
            }

            .print-container {
                border: none;
            }

            .record-container {
                border: 1px solid #000;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="header">
            <h1>PT. AYPSIS INDONESIA</h1>
            <h2>Laporan Perbaikan Kontainer - Bulk Print</h2>
        </div>

        <div class="print-date">
            Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        </div>

        @forelse($perbaikanKontainers as $perbaikanKontainer)
        <div class="record-container">
            <div class="record-header">
                Record #{{ $loop->iteration }} - {{ $perbaikanKontainer->nomor_kontainer }}
            </div>

            <div class="perbaikan-info">
                <div class="info-section">
                    <h3>Informasi Kontainer</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Nomor Kontainer:</span>
                            <span class="info-value">{{ $perbaikanKontainer->nomor_kontainer }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nomor Tagihan:</span>
                            <span class="info-value">{{ $perbaikanKontainer->nomor_tagihan ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tanggal Perbaikan:</span>
                            <span class="info-value">{{ $perbaikanKontainer->tanggal_perbaikan ? \Carbon\Carbon::parse($perbaikanKontainer->tanggal_perbaikan)->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status Perbaikan:</span>
                            <span class="info-value">{{ $perbaikanKontainer->status_perbaikan }}</span>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h3>Informasi Vendor</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Vendor Bengkel:</span>
                            <span class="info-value">{{ $perbaikanKontainer->vendorBengkel->nama_bengkel ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Dibuat Oleh:</span>
                            <span class="info-value">{{ $perbaikanKontainer->creator->name ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tanggal Dibuat:</span>
                            <span class="info-value">{{ $perbaikanKontainer->created_at ? \Carbon\Carbon::parse($perbaikanKontainer->created_at)->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Terakhir Update:</span>
                            <span class="info-value">{{ $perbaikanKontainer->updated_at ? \Carbon\Carbon::parse($perbaikanKontainer->updated_at)->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-section">
                <h3>Deskripsi Perbaikan</h3>
                <div class="content-text">
                    {{ $perbaikanKontainer->deskripsi_perbaikan ?? '-' }}
                </div>
            </div>

            <div class="content-section">
                <h3>Estimasi Kerusakan Kontainer</h3>
                <div class="content-text">
                    {{ $perbaikanKontainer->estimasi_kerusakan_kontainer ?? '-' }}
                </div>
            </div>

            <div class="content-section">
                <h3>Realisasi Kerusakan</h3>
                <div class="content-text">
                    {{ $perbaikanKontainer->realisasi_kerusakan ?? '-' }}
                </div>
            </div>

            @if($perbaikanKontainer->catatan)
            <div class="content-section">
                <h3>Catatan</h3>
                <div class="content-text">
                    {{ $perbaikanKontainer->catatan }}
                </div>
            </div>
            @endif

            <div class="footer">
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <p>Dibuat Oleh</p>
                        <p>{{ $perbaikanKontainer->creator->name ?? '-' }}</p>
                    </div>
                    @if($perbaikanKontainer->updater && $perbaikanKontainer->updater->id !== $perbaikanKontainer->creator->id)
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <p>Diupdate Oleh</p>
                        <p>{{ $perbaikanKontainer->updater->name ?? '-' }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 50px; color: #666;">
            Tidak ada data perbaikan kontainer untuk dicetak
        </div>
        @endforelse
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
