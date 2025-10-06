<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Pembayaran - {{ $pembayaranAktivitasLainnya->nomor_pembayaran }}</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            .no-print {
                display: none;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 24pt;
            margin-bottom: 10px;
            color: #2563eb;
        }

        .header p {
            font-size: 10pt;
            color: #666;
        }

        .title {
            text-align: center;
            margin: 30px 0;
        }

        .title h2 {
            font-size: 18pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-section {
            margin: 20px 0;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 200px;
            padding: 8px 10px;
            font-weight: bold;
            vertical-align: top;
        }

        .info-separator {
            display: table-cell;
            width: 20px;
            padding: 8px 5px;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            padding: 8px 10px;
            vertical-align: top;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft {
            background-color: #e5e7eb;
            color: #374151;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .amount-section {
            background-color: #f3f4f6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }

        .amount-label {
            font-size: 12pt;
            color: #666;
            margin-bottom: 10px;
        }

        .amount-value {
            font-size: 24pt;
            font-weight: bold;
            color: #2563eb;
        }

        .notes-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9fafb;
            border-left: 4px solid #2563eb;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .signature-section {
            margin-top: 60px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 80px;
        }

        .signature-name {
            border-top: 1px solid #333;
            padding-top: 5px;
            display: inline-block;
            min-width: 200px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14pt;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .print-button:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print / Download PDF
    </button>

    <div class="container">
        <div class="header">
            <h1>PT AYPSIS</h1>
            <p>Jl. Contoh No. 123, Jakarta</p>
            <p>Telp: (021) 1234567 | Email: info@aypsis.com</p>
        </div>

        <div class="title">
            <h2>Bukti Pembayaran Aktivitas Lain-lain</h2>
        </div>

        <div class="info-section">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Nomor Pembayaran</div>
                    <div class="info-separator">:</div>
                    <div class="info-value"><strong>{{ $pembayaranAktivitasLainnya->nomor_pembayaran }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Pembayaran</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($pembayaranAktivitasLainnya->tanggal_pembayaran)->format('d F Y') }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Metode Pembayaran</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ ucfirst(str_replace('_', ' ', $pembayaranAktivitasLainnya->metode_pembayaran)) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Referensi</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $pembayaranAktivitasLainnya->referensi_pembayaran ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">
                        @php
                            $statusClass = 'status-' . $pembayaranAktivitasLainnya->status;
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            {{ ucfirst($pembayaranAktivitasLainnya->status) }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dibuat Oleh</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $pembayaranAktivitasLainnya->creator->username ?? '-' }}</div>
                </div>
                @if($pembayaranAktivitasLainnya->approved_by)
                <div class="info-row">
                    <div class="info-label">Disetujui Oleh</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $pembayaranAktivitasLainnya->approver->username ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Persetujuan</div>
                    <div class="info-separator">:</div>
                    <div class="info-value">{{ $pembayaranAktivitasLainnya->approved_at ? \Carbon\Carbon::parse($pembayaranAktivitasLainnya->approved_at)->format('d F Y H:i') : '-' }}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="amount-section">
            <div class="amount-label">Total Pembayaran</div>
            <div class="amount-value">Rp {{ number_format($pembayaranAktivitasLainnya->total_nominal, 0, ',', '.') }}</div>
            <div style="margin-top: 10px; font-size: 10pt; color: #666;">
                <em>{{ ucwords(\App\Helpers\Terbilang::make($pembayaranAktivitasLainnya->total_nominal)) }} Rupiah</em>
            </div>
        </div>

        @if($pembayaranAktivitasLainnya->keterangan)
        <div class="notes-section">
            <div class="notes-title">Keterangan:</div>
            <div>{{ $pembayaranAktivitasLainnya->keterangan }}</div>
        </div>
        @endif

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Dibuat Oleh,</div>
                <div class="signature-name">
                    {{ $pembayaranAktivitasLainnya->creator->username ?? '____________________' }}
                </div>
            </div>
            @if($pembayaranAktivitasLainnya->status == 'approved')
            <div class="signature-box">
                <div class="signature-title">Disetujui Oleh,</div>
                <div class="signature-name">
                    {{ $pembayaranAktivitasLainnya->approver->username ?? '____________________' }}
                </div>
            </div>
            @else
            <div class="signature-box">
                <div class="signature-title">Penerima,</div>
                <div class="signature-name">
                    ____________________
                </div>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>Dokumen ini dicetak pada {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</p>
            <p>{{ $pembayaranAktivitasLainnya->nomor_pembayaran }}</p>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
