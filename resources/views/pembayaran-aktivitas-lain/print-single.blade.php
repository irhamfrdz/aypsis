<!DOCTYPE html>
<html lang="id">
@php
    $paperSize = request('paper_size', 'Half-A4');
    $paperMap = [
        'Folio' => [
            'size' => '215.9mm 330.2mm',
            'width' => '215.9mm',
            'height' => '330.2mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '13px',
            'headerH1' => '24px',
            'tableFont' => '11px',
        ],
        'Half-Folio' => [
            'size' => '215.9mm 165.1mm',
            'width' => '215.9mm',
            'height' => '165.1mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '14px',
            'headerH1' => '22px',
            'tableFont' => '12px',
        ],
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '210mm',
            'fontSize' => '13px',
            'headerH1' => '24px',
            'tableFont' => '11px',
        ],
        'Half-A4' => [
            'size' => '210mm 148.5mm',
            'width' => '210mm',
            'height' => '148.5mm',
            'containerWidth' => '210mm',
            'fontSize' => '12px',
            'headerH1' => '18px',
            'tableFont' => '10px',
        ]
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-A4'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Pembayaran Aktivitas Lain - {{ $pembayaranAktivitasLain->nomor }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: {{ $currentPaper['size'] }} portrait;
            margin: 10mm;
        }

        html, body {
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
            font-family: Arial, sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.2;
            color: #333;
            background: white;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: calc({{ $currentPaper['containerWidth'] }} - 20mm);
            padding: 0 10mm;
            margin: 0 auto;
            box-sizing: border-box;
            min-height: calc({{ $currentPaper['height'] }} - 20mm);
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: 800;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .header-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .info-section {
            margin-bottom: 12px;
            font-size: 11px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .info-table td {
            padding: 4px 8px;
            font-size: {{ $currentPaper['tableFont'] }};
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 30%;
            font-weight: 800;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12mm;
            table-layout: fixed;
        }

        .table th,
        .table td {
            border: 1px solid #333;
            padding: 2mm 3mm;
            text-align: left;
            vertical-align: middle;
        }

        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 800;
            font-size: {{ $currentPaper['tableFont'] }};
            text-align: center;
            border: 2px solid #333;
        }

        .table td {
            font-size: {{ $currentPaper['tableFont'] }};
            font-weight: 500;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row td {
            background-color: #e9ecef !important;
            font-weight: 900 !important;
            border: 2px solid #333 !important;
        }

        .signature-section {
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #333;
        }

        .signature-table td {
            width: 33.33%;
            border: 1px solid #333;
            padding: 12px 8px;
            text-align: center;
            vertical-align: top;
            height: 60px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            background: white;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <!-- Paper Size Selector (hidden when printing) -->
    <div class="no-print" style="min-width: 160px; display: flex; gap: 8px; align-items: flex-end;">
        @include('components.paper-selector', ['selectedSize' => $paperSize ?? 'Half-A4'])
        <div style="margin-top: 6px; font-size: 12px; color: #444;">
            <strong>Current: {{ $paperSize }}</strong><br>
            <small>{{ $currentPaper['width'] }} × {{ $currentPaper['height'] }}</small>
        </div>
        <div style="margin-left: 6px;">
            <button id="startPrint" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">Print</button>
        </div>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">

            <div class="header-meta">
                <span><strong>Tanggal: {{ \Carbon\Carbon::parse($pembayaranAktivitasLain->tanggal)->format('d-M-Y') }}</strong></span>
            </div>
            <h1>PERMOHONAN TRANSFER</h1>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td>Nomor</td>
                    <td>: {{ $pembayaranAktivitasLain->nomor }}</td>
                </tr>
                <tr>
                    <td>Jenis Aktivitas</td>
                    <td>: {{ $pembayaranAktivitasLain->jenis_aktivitas }}</td>
                </tr>
                @if($pembayaranAktivitasLain->sub_jenis_kendaraan)
                <tr>
                    <td>Sub Jenis</td>
                    <td>: {{ $pembayaranAktivitasLain->sub_jenis_kendaraan }}</td>
                </tr>
                @endif
                @if($pembayaranAktivitasLain->jenis_aktivitas === 'Pembayaran Adjusment Uang Jalan' && $pembayaranAktivitasLain->jenis_penyesuaian)
                <tr>
                    <td>Jenis Penyesuaian</td>
                    <td>: {{ ucfirst($pembayaranAktivitasLain->jenis_penyesuaian) }}</td>
                </tr>
                @endif
                @if($pembayaranAktivitasLain->nomor_polisi)
                <tr>
                    <td>Nomor Polisi</td>
                    <td>: {{ $pembayaranAktivitasLain->nomor_polisi }}</td>
                </tr>
                @endif
                @if($pembayaranAktivitasLain->nomor_voyage)
                <tr>
                    <td>Nomor Voyage</td>
                    <td>: {{ $pembayaranAktivitasLain->nomor_voyage }}</td>
                </tr>
                @endif
                @if($pembayaranAktivitasLain->penerima)
                <tr>
                    <td>Penerima</td>
                    <td>: {{ $pembayaranAktivitasLain->penerima }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Daftar Invoice yang Dibayar -->
        @php
            // Debug: Check if invoices relation exists
            $hasInvoices = isset($pembayaranAktivitasLain->invoices) && $pembayaranAktivitasLain->invoices->count() > 0;
            $jenisAktivitas = $pembayaranAktivitasLain->jenis_aktivitas ?? '';
            $jenisPenyesuaian = $pembayaranAktivitasLain->jenis_penyesuaian ?? '';
            
            $isPembayaranKapal = stripos($jenisAktivitas, 'Kapal') !== false;
            $isAdjustment = stripos($jenisAktivitas, 'Adjustment') !== false;
            // Check for 'pengembalian penuh' (handle snake_case or spaces)
            $isPengembalianPenuh = $isAdjustment && stripos(str_replace(['_', '-'], ' ', $jenisPenyesuaian), 'pengembalian penuh') !== false;
        @endphp
        
        @if($hasInvoices && $isPembayaranKapal)
        <!-- Daftar Invoice untuk Pembayaran Kapal -->
        <div style="margin-bottom: 12px;">
            <strong style="font-size: {{ $currentPaper['tableFont'] }};">Detail Pembayaran Biaya Kapal:</strong>
            <table class="table" style="margin-top: 6px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 22%;">Nomor Invoice</th>
                        <th style="width: 12%;">Tanggal</th>
                        <th style="width: 13%;">No. Voyage</th>
                        <th style="width: 28%;">Klasifikasi Biaya</th>
                        <th style="width: 20%;">Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalInvoices = 0; @endphp
                    @foreach($pembayaranAktivitasLain->invoices as $index => $invoice)
                        @php $totalInvoices += $invoice->total; @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $invoice->nomor_invoice }}</td>
                            <td class="text-center">{{ $invoice->tanggal_invoice->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $invoice->nomor_voyage ?? '-' }}</td>
                            <td>{{ $invoice->klasifikasiBiaya->nama ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="5" class="text-right"><strong>TOTAL PEMBAYARAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($totalInvoices, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @elseif($hasInvoices && $isPengembalianPenuh)
        <!-- Daftar Invoice untuk Pengembalian Penuh Uang Jalan -->
        <div style="margin-bottom: 12px;">
            <strong style="font-size: {{ $currentPaper['tableFont'] }};">Detail Pengembalian Penuh Uang Jalan:</strong>
            <table class="table" style="margin-top: 6px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">No. Surat Jalan</th>
                        <th style="width: 10%;">Tanggal SJ</th>
                        <th style="width: 12%;">No. Polisi</th>
                        <th style="width: 15%;">Supir</th>
                        <th style="width: 18%;">Tujuan</th>
                        <th style="width: 10%;">Ritase</th>
                        <th style="width: 15%;">Nominal Kembali</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalInvoices = 0; @endphp
                    @foreach($pembayaranAktivitasLain->invoices as $index => $invoice)
                        @php 
                            $totalInvoices += $invoice->total; 
                            $sj = $invoice->suratJalan;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $sj->nomor_surat_jalan ?? '-' }}</td>
                            <td class="text-center">{{ ($sj && $sj->tanggal_surat_jalan) ? \Carbon\Carbon::parse($sj->tanggal_surat_jalan)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $sj->no_plat ?? '-' }}</td>
                            <td>{{ $sj->supir ?? '-' }}</td>
                            <td>{{ $sj->tujuan_pengiriman ?? '-' }}</td>
                            <td class="text-center">{{ $sj->rit ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="7" class="text-right"><strong>TOTAL PENGEMBALIAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($totalInvoices, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @elseif($hasInvoices)
        <!-- Daftar Invoice yang Dibayar (Non-Kapal) -->
        <div style="margin-bottom: 12px;">
            <strong style="font-size: {{ $currentPaper['tableFont'] }};">Daftar Invoice yang Dibayar ({{ $pembayaranAktivitasLain->invoices->count() }} invoice):</strong>
            <table class="table" style="margin-top: 6px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 15%;">No. Invoice</th>
                        <th style="width: 10%;">Tanggal</th>
                        @if(stripos($pembayaranAktivitasLain->jenis_aktivitas, 'Adjustment') !== false)
                            <th style="width: 12%;">No. Surat Jalan</th>
                            <th style="width: 13%;">No. Accurate Sebelumnya</th>
                        @endif
                        <th style="width: 12%;">
                            @if(stripos($pembayaranAktivitasLain->jenis_aktivitas, 'Adjustment') !== false)
                                Tipe Penyesuaian
                            @else
                                Sub Jenis
                            @endif
                        </th>
                        <th style="width: 15%;">Penerima</th>
                        <th style="width: 13%;">Total Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalInvoices = 0; @endphp
                    @foreach($pembayaranAktivitasLain->invoices as $index => $invoice)
                        @php $totalInvoices += $invoice->total; @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $invoice->nomor_invoice }}</td>
                            <td class="text-center">{{ $invoice->tanggal_invoice->format('d/m/Y') }}</td>
                            @if(stripos($pembayaranAktivitasLain->jenis_aktivitas, 'Adjustment') !== false)
                                <td>{{ $invoice->suratJalan->no_surat_jalan ?? '-' }}</td>
                                <td>
                                    @php
                                        $nomorAccurateSebelumnya = '-';
                                        if ($invoice->suratJalan && $invoice->suratJalan->pembayaranPranotaUangJalan) {
                                            $nomorAccurateSebelumnya = $invoice->suratJalan->pembayaranPranotaUangJalan->nomor_accurate ?? '-';
                                        }
                                    @endphp
                                    {{ $nomorAccurateSebelumnya }}
                                </td>
                            @endif
                            <td>
                                @if(stripos($pembayaranAktivitasLain->jenis_aktivitas, 'Adjustment') !== false)
                                    @php
                                        $tipePenyesuaianList = [];
                                        if (!empty($invoice->tipe_penyesuaian)) {
                                            $decodedTipe = json_decode($invoice->tipe_penyesuaian, true);
                                            if (is_array($decodedTipe)) {
                                                foreach ($decodedTipe as $tipe) {
                                                    $tipePenyesuaianList[] = strtoupper($tipe['tipe'] ?? '-');
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ count($tipePenyesuaianList) > 0 ? implode(', ', $tipePenyesuaianList) : '-' }}
                                @else
                                    {{ $invoice->sub_jenis_kendaraan ?? '-' }}
                                @endif
                            </td>
                            <td>{{ $invoice->penerima ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="{{ stripos($pembayaranAktivitasLain->jenis_aktivitas, 'Adjustment') !== false ? '7' : '5' }}" class="text-right"><strong>TOTAL INVOICE</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($totalInvoices, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Keterangan -->
        <div style="margin-bottom: 12px; border: 2px solid #333; padding: 8px; min-height: 40px;">
            <strong>Keterangan:</strong><br>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <div style="margin-bottom: 40px;"><strong>Dibuat Oleh:</strong></div>
                        <div>___________</div>
                    </td>
                    <td>
                        <div style="margin-bottom: 40px;"><strong>Diperiksa Oleh:</strong></div>
                        <div>___________</div>
                    </td>
                    <td>
                        <div style="margin-bottom: 40px;"><strong>Disetujui Oleh:</strong></div>
                        <div>___________</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('startPrint')?.addEventListener('click', function() {
            window.print();
        });
        
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
