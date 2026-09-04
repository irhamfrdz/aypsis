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
    <title>Permintaan Amprahan - PA-{{ str_pad($permohonan->id, 6, '0', STR_PAD_LEFT) }}</title>
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
        
        .header h2 {
            font-size: calc({{ $currentPaper['headerH1'] }} * 0.8);
            font-weight: bold;
            margin-bottom: 2px;
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
            padding: 2px 4px;
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
        }

        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .font-bold { font-weight: bold !important; }

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
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 10px; right: 10px; background: white; padding: 10px; border: 1px solid #ccc; border-radius: 5px; z-index: 1000; display: flex; gap: 8px;">
        @include('components.paper-selector', ['selectedSize' => $paperSize ?? 'Half-A4'])
        <button onclick="window.print()" class="bg-blue-600 text-white px-3 py-2 rounded text-sm" style="background-color:#2563eb; color:white; border:none; padding: 5px 10px; border-radius:3px; cursor:pointer;">Print</button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PERMINTAAN AMPRAHAN KAPAL</h1>
            <h2>PT. ALEXINDO YAKINPRIMA</h2>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div style="display: flex; gap: 20px; align-items: flex-start;">
                <div style="flex: 1;">
                    <table class="info-table">
                        <tr>
                            <td style="width: 35%;">Tanggal</td>
                            <td>: {{ \Carbon\Carbon::parse($permohonan->created_at)->format('d/M/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Nomor Dokumen</td>
                            <td>: PA-{{ str_pad($permohonan->id, 6, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <td>Kapal</td>
                            <td>: {{ $permohonan->kapal->nama_kapal ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div style="flex: 1;">
                    <table class="info-table">
                        <tr>
                            <td style="width: 35%;">Pemohon</td>
                            <td>: {{ $permohonan->user->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Nomor Voyage</td>
                            <td>: {{ $permohonan->nomor_voyage ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: {{ ucfirst($permohonan->status) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Section -->
        <div class="section-header">Daftar Barang yang Diminta</div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Nama Barang</th>
                    <th style="width: 15%;">Jumlah</th>
                    <th style="width: 15%;">Satuan</th>
                    <th style="width: 30%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permohonan->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="text-center font-bold">{{ rtrim(rtrim(number_format($item->jumlah, 2, ',', '.'), '0'), ',') }}</td>
                    <td class="text-center">{{ $item->satuan }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Keterangan -->
        @if($permohonan->keterangan_umum)
        <div style="margin-top: 15px;">
            <div style="font-weight: bold; font-size: {{ $currentPaper['tableFont'] }};">Keterangan Umum:</div>
            <div class="keterangan-box">
                {{ $permohonan->keterangan_umum }}
            </div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="footer">
            <table class="signature-table">
                <tr>
                    <td>
                        <div style="margin-bottom: 40px; font-weight: bold; font-size: {{ $currentPaper['tableFont'] }};">Pemohon,</div>
                        <div style="border-bottom: 1px solid #333; width: 80%; margin: 0 auto; padding-bottom: 2px;">
                            {{ $permohonan->user->name ?? 'ABK / Nahkoda' }}
                        </div>
                    </td>
                    <td>
                        <div style="margin-bottom: 40px; font-weight: bold; font-size: {{ $currentPaper['tableFont'] }};">Mengetahui,</div>
                        <div style="border-bottom: 1px solid #333; width: 80%; margin: 0 auto;"></div>
                    </td>
                    <td>
                        <div style="margin-bottom: 40px; font-weight: bold; font-size: {{ $currentPaper['tableFont'] }};">Disetujui Oleh,</div>
                        <div style="border-bottom: 1px solid #333; width: 80%; margin: 0 auto;"></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
