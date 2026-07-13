<!DOCTYPE html>
<html lang="id">
@php
    $paperSize = request('paper_size', 'Half-Folio');
    $paperMap = [
        'Folio' => [
            'size' => '215.9mm 330.2mm',
            'width' => '215.9mm',
            'height' => '330.2mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '13px',
            'headerH1' => '20px',
            'tableFont' => '11px',
        ],
        'Half-Folio' => [
            'size' => '165.1mm 215.9mm',
            'width' => '165.1mm',
            'height' => '215.9mm',
            'containerWidth' => '165.1mm',
            'fontSize' => '9px',
            'headerH1' => '14px',
            'tableFont' => '8px',
        ],
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '210mm',
            'fontSize' => '13px',
            'headerH1' => '20px',
            'tableFont' => '11px',
        ],
        'Half-A4' => [
            'size' => '148.5mm 210mm',
            'width' => '148.5mm',
            'height' => '210mm',
            'containerWidth' => '148.5mm',
            'fontSize' => '9px',
            'headerH1' => '14px',
            'tableFont' => '8px',
        ]
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-Folio'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Rekapan Bongkar/Muat Barang Perincian - {{ $namaKapal }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: {{ $currentPaper['size'] }};
            margin: 5mm;
        }

        html, body {
            width: {{ $currentPaper['width'] }};
            font-family: Arial, sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.2;
            color: #000;
            background: white;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: calc({{ $currentPaper['containerWidth'] }} - 10mm);
            padding: 0 5mm;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
            border-bottom: 2px solid #333;
            padding-bottom: 2px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 4px;
            color: #1a1a1a;
        }

        .info-section {
            margin-bottom: 12px;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .info-table td {
            padding: 2px 4px;
            font-size: {{ $currentPaper['tableFont'] }};
            vertical-align: top;
            font-weight: bold;
        }

        .section-header {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: {{ $currentPaper['tableFont'] }};
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
            table-layout: fixed;
        }

        .custom-table th, 
        .custom-table td {
            border: 1px solid #333;
            padding: 1px 4px;
            text-align: left;
            vertical-align: middle;
        }

        .custom-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            font-size: {{ $currentPaper['tableFont'] }};
            text-align: center;
            border: 1.5px solid #333;
        }

        .custom-table td {
            font-size: {{ $currentPaper['tableFont'] }};
            font-weight: bold;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .total-row td {
            background-color: #f0f0f0 !important;
            font-weight: bold !important;
            border: 1.5px solid #333 !important;
        }

        .keterangan-box {
            border: 1.5px solid #333;
            padding: 4px;
            margin-top: 10px;
            min-height: 40px;
        }

        .footer {
            margin-top: 10px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .signature-table td {
            width: 33.33%;
            padding: 5px;
        }

        @media print {
            .no-print { display: none !important; }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        .no-print-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            background: white;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }
    </style>
</head>
<body>
    <div class="no-print-controls no-print">
        @include('components.paper-selector', ['selectedSize' => $paperSize])
        <div style="margin-top: 6px; font-size: 12px; color: #444;">
            <strong>Current: {{ $paperSize }}</strong><br>
            <small>{{ $currentPaper['width'] }} × {{ $currentPaper['height'] }}</small>
        </div>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">Print</button>
        <a href="{{ route('bl.rekap-bongkaran-perincian', ['nama_kapal' => $namaKapal, 'no_voyage' => $noVoyage]) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded text-sm no-underline" style="text-decoration: none;">Kembali</a>
    </div>

    <div class="container">
        <div class="header">
            <h1>REKAPAN BONGKAR/MUAT BARANG PERINCIAN</h1>
        </div>

        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td style="width: 15%;">Nama Kapal</td>
                    <td style="width: 35%;">: {{ $namaKapal }}</td>
                    <td style="width: 15%;">Est Tiba</td>
                    <td>: {{ $estTiba }}</td>
                </tr>
                <tr>
                    <td>Voyage</td>
                    <td>: {{ $noVoyage }}</td>
                    <td>Dari</td>
                    <td>: 
                        @if(Str::contains($noVoyage, 'BJ')) Batam 
                        @elseif(Str::contains($noVoyage, 'PJ')) Pinang
                        @elseif(Str::contains($noVoyage, ['JB', 'JP'])) Jakarta
                        @else - @endif
                    </td>
                </tr>
            </table>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;" colspan="2">Jumlah Barang</th>
                    <th style="width: 65%;">Nama Barang</th>
                    <th style="width: 15%;" colspan="2">Ton / M3</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-right" style="border-right: none;">
                        {{ number_format($item['kuantitas'], 0, ',', '.') }}
                    </td>
                    <td class="text-left" style="border-left: none;">
                        {{ $item['satuan'] }}
                    </td>
                    <td style="font-weight: bold;" title="{{ $item['nama_barang'] }}">
                        {{ Str::limit($item['nama_barang'], 80) }}
                    </td>
                    <td class="text-right" style="border-right: none; width: 75%;">
                        {{ $item['amount'] !== null ? rtrim(rtrim(number_format($item['amount'], 3, ',', '.'), '0'), ',') : '' }}
                    </td>
                    <td class="text-center" style="border-left: none; width: 25%;">
                        {{ $item['unit'] }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data detail.</td>
                </tr>
                @endforelse
                @if($items->count() > 0)
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL</td>
                    <td class="text-right" style="border-right: none;">
                        {{ rtrim(rtrim(number_format($totalAmount, 3, ',', '.'), '0'), ',') }}
                    </td>
                    <td class="text-center" style="border-left: none;">
                        Kgs/m3
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>
