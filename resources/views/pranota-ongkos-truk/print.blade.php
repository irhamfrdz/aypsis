<!DOCTYPE html>
<html lang="id">
@php
    $paperSize = request('paper_size', 'Half-Folio');
    $paperMap = [
        'Half-Folio' => [
            'size' => '215.9mm 165.1mm',
            'width' => '215.9mm',
            'height' => '165.1mm',
            'containerWidth' => '195.9mm', // width - 2*margin
            'fontSize' => '10px',
            'headerH1' => '14px',
            'tableFont' => '10px',
            'signatureBottom' => '3mm'
        ],
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '190mm', // width - 2*margin
            'fontSize' => '11px',
            'headerH1' => '16px',
            'tableFont' => '11px',
            'signatureBottom' => '5mm'
        ],
        'Folio' => [
            'size' => '215.9mm 330.2mm',
            'width' => '215.9mm',
            'height' => '330.2mm',
            'containerWidth' => '195.9mm',
            'fontSize' => '11px',
            'headerH1' => '16px',
            'tableFont' => '11px',
            'signatureBottom' => '5mm'
        ]
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-Folio'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Print Permohonan Transfer - {{ $pranota->no_pranota }}</title>
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

        html {
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.4;
            color: #333;
            background: white;
            position: relative;
            width: {{ $currentPaper['width'] }};
            margin: 0;
            padding: 0;
        }

        .container {
            width: {{ $currentPaper['containerWidth'] }};
            max-width: {{ $currentPaper['containerWidth'] }};
            margin: 0 auto;
            padding: 0;
            position: relative;
            box-sizing: border-box;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: 2px;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 9px;
            color: #666;
        }

        .table-container {
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #333;
            padding: 2px 2px;
            text-align: left;
            vertical-align: middle;
            font-size: {{ $currentPaper['tableFont'] }};
            word-wrap: break-word;
            line-height: 1.2;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .signature-section {
            margin-top: 15px;
            text-align: center;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .no-print {
            display: block;
        }

        @media print {
            @page {
                size: {{ $currentPaper['size'] }} portrait;
                margin: 10mm;
            }

            .no-print {
                display: none !important;
            }

            html, body {
                width: {{ $currentPaper['width'] }};
                margin: 0;
                padding: 0;
            }

            .container {
                width: {{ $currentPaper['containerWidth'] }};
                margin: 0 auto;
                padding: 0;
                box-sizing: border-box;
            }

            th {
                background-color: #ffffff !important;
                color: #000 !important;
            }
        }
    </style>
</head>
<body>
    <!-- Print Instructions Banner (hidden when printing) -->
    <div class="no-print" style="position: fixed; top: 10px; left: 10px; right: 10px; background: #fef3c7; padding: 10px 15px; border: 2px solid #f59e0b; border-radius: 8px; z-index: 1001; box-shadow: 0 4px 6px rgba(0,0,0,0.1); font-size: 11px;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">
            <div style="flex: 1;">
                <strong>⚠️ PENTING - Setting Print:</strong><br>
                1. Scale: <strong>100%</strong> | 2. Orientation: <strong>Portrait</strong> | 3. Margin: <strong>Default</strong><br>
                Gunakan menu di kanan untuk ganti ukuran kertas (A4 / Half-Folio).
            </div>
            <div style="display: flex; gap: 8px;">
                <div style="background: white; padding: 5px 10px; border-radius: 5px; border: 1px solid #d1d5db; display: flex; align-items: center; gap: 5px;">
                    <span style="font-weight: bold; color: #374151;">Ukuran:</span>
                    <a href="?paper_size=Half-Folio" style="text-decoration: none; padding: 2px 8px; border-radius: 4px; font-size: 10px; {{ $paperSize === 'Half-Folio' ? 'background: #007bff; color: white;' : 'background: #e5e7eb; color: #4b5563;' }}">Half-Folio</a>
                    <a href="?paper_size=A4" style="text-decoration: none; padding: 2px 8px; border-radius: 4px; font-size: 10px; {{ $paperSize === 'A4' ? 'background: #007bff; color: white;' : 'background: #e5e7eb; color: #4b5563;' }}">A4</a>
                    <a href="?paper_size=Folio" style="text-decoration: none; padding: 2px 8px; border-radius: 4px; font-size: 10px; {{ $paperSize === 'Folio' ? 'background: #007bff; color: white;' : 'background: #e5e7eb; color: #4b5563;' }}">Folio</a>
                </div>
                <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 5px;">
                    🖨️ CETAK
                </button>
                <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                    TUTUP
                </button>
            </div>
        </div>
    </div>

    <div class="container" style="margin-top: 80px;">
        <!-- Header -->
        <div class="header">
            <h1>PERMOHONAN TRANSFER</h1>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 5px; font-size: 9px;">
                <span><strong>No Pranota : {{ $pranota->no_pranota }}</strong></span>
                <span><strong>Tanggal : {{ $pranota->tanggal_pranota->format('d-m-Y') }}</strong></span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 25%">NO SURAT JALAN</th>
                        <th style="width: 15%">TANGGAL</th>
                        <th style="width: 35%">TUJUAN</th>
                        <th style="width: 20%">NOMINAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pranota->items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center font-bold">{{ $item->no_surat_jalan }}</td>
                            <td class="text-center">{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</td>
                            <td>
                                @php
                                    $tujuan = '-';
                                    if($item->type === 'SuratJalan' && $item->suratJalan) {
                                        $tujuan = $item->suratJalan->tujuanPengambilanRelation->ke ?? $item->suratJalan->tujuan_pengambilan ?? '-';
                                    } elseif($item->type === 'SuratJalanBongkaran' && $item->suratJalanBongkaran) {
                                        $tujuan = $item->suratJalanBongkaran->tujuanPengambilanRelation->ke ?? $item->suratJalanBongkaran->tujuan_pengambilan ?? '-';
                                    }
                                @endphp
                                {{ $tujuan }}
                            </td>
                            <td class="text-right">{{ number_format($item->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    
                    <!-- Subtotal Row -->
                    <tr>
                        <td colspan="4" class="text-right font-bold">Subtotal</td>
                        <td class="text-right font-bold">{{ number_format($pranota->items->sum('nominal'), 0, ',', '.') }}</td>
                    </tr>
                    
                    <!-- Adjustment Row -->
                    @if($pranota->adjustment != 0)
                    <tr>
                        <td colspan="4" class="text-right">Adjustment</td>
                        <td class="text-right">{{ number_format($pranota->adjustment, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    
                    <!-- Total Row -->
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td colspan="4" class="text-right">TOTAL</td>
                        <td class="text-right">{{ number_format($pranota->total_nominal, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="font-size: 9px; margin-bottom: 10px;">
            <strong>Keterangan:</strong> {{ $pranota->keterangan ?? '-' }}
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 5px;">
                        <div style="font-size: 9px; margin-bottom: 30px;">Dibuat Oleh,</div>
                        <div style="border-bottom: 1px dotted #333; width: 80%; margin: 0 auto;"></div>
                        <div style="font-size: 9px; margin-top: 5px;">({{ $pranota->creator->username ?? 'Admin' }})</div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 5px;">
                        <div style="font-size: 9px; margin-bottom: 30px;">Diketahui Oleh,</div>
                        <div style="border-bottom: 1px dotted #333; width: 80%; margin: 0 auto;"></div>
                        <div style="font-size: 9px; margin-top: 5px;">(Manager)</div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top; padding: 5px;">
                        <div style="font-size: 9px; margin-bottom: 30px;">Disetujui Oleh,</div>
                        <div style="border-bottom: 1px dotted #333; width: 80%; margin: 0 auto;"></div>
                        <div style="font-size: 9px; margin-top: 5px;">(Finance)</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
