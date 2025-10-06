<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Memo Surat Jalan - {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} sampai {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 10px;
            background: white;
            line-height: 1.4;
        }

        .print-container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding: 10px;
            margin-bottom: 20px;
            background: #f8f9fa;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #666;
        }

        .date-range {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 12px;
        }

        .permohonan-item {
            border: 1px solid #000;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .memo-header {
            background: #e9ecef;
            padding: 8px;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }

        .memo-info {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .info-left, .info-right {
            width: 48%;
        }

        .info-item {
            margin-bottom: 3px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .detail-section {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .detail-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px;
        }

        .biaya-section {
            padding: 8px;
            background: #f8f9fa;
        }

        .total-biaya {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            margin-top: 5px;
            padding: 5px;
            background: #fff;
            border: 1px solid #000;
        }

        .separator {
            border-top: 1px dashed #999;
            margin: 10px 0;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }

        @media print {
            body {
                padding: 0;
            }

            .permohonan-item {
                margin-bottom: 10px;
            }

            .separator {
                page-break-after: always;
            }

            .separator:last-child {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="header">
            <h1>PT. AYPSIS INDONESIA</h1>
            <h2>Laporan Memo Surat Jalan</h2>
        </div>

        <div class="date-range">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}<br>
            Total Permohonan: {{ $permohonans->count() }} item
        </div>

        @forelse ($permohonans as $permohonan)
            <div class="permohonan-item">
                <div class="memo-header">
                    Memo: {{ $permohonan->nomor_memo }}
                    @if($permohonan->tanggal_memo)
                        - Tanggal: {{ \Carbon\Carbon::parse($permohonan->tanggal_memo)->format('d/m/Y') }}
                    @endif
                </div>

                <div class="memo-info">
                    <div class="info-left">
                        <div class="info-item">
                            <span class="label">Kegiatan:</span>
                            {{ $kegiatanMap[$permohonan->kegiatan] ?? $permohonan->kegiatan }}
                        </div>
                        <div class="info-item">
                            <span class="label">Supir:</span>
                            {{ $permohonan->supir->nama_lengkap ?? $permohonan->supir->nama_panggilan ?? '-' }}
                        </div>
                        <div class="info-item">
                            <span class="label">Dari - Ke:</span>
                            {{ $permohonan->dari ?? '-' }} - {{ $permohonan->ke ?? '-' }}
                        </div>
                    </div>
                    <div class="info-right">
                        <div class="info-item">
                            <span class="label">Jumlah Kontainer:</span>
                            {{ $permohonan->jumlah_kontainer ?? 0 }}
                        </div>
                        <div class="info-item">
                            <span class="label">Uang Jalan:</span>
                            Rp. {{ number_format($permohonan->jumlah_uang_jalan, 0, ',', '.') }}
                        </div>
                        @if($permohonan->adjustment)
                        <div class="info-item">
                            <span class="label">Adjustment:</span>
                            Rp. {{ number_format($permohonan->adjustment, 0, ',', '.') }}
                        </div>
                        @endif
                    </div>
                </div>

                @if($permohonan->alasan_adjustment)
                <div class="detail-section">
                    <div class="detail-title">Alasan Adjustment:</div>
                    {{ $permohonan->alasan_adjustment }}
                </div>
                @endif

                @if($permohonan->catatan)
                <div class="detail-section">
                    <div class="detail-title">Catatan:</div>
                    {{ $permohonan->catatan }}
                </div>
                @endif

                @if($permohonan->kontainers && $permohonan->kontainers->count() > 0)
                <div class="detail-section">
                    <div class="detail-title">Detail Kontainer:</div>
                    <div class="detail-grid">
                        @foreach($permohonan->kontainers as $kontainer)
                        <div>
                            <strong>{{ $kontainer->nomor_kontainer ?? '-' }}</strong>
                            @if($kontainer->ukuran) ({{ $kontainer->ukuran }}) @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="biaya-section">
                    <div class="total-biaya">
                        TOTAL BIAYA: Rp. {{ number_format($permohonan->total_harga_setelah_adj, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            @if(!$loop->last)
                <div class="separator"></div>
            @endif
        @empty
            <div class="no-data">
                Tidak ada data permohonan dalam rentang tanggal yang dipilih.
            </div>
        @endforelse
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
