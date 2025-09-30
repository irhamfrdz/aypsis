<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Perbaikan Kontainer - {{ $perbaikanKontainer->nomor_kontainer }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
            background: white;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 5mm;
            background: white;
            border: 1px solid #e5e7eb;
        }

        /* Header Section */
        .header {
            text-align: center;
            border-bottom: 2px solid #1f2937;
            padding-bottom: 6px;
            margin-bottom: 8px;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background: #3b82f6;
        }

        .company-name {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .document-title {
            font-size: 10px;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .document-subtitle {
            font-size: 8px;
            color: #9ca3af;
            font-style: italic;
        }

        /* Information Tables */
        .info-section {
            margin-bottom: 8px;
        }

        .info-section h3 {
            font-size: 9px;
            font-weight: bold;
            color: #1f2937;
            background: #f3f4f6;
            padding: 4px 8px;
            border-left: 3px solid #3b82f6;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 8px;
        }

        .info-table td {
            padding: 3px 4px;
            border: 1px solid #e5e7eb;
        }

        .info-table .label {
            font-weight: bold;
            background: #f9fafb;
            width: 35%;
            color: #374151;
            border-right: 1px solid #e5e7eb;
        }

        .info-table .value {
            background: white;
            color: #111827;
        }

        /* Content Sections */
        .content-section {
            margin-bottom: 8px;
        }

        .content-section h3 {
            font-size: 9px;
            font-weight: bold;
            color: #1f2937;
            background: #f3f4f6;
            padding: 4px 8px;
            border-left: 3px solid #10b981;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-box {
            border: 1px solid #e5e7eb;
            border-radius: 3px;
            padding: 4px;
            background: white;
            min-height: 20px;
            line-height: 1.3;
            font-size: 8px;
        }

        .content-box:empty::before {
            content: "-";
            color: #9ca3af;
            font-style: italic;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 6px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-belum-masuk-pranota {
            background: #fef3c7;
            color: #d97706;
            border: 1px solid #f59e0b;
        }

        .status-sudah-masuk-pranota {
            background: #dbeafe;
            color: #2563eb;
            border: 1px solid #3b82f6;
        }

        .status-sudah-dibayar {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        /* Footer */
        .footer {
            margin-top: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }

        .signature-section {
            display: flex;
            justify-content: space-around;
            margin-top: 6px;
        }

        .signature-box {
            text-align: center;
            width: 120px;
        }

        .signature-line {
            border-bottom: 1px solid #374151;
            margin: 10px 0 2px 0;
            position: relative;
        }

        .signature-label {
            font-size: 8px;
            color: #6b7280;
            font-weight: 600;
        }

        .signature-name {
            font-size: 8px;
            color: #111827;
            margin-top: 1px;
        }

        /* Two Column Layout for Info Sections */
        .info-row {
            display: flex;
            gap: 8px;
            margin-bottom: 6px;
        }

        .info-column {
            flex: 1;
        }

        /* Print Styles */
        @media print {
            body {
                font-size: 7px;
                line-height: 1.2;
            }

            .print-container {
                padding: 2mm;
                border: none;
                box-shadow: none;
            }

            .info-table {
                font-size: 7px;
            }

            .content-box {
                font-size: 7px;
            }

            .header {
                margin-bottom: 4px;
            }

            .info-section {
                margin-bottom: 4px;
            }

            .content-section {
                margin-bottom: 4px;
            }
        }

        @page {
            size: A4;
            margin: 3mm;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">PT. AYPSIS INDONESIA</div>
            <div class="document-title">Laporan Perbaikan Kontainer</div>
            <div class="document-subtitle">Nomor: {{ $perbaikanKontainer->nomor_kontainer }}</div>
        </div>

        <!-- Basic Information & Vendor Info in 2 columns -->
        <div class="info-row">
            <div class="info-column">
                <div class="info-section">
                    <h3>Informasi Dasar</h3>
                    <table class="info-table">
                        <tr>
                            <td class="label">Nomor Kontainer</td>
                            <td class="value">{{ $perbaikanKontainer->nomor_kontainer ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Nomor Tagihan</td>
                            <td class="value">{{ $perbaikanKontainer->nomor_tagihan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tanggal Perbaikan</td>
                            <td class="value">{{ $perbaikanKontainer->tanggal_perbaikan ? \Carbon\Carbon::parse($perbaikanKontainer->tanggal_perbaikan)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tanggal Selesai</td>
                            <td class="value">{{ $perbaikanKontainer->tanggal_selesai ? \Carbon\Carbon::parse($perbaikanKontainer->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status</td>
                            <td class="value">
                                <span class="status-badge status-{{ str_replace('_', '-', $perbaikanKontainer->status ?? 'belum-masuk-pranota') }}">
                                    {{ $perbaikanKontainer->status_label ?? 'Belum Masuk Pranota' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="info-column">
                <div class="info-section">
                    <h3>Vendor & Administrasi</h3>
                    <table class="info-table">
                        <tr>
                            <td class="label">Vendor Bengkel</td>
                            <td class="value">{{ $perbaikanKontainer->vendorBengkel->nama_bengkel ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Dibuat Oleh</td>
                            <td class="value">{{ $perbaikanKontainer->creator->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tanggal Dibuat</td>
                            <td class="value">{{ $perbaikanKontainer->created_at ? \Carbon\Carbon::parse($perbaikanKontainer->created_at)->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Terakhir Update</td>
                            <td class="value">{{ $perbaikanKontainer->updated_at ? \Carbon\Carbon::parse($perbaikanKontainer->updated_at)->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        @if($perbaikanKontainer->updater && $perbaikanKontainer->updater->id !== $perbaikanKontainer->creator->id)
                        <tr>
                            <td class="label">Diupdate Oleh</td>
                            <td class="value">{{ $perbaikanKontainer->updater->name ?? '-' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Cost Information -->
        <div class="info-section">
            <h3>Informasi Biaya</h3>
            <table class="info-table">
                <tr>
                    <td class="label">Estimasi Biaya</td>
                    <td class="value">{{ $perbaikanKontainer->estimasi_biaya_perbaikan ? 'Rp ' . number_format((float) $perbaikanKontainer->estimasi_biaya_perbaikan, 0, ',', '.') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Realisasi Biaya</td>
                    <td class="value">{{ $perbaikanKontainer->realisasi_biaya_perbaikan ? 'Rp ' . number_format((float) $perbaikanKontainer->realisasi_biaya_perbaikan, 0, ',', '.') : '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- Description Sections in 2 columns -->
        <div class="info-row">
            <div class="info-column">
                <div class="content-section">
                    <h3>Deskripsi Perbaikan</h3>
                    <div class="content-box">
                        {{ $perbaikanKontainer->deskripsi_perbaikan ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="info-column">
                <div class="content-section">
                    <h3>Estimasi Kerusakan</h3>
                    <div class="content-box">
                        {{ $perbaikanKontainer->estimasi_kerusakan_kontainer ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Realization Section -->
        <div class="content-section">
            <h3>Realisasi Kerusakan</h3>
            <div class="content-box">
                {{ $perbaikanKontainer->realisasi_kerusakan ?? '-' }}
            </div>
        </div>

        <!-- Notes -->
        @if($perbaikanKontainer->catatan)
        <div class="content-section">
            <h3>Catatan Tambahan</h3>
            <div class="content-box">
                {{ $perbaikanKontainer->catatan }}
            </div>
        </div>
        @endif

        <!-- Footer with Signatures -->
        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Dibuat Oleh</div>
                    <div class="signature-name">{{ $perbaikanKontainer->creator->name ?? '-' }}</div>
                </div>

                @if($perbaikanKontainer->updater && $perbaikanKontainer->updater->id !== $perbaikanKontainer->creator->id)
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Diperiksa Oleh</div>
                    <div class="signature-name">{{ $perbaikanKontainer->updater->name ?? '-' }}</div>
                </div>
                @endif

                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-label">Disetujui Oleh</div>
                    <div class="signature-name">____________________</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Auto-print when page loads
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
