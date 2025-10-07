<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $pembayaran->nomor_pembayaran }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            padding: 3px 0;
        }
        .info-value {
            display: table-cell;
            padding: 3px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .table td.number {
            text-align: right;
        }
        .table tfoot td {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 30px;
            border: 2px solid #000;
            padding: 15px;
        }
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-row {
            display: table-row;
        }
        .summary-label {
            display: table-cell;
            width: 70%;
            font-weight: bold;
            padding: 3px 0;
        }
        .summary-value {
            display: table-cell;
            text-align: right;
            padding: 3px 0;
        }
        .final-amount {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #000;
            margin-top: 10px;
            padding-top: 5px;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-left,
        .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 60px;
            border-bottom: 1px solid #000;
            margin: 20px auto;
            width: 200px;
        }
        .signature-label {
            margin-top: 10px;
            font-weight: bold;
        }
        .status-approved {
            color: green;
            font-weight: bold;
        }
        .notes {
            margin-top: 20px;
            font-style: italic;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                max-width: 210mm;
                max-height: 297mm;
                font-size: 10px;
                margin: 0;
                padding: 5mm 5mm 5mm 5mm;
                box-sizing: border-box;
            }
            .header, .info-section, .summary, .notes, .signature-section, .table {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .table th, .table td {
                padding: 4px !important;
            }
            .summary {
                margin-top: 10px !important;
                padding: 8px !important;
            }
            .signature-section {
                margin-top: 20px !important;
            }
            .signature-space {
                height: 40px !important;
                width: 120px !important;
            }
            .no-print {
                display: none !important;
            }
            /* Paksa semua elemen tetap di satu halaman */
            body {
                overflow: hidden !important;
            }
        }
    </style>
</head>
<body>
    <!-- Print button (hidden when printing) -->
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px;">Cetak</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; margin-left: 10px;">Tutup</button>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="company-name">PT. Alexindo Yakin Prima</div>
        <div>Jl. Pluit Raya</div>
        <div>Telp: (021) 1234567 | Email: info@aypsis.com</div>
        <div class="document-title">KWITANSI PEMBAYARAN</div>
    </div>

    <!-- Payment Information -->
    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nomor Pembayaran:</div>
                <div class="info-value">{{ $pembayaran->nomor_pembayaran }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nomor Cetakan:</div>
                <div class="info-value">#{{ $pembayaran->nomor_cetakan }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nomor Invoice:</div>
                <div class="info-value">
                    @foreach($pembayaran->items as $index => $item)
                        {{ $item->pranota->no_invoice ?? '-' }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Nomor Invoice Vendor:</div>
                <div class="info-value">
                    @foreach($pembayaran->items as $index => $item)
                        {{ $item->pranota->no_invoice_vendor ?? '-' }}{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Pembayaran:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Kas:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($pembayaran->tanggal_kas)->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Bank:</div>
                <div class="info-value">{{ $pembayaran->bank }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jenis Transaksi:</div>
                <div class="info-value">{{ ucfirst($pembayaran->jenis_transaksi) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-approved">{{ $pembayaran->status === 'approved' ? 'DISETUJUI' : strtoupper($pembayaran->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Periode Information -->
    <div style="margin-bottom: 10px; padding: 8px; background-color: #fff3cd; border: 1px solid #ffc107;">
        <strong>Informasi Periode:</strong><br>
        @php
            $periodeInfo = [];
            foreach($pembayaran->items as $item) {
                if($item->pranota) {
                    $tagihanItems = $item->pranota->tagihanKontainerSewaItems();
                    foreach($tagihanItems as $tagihan) {
                        if($tagihan->periode) {
                            $key = $tagihan->periode . ' ' . ucfirst($tagihan->masa ?? 'Hari');
                            if(!isset($periodeInfo[$key])) {
                                $periodeInfo[$key] = 0;
                            }
                            $periodeInfo[$key]++;
                        }
                    }
                }
            }
        @endphp
        @if(count($periodeInfo) > 0)
            @foreach($periodeInfo as $periodeLabel => $count)
                {{ $periodeLabel }}: {{ $count }} kontainer{{ !$loop->last ? ', ' : '' }}
            @endforeach
        @else
            Tidak ada data periode
        @endif
    </div>

    <!-- Container Details Table -->
    <table class="table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 18%;">Nomor Kontainer</th>
                <th style="width: 8%;">Size</th>
                <th style="width: 22%;">Masa Sewa</th>
                <th style="width: 12%;">Tarif</th>
                <th style="width: 15%;">DPP</th>
                <th style="width: 20%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($pembayaran->items as $item)
                @if($item->pranota)
                    @php
                        // Get tagihan items from pranota (PranotaTagihanKontainerSewa model)
                        $tagihanItems = $item->pranota->tagihanKontainerSewaItems();
                    @endphp
                    @foreach($tagihanItems as $tagihan)
                        <tr>
                            <td style="text-align: center;">{{ $no++ }}</td>
                            <td style="font-size: 10px;">{{ $tagihan->nomor_kontainer ?? '-' }}</td>
                            <td style="text-align: center; font-size: 10px;">{{ $tagihan->size ?? '-' }}</td>
                            <td style="font-size: 9px;">
                                {{ $tagihan->tanggal_awal ? \Carbon\Carbon::parse($tagihan->tanggal_awal)->format('d/m/Y') : '-' }}
                                -
                                {{ $tagihan->tanggal_akhir ? \Carbon\Carbon::parse($tagihan->tanggal_akhir)->format('d/m/Y') : '-' }}
                            </td>
                            <td style="text-align: center; font-size: 10px;">
                                {{ $tagihan->tarif ?? '-' }}
                            </td>
                            <td class="number" style="font-size: 10px;">
                                Rp {{ number_format((float)($tagihan->dpp ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="number" style="font-size: 10px;">
                                Rp {{ number_format((float)($tagihan->grand_total ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align: right;">Subtotal:</td>
                <td class="number">Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</td>
            </tr>
            @if($pembayaran->total_tagihan_penyesuaian != 0)
                <tr>
                    <td colspan="6" style="text-align: right;">Penyesuaian:</td>
                    <td class="number" style="color: {{ $pembayaran->total_tagihan_penyesuaian >= 0 ? 'green' : 'red' }};">
                        {{ $pembayaran->total_tagihan_penyesuaian >= 0 ? '+' : '' }}Rp {{ number_format($pembayaran->total_tagihan_penyesuaian, 0, ',', '.') }}
                    </td>
                </tr>
            @endif
            @if($pembayaran->dp_amount && $pembayaran->dp_amount > 0)
                <tr>
                    <td colspan="6" style="text-align: right;">Potongan DP:</td>
                    <td class="number" style="color: red;">
                        -Rp {{ number_format($pembayaran->dp_amount, 0, ',', '.') }}
                    </td>
                </tr>
            @endif
            <tr style="font-size: 14px;">
                <td colspan="6" style="text-align: right; font-weight: bold;">TOTAL PEMBAYARAN:</td>
                <td class="number" style="font-weight: bold; font-size: 14px;">
                    Rp {{ number_format($pembayaran->total_tagihan_setelah_penyesuaian ?? $pembayaran->total_pembayaran, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Payment Summary Box -->
    <div class="summary">
        <div class="summary-title">RINGKASAN PEMBAYARAN</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-label">Jumlah Pranota:</div>
                <div class="summary-value">{{ $pembayaran->items->count() }} item</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Pranota:</div>
                <div class="summary-value">Rp {{ number_format($pembayaran->total_pembayaran, 0, ',', '.') }}</div>
            </div>
            @if($pembayaran->total_tagihan_penyesuaian != 0)
                <div class="summary-row">
                    <div class="summary-label">Penyesuaian:</div>
                    <div class="summary-value" style="color: {{ $pembayaran->total_tagihan_penyesuaian >= 0 ? 'green' : 'red' }};">
                        {{ $pembayaran->total_tagihan_penyesuaian >= 0 ? '+' : '' }}Rp {{ number_format($pembayaran->total_tagihan_penyesuaian, 0, ',', '.') }}
                    </div>
                </div>
            @endif
            @if($pembayaran->dp_amount && $pembayaran->dp_amount > 0)
                <div class="summary-row">
                    <div class="summary-label">Potongan DP:</div>
                    <div class="summary-value" style="color: red;">
                        -Rp {{ number_format($pembayaran->dp_amount, 0, ',', '.') }}
                    </div>
                </div>
            @endif
            <div class="summary-row final-amount">
                <div class="summary-label">TOTAL PEMBAYARAN:</div>
                <div class="summary-value">Rp {{ number_format($pembayaran->total_tagihan_setelah_penyesuaian ?? $pembayaran->total_pembayaran, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <!-- Additional Notes -->
    @if($pembayaran->alasan_penyesuaian || $pembayaran->keterangan || ($pembayaran->dp_amount && $pembayaran->dp_amount > 0))
        <div class="notes">
            @if($pembayaran->dp_amount && $pembayaran->dp_amount > 0)
                <p><strong>Informasi DP:</strong>
                    Pembayaran ini menggunakan potongan Down Payment sebesar Rp {{ number_format($pembayaran->dp_amount, 0, ',', '.') }}
                    @if($pembayaran->dpPayment)
                        dari {{ $pembayaran->dpPayment->nomor_pembayaran ?? 'N/A' }}
                    @endif
                </p>
            @endif
            @if($pembayaran->alasan_penyesuaian)
                <p><strong>Alasan Penyesuaian:</strong> {{ $pembayaran->alasan_penyesuaian }}</p>
            @endif
            @if($pembayaran->keterangan)
                <p><strong>Keterangan:</strong> {{ $pembayaran->keterangan }}</p>
            @endif
        </div>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-left">
            <div>Dibuat Oleh:</div>
            <div class="signature-space"></div>
            <div class="signature-label">{{ $pembayaran->pembuatPembayaran->name ?? 'N/A' }}</div>
            <div>{{ \Carbon\Carbon::parse($pembayaran->created_at)->format('d/m/Y') }}</div>
        </div>
        <div class="signature-right">
            <div>Disetujui Oleh:</div>
            <div class="signature-space"></div>
            <div class="signature-label">{{ $pembayaran->penyetujuPembayaran->name ?? 'N/A' }}</div>
            @if($pembayaran->tanggal_persetujuan)
                <div>{{ \Carbon\Carbon::parse($pembayaran->tanggal_persetujuan)->format('d/m/Y') }}</div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>Dokumen ini dicetak secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>{{ $pembayaran->nomor_pembayaran }} - PT. AYPSIS INDONESIA</p>
    </div>
</body>
</html>
