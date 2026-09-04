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
    <title>Biaya Bensin - BBS-{{ str_pad($biayaBensin->id, 6, '0', STR_PAD_LEFT) }}</title>
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

        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .font-bold { font-weight: bold !important; }

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
            <h1>PERMOHONAN TRANSFER</h1>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div style="display: flex; gap: 20px; align-items: flex-start;">
                <div style="flex: 1;">
                    <table class="info-table">
                        <tr>
                            <td style="width: 35%;">Tanggal</td>
                            <td>: {{ \Carbon\Carbon::parse($biayaBensin->tanggal)->format('d/M/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Nomor</td>
                            <td>: BBS-{{ str_pad($biayaBensin->id, 6, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <td>Kendaraan</td>
                            <td>: {{ $biayaBensin->mobil->nopol ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div style="flex: 1;">
                    <table class="info-table">
                        <tr>
                            <td style="width: 35%;">Penerima</td>
                            <td>: {{ $biayaBensin->supir->nama_karyawan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Nomor Kartu</td>
                            <td>: {{ $biayaBensin->nomor_kartu ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Section -->
        <div class="section-header">Rincian Pembayaran Biaya Bensin</div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Kendaraan</th>
                    <th style="width: 25%;">Supir</th>
                    <th style="width: 10%;">Liter</th>
                    <th style="width: 15%;">Harga/Liter</th>
                    <th style="width: 25%;">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td class="text-center">{{ $biayaBensin->mobil->nopol ?? '-' }}</td>
                    <td class="text-center">{{ $biayaBensin->supir->nama_karyawan ?? '-' }}</td>
                    <td class="text-right">{{ number_format($biayaBensin->liter, 2, ',', '.') }} L</td>
                    <td class="text-right">Rp {{ number_format($biayaBensin->harga_per_liter, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($biayaBensin->biaya, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-right font-bold" style="padding-right: 10px;">TOTAL</td>
                    <td class="text-right font-bold">Rp {{ number_format($biayaBensin->biaya, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Keterangan -->
        <div style="margin-top: 15px;">
            <div style="font-weight: bold; font-size: {{ $currentPaper['tableFont'] }};">Keterangan:</div>
            <div class="keterangan-box">
                KM Awal: {{ number_format($biayaBensin->km_awal, 0, ',', '.') }} | KM Akhir: {{ number_format($biayaBensin->km_akhir, 0, ',', '.') }} <br>
                {{ $biayaBensin->keterangan ?? '-' }}
            </div>
        </div>

        <!-- Signatures -->
        <div class="footer">
            <table class="signature-table">
                <tr>
                    <td>
                        <div style="margin-bottom: 40px; font-weight: bold; font-size: {{ $currentPaper['tableFont'] }};">Menyetujui,</div>
                        <div style="border-bottom: 1px solid #333; width: 80%; margin: 0 auto;"></div>
                    </td>
                    <td>
                        <div style="margin-bottom: 40px; font-weight: bold; font-size: {{ $currentPaper['tableFont'] }};">Mengetahui,</div>
                        <div style="border-bottom: 1px solid #333; width: 80%; margin: 0 auto;"></div>
                    </td>
                    <td>
                        <div style="margin-bottom: 40px; font-weight: bold; font-size: {{ $currentPaper['tableFont'] }};">Pemohon,</div>
                        <div style="border-bottom: 1px solid #333; width: 80%; margin: 0 auto; padding-bottom: 2px;">
                            {{ $biayaBensin->creator->name ?? 'Admin' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
