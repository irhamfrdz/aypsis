<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Permohonan Transfer - {{ $pranotaUangJalan->nomor_pranota }}</title>
    <style>
        @media print {
            @page {
                margin: 15mm;
                size: A4;
            }
            .no-print {
                display: none !important;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .table-container {
            margin: 20px 0;
        }

        .table-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #495057;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
            font-size: 9px;
            word-wrap: break-word;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: 600;
        }

        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 11px;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #333;
            padding-top: 8px;
            margin-top: 10px;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            font-size: 10px;
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .print-button:hover {
            background: #0056b3;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-approved {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-unpaid {
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print Button -->
        <button class="print-button no-print" onclick="window.print()">
            🖨️ Cetak Permohonan
        </button>

        <!-- Header -->
        <div class="header">
            <h1>Permohonan Transfer</h1>
        </div>

        <!-- Pranota Info -->
        <div style="margin-bottom: 20px; font-size: 12px;">
            <p><strong>No Pranota: {{ $pranotaUangJalan->nomor_pranota }}</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Tgl Uang Jalan: {{ $pranotaUangJalan->tanggal_pranota->format('d-m-Y') }}</strong></p>
        </div>

        <!-- Uang Jalan Table -->
        <div class="table-container">
            <div class="table-title">Daftar Uang Jalan</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 3%">No</th>
                        <th style="width: 11%">Nomor Surat Jalan</th>
                        <th style="width: 11%">Nomor Uang Jalan</th>
                        <th style="width: 9%">Barang</th>
                        <th style="width: 7%">NIK</th>
                        <th style="width: 9%">Supir</th>
                        <th style="width: 11%">Pengirim</th>
                        <th style="width: 11%">Tujuan</th>
                        <th style="width: 9%">No Kas Bank</th>
                        <th style="width: 9%">Tanggal Tanda Terima</th>
                        <th style="width: 10%" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pranotaUangJalan->uangJalans as $index => $uangJalan)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                @if($uangJalan->suratJalan)
                                    {{ $uangJalan->suratJalan->no_surat_jalan }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="font-bold">{{ $uangJalan->nomor_uang_jalan }}</td>
                            <td>
                                @if($uangJalan->suratJalan)
                                    {{ $uangJalan->suratJalan->jenis_barang ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($uangJalan->suratJalan && $uangJalan->suratJalan->supir_nik)
                                    {{ $uangJalan->suratJalan->supir_nik }}
                                @elseif($uangJalan->suratJalan && $uangJalan->suratJalan->kenek_nik)
                                    {{ $uangJalan->suratJalan->kenek_nik }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($uangJalan->suratJalan)
                                    {{ $uangJalan->suratJalan->supir ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($uangJalan->suratJalan)
                                    {{ $uangJalan->suratJalan->pengirim ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($uangJalan->suratJalan)
                                    {{ $uangJalan->suratJalan->tujuan_pengambilan ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $uangJalan->nomor_kas_bank ?? '-' }}
                            </td>
                            <td class="text-center">
                                @if($uangJalan->suratJalan && $uangJalan->suratJalan->tanggal_tanda_terima)
                                    {{ $uangJalan->suratJalan->tanggal_tanda_terima->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right font-bold">
                                Rp {{ number_format($uangJalan->jumlah_total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <span>Jumlah Item:</span>
                <span>{{ $pranotaUangJalan->jumlah_uang_jalan }} uang jalan</span>
            </div>
            <div class="summary-row">
                <span>Subtotal Uang Jalan:</span>
                <span>Rp {{ number_format($pranotaUangJalan->total_amount, 0, ',', '.') }}</span>
            </div>
            @if($pranotaUangJalan->penyesuaian != 0)
            <div class="summary-row">
                <span>Penyesuaian:</span>
                <span>Rp {{ number_format($pranotaUangJalan->penyesuaian, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="summary-row total">
                <span>TOTAL AKHIR:</span>
                <span>Rp {{ number_format($pranotaUangJalan->total_with_penyesuaian, 0, ',', '.') }}</span>
            </div>
            @if($pranotaUangJalan->keterangan_penyesuaian)
            <div class="summary-row">
                <span>Keterangan Penyesuaian:</span>
                <span>{{ $pranotaUangJalan->keterangan_penyesuaian }}</span>
            </div>
            @endif
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Dibuat oleh,</p>
                <div class="signature-line">
                    {{ $pranotaUangJalan->creator->name ?? 'N/A' }}
                </div>
            </div>
            <div class="signature-box">
                <p>Disetujui oleh,</p>
                <div class="signature-line">
                    (....................................)
                </div>
            </div>
            <div class="signature-box">
                <p>Diterima oleh,</p>
                <div class="signature-line">
                    (....................................)
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 40px; text-align: center; font-size: 9px; color: #666;">
            <p>Dokumen ini dicetak pada {{ now()->format('d F Y H:i:s') }}</p>
            <p>Halaman ini adalah salinan resmi dari sistem Aypsis</p>
        </div>
    </div>

    <script>
        // Auto focus for better printing experience
        window.onload = function() {
            window.focus();
        };

        // Optional: Auto print when page loads (uncomment if needed)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>