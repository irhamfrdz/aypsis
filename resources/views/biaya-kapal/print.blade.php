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
            'fontSize' => '11px',
            'headerH1' => '18px',
            'tableFont' => '9px',
        ],
        'Half-Folio' => [
            'size' => '215.9mm 165.1mm',
            'width' => '215.9mm',
            'height' => '165.1mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '12px',
            'headerH1' => '18px',
            'tableFont' => '10px',
        ],
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '210mm',
            'fontSize' => '11px',
            'headerH1' => '18px',
            'tableFont' => '9px',
        ],
        'Half-A4' => [
            'size' => '210mm 148.5mm',
            'width' => '210mm',
            'height' => '148.5mm',
            'containerWidth' => '210mm',
            'fontSize' => '9px',
            'headerH1' => '14px',
            'tableFont' => '7px',
        ]
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-A4'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Biaya Kapal - {{ $biayaKapal->nomor_invoice }}</title>
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
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .header-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .info-section {
            margin-bottom: 12px;
            font-size: 9px;
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
            font-weight: bold;
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
            font-weight: bold;
            font-size: {{ $currentPaper['tableFont'] }};
            text-align: center;
            border: 2px solid #333;
        }

        .table td {
            font-size: {{ $currentPaper['tableFont'] }};
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row td {
            background-color: #e9ecef !important;
            font-weight: bold !important;
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
            <div class="header-info">
                <div>
                    <strong>PT. ALEXINDO YAKINPRIMA</strong><br>
                    <span>Jalan Pluit Raya No.8 Blok B No.12, Jakarta Utara 14440</span>
                </div>
            </div>
            <div class="header-meta">
                <span><strong>Tanggal: {{ \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d-M-Y') }}</strong></span>
            </div>
            <h1>PERMOHONAN TRANSFER</h1>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td>Nomor</td>
                    <td>: {{ $biayaKapal->nomor_invoice }}</td>
                </tr>
                <tr>
                    <td>Jenis Biaya</td>
                    <td>: {{ $biayaKapal->jenis_biaya_label }}</td>
                </tr>
                @if($biayaKapal->nomor_referensi)
                <tr>
                    <td>Nomor Referensi</td>
                    <td>: {{ $biayaKapal->nomor_referensi }}</td>
                </tr>
                @endif
                @if($biayaKapal->penerima)
                <tr>
                    <td>Penerima</td>
                    <td>: {{ $biayaKapal->penerima }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Detail Biaya Kapal -->
        <div style="margin-bottom: 12px;">
            <strong style="font-size: {{ $currentPaper['tableFont'] }};">Detail Biaya Kapal:</strong>
            <table class="table" style="margin-top: 6px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">Nama Kapal</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 15%;">No. Voyage</th>
                        @if($biayaKapal->barangDetails && $biayaKapal->barangDetails->count() > 0)
                        <th style="width: 25%;">Detail Barang</th>
                        @else
                        <th style="width: 25%;">Keterangan</th>
                        @endif
                        <th style="width: 15%;">Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $namaKapals = is_array($biayaKapal->nama_kapal) ? $biayaKapal->nama_kapal : [$biayaKapal->nama_kapal];
                        $noVoyages = is_array($biayaKapal->no_voyage) ? $biayaKapal->no_voyage : ($biayaKapal->no_voyage ? [$biayaKapal->no_voyage] : []);
                        $maxCount = max(count($namaKapals), count($noVoyages), 1);
                    @endphp
                    
                    @if($biayaKapal->barangDetails && $biayaKapal->barangDetails->count() > 0)
                        {{-- Jika ada detail barang, tampilkan per barang --}}
                        @foreach($biayaKapal->barangDetails as $index => $detail)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $namaKapals[0] ?? '-' }}{{ count($namaKapals) > 1 ? ', +' . (count($namaKapals) - 1) . ' kapal' : '' }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $noVoyages[0] ?? '-' }}{{ count($noVoyages) > 1 ? ', +' . (count($noVoyages) - 1) : '' }}</td>
                            <td>{{ $detail->pricelistBuruh->barang ?? '-' }} ({{ $detail->jumlah }}x)</td>
                            <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @else
                        {{-- Jika tidak ada detail barang, tampilkan per kapal/voyage --}}
                        @for($i = 0; $i < $maxCount; $i++)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $namaKapals[$i] ?? ($i == 0 ? '-' : '') }}</td>
                            <td class="text-center">{{ $i == 0 ? \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d/m/Y') : '' }}</td>
                            <td class="text-center">{{ $noVoyages[$i] ?? '-' }}</td>
                            <td>{{ $i == 0 ? ($biayaKapal->keterangan ?? '-') : '' }}</td>
                            <td class="text-right">{{ $i == 0 ? 'Rp ' . number_format($biayaKapal->nominal, 0, ',', '.') : '' }}</td>
                        </tr>
                        @endfor
                    @endif
                    
                    <tr class="total-row">
                        <td colspan="5" class="text-right"><strong>TOTAL PEMBAYARAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($biayaKapal->nominal, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Keterangan -->
        <div style="margin-bottom: 12px; border: 2px solid #333; padding: 8px; min-height: 40px;">
            <strong>Keterangan:</strong><br>
            @if($biayaKapal->barangDetails && $biayaKapal->barangDetails->count() > 0)
                {{-- Jika ada detail barang, tampilkan keterangan di sini --}}
                {{ $biayaKapal->keterangan ?? '' }}
            @endif
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
