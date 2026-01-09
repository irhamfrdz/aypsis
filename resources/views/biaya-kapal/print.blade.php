<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Biaya Kapal - {{ $biayaKapal->tanggal->format('d/m/Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #1a1a1a;
        }

        .header p {
            font-size: 14px;
            color: #666;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0e0e0;
            color: #1a1a1a;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            padding: 8px 0;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-green {
            background-color: #d1fae5;
            color: #065f46;
        }

        .nominal-box {
            background-color: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .nominal-label {
            font-size: 12px;
            color: #166534;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .nominal-value {
            font-size: 28px;
            font-weight: bold;
            color: #15803d;
        }

        .keterangan-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .barang-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .barang-table th,
        .barang-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .barang-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }

        .barang-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .barang-table tfoot td {
            background-color: #e5e7eb;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            font-size: 11px;
            color: #666;
        }

        .bukti-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #fef3c7;
            border-radius: 8px;
            border-left: 4px solid #f59e0b;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }

            @page {
                margin: 1cm;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .print-button:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">
        <i class="fas fa-print"></i> Print
    </button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>BIAYA OPERASIONAL KAPAL</h1>
            <p>Detail Pengeluaran Operasional</p>
        </div>

        <!-- Informasi Umum -->
        <div class="section">
            <div class="section-title">Informasi Umum</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Tanggal:</div>
                    <div class="info-value">{{ $biayaKapal->tanggal->format('d/m/Y') }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Nomor Invoice:</div>
                    <div class="info-value"><strong>{{ $biayaKapal->nomor_invoice }}</strong></div>
                </div>

                @if($biayaKapal->nomor_referensi)
                <div class="info-item">
                    <div class="info-label">Nomor Referensi:</div>
                    <div class="info-value">{{ $biayaKapal->nomor_referensi }}</div>
                </div>
                @endif

                <div class="info-item">
                    <div class="info-label">Nama Kapal:</div>
                    <div class="info-value">
                        @php
                            $namaKapals = is_array($biayaKapal->nama_kapal) ? $biayaKapal->nama_kapal : [$biayaKapal->nama_kapal];
                        @endphp
                        @foreach($namaKapals as $index => $kapal)
                            <span class="badge badge-blue">{{ $kapal }}</span>{{ $index < count($namaKapals) - 1 ? ' ' : '' }}
                        @endforeach
                    </div>
                </div>

                @if($biayaKapal->no_voyage && count($biayaKapal->no_voyage) > 0)
                <div class="info-item">
                    <div class="info-label">Nomor Voyage:</div>
                    <div class="info-value">
                        @php
                            $noVoyages = is_array($biayaKapal->no_voyage) ? $biayaKapal->no_voyage : [$biayaKapal->no_voyage];
                        @endphp
                        @foreach($noVoyages as $index => $voyage)
                            <span class="badge badge-green">{{ $voyage }}</span>{{ $index < count($noVoyages) - 1 ? ' ' : '' }}
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="info-item">
                    <div class="info-label">Jenis Biaya:</div>
                    <div class="info-value">
                        <span class="badge badge-blue">{{ $biayaKapal->jenis_biaya_label }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nominal -->
        <div class="section">
            <div class="nominal-box">
                <div class="nominal-label">TOTAL NOMINAL</div>
                <div class="nominal-value">{{ $biayaKapal->formatted_nominal }}</div>
            </div>
        </div>

        <!-- Detail Barang (if Biaya Buruh) -->
        @if($biayaKapal->barangDetails && $biayaKapal->barangDetails->count() > 0)
        <div class="section">
            <div class="section-title">Detail Barang</div>
            <table class="barang-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th>Nama Barang</th>
                        <th style="width: 15%; text-align: center;">Jumlah</th>
                        <th style="width: 20%; text-align: right;">Tarif</th>
                        <th style="width: 20%; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($biayaKapal->barangDetails as $index => $detail)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $detail->pricelistBuruh->barang ?? '-' }}</td>
                        <td style="text-align: center;">{{ $detail->jumlah }}</td>
                        <td style="text-align: right;">Rp {{ number_format($detail->tarif, 0, ',', '.') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;">TOTAL:</td>
                        <td style="text-align: right;">Rp {{ number_format($biayaKapal->nominal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

        <!-- Keterangan -->
        @if($biayaKapal->keterangan)
        <div class="section">
            <div class="section-title">Keterangan</div>
            <div class="keterangan-box">
                {{ $biayaKapal->keterangan }}
            </div>
        </div>
        @endif

        <!-- Bukti -->
        @if($biayaKapal->bukti)
        <div class="bukti-section">
            <strong><i class="fas fa-paperclip"></i> Bukti Dokumen:</strong> Tersedia
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Sistem Manajemen Biaya Operasional Kapal</p>
        </div>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
