<!DOCTYPE html>
<html lang="id">
@php
    // Calculate Vendor first to determine default paper size
    $vendorDisplay = $biayaKapal->nama_vendor ?? ($biayaKapal->perijinanDetails->pluck('vendor')->filter()->unique()->values()->first() ?? '-');
    
    $paperSize = request('paper_size', 'A4');
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
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['A4'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Invoice Biaya Perijinan - {{ $biayaKapal->nomor_invoice }}</title>
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
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .section-header {
            font-weight: bold;
            margin: 8px 0 4px 0;
            text-decoration: underline;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: {{ $currentPaper['tableFont'] }};
            margin-bottom: 10px;
        }

        .custom-table th, .custom-table td {
            border: 1px solid #333;
            padding: 4px;
            text-align: left;
        }

        .custom-table th {
            background-color: #f2f2f2 !important;
            font-weight: bold;
            text-align: center;
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
            <small>{{ $currentPaper['width'] }} &times; {{ $currentPaper['height'] }}</small>
        </div>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">Print</button>
        <a href="{{ route('biaya-kapal.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded text-sm no-underline" style="text-decoration: none;">Kembali</a>
    </div>

    <div class="container">
        <div class="header">
            <h1>PERMOHONAN TRANSFER</h1>
        </div>
        
        @php
            $penerimaDisplay = $biayaKapal->penerima ?? ($biayaKapal->perijinanDetails->pluck('penerima')->filter()->unique()->values()->first() ?? '-');
            $rekeningDisplay = $biayaKapal->nomor_rekening ?? ($biayaKapal->perijinanDetails->pluck('nomor_rekening')->filter()->unique()->values()->first() ?? '-');
            
            $totalGrandTotal = $biayaKapal->perijinanDetails->sum('grand_total');
        @endphp

        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td style="width: 15%;">Nomor</td>
                    <td style="width: 35%;">: {{ $biayaKapal->nomor_invoice }}</td>
                    <td style="width: 15%;">Tanggal</td>
                    <td>: {{ $biayaKapal->tanggal ? $biayaKapal->tanggal->format('d/M/Y') : '-' }}</td>
                </tr>
                <tr>
                    <td>Penerima</td>
                    <td>: {{ $penerimaDisplay }}</td>
                    <td>Vendor</td>
                    <td>: {{ $vendorDisplay }}</td>
                </tr>
                <tr>
                    <td>No. Rekening</td>
                    <td>: {{ $rekeningDisplay }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>

        <div class="section-header">Detail Biaya Perijinan:</div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 50%;">Referensi / Keterangan</th>
                    <th style="width: 25%;">Nomor Voyage</th>
                    <th style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($biayaKapal->perijinanDetails as $detail)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>
                            <div class="font-bold">{{ $detail->nama_kapal }}</div>
                            @if($detail->nomor_referensi)
                                <div style="font-size: 0.9em; color: #333;">Ref: {{ $detail->nomor_referensi }}</div>
                            @endif
                            @if($detail->details->count() > 0)
                                <div style="font-size: 0.85em; color: #555; margin-top: 2px;">
                                    Items: {{ $detail->details->map(fn($item) => ($item->pricelist->nama ?? $item->nama_perijinan))->join(', ') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">{{ $detail->no_voyage }}</td>
                        <td class="text-right font-bold">
                            Rp {{ number_format($detail->grand_total, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                
                <tr class="total-row">
                    <td colspan="3" class="text-right">GRAND TOTAL</td>
                    <td class="text-right">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>



        @if($biayaKapal->keterangan)
        <div class="section-header">Keterangan:</div>
        <div class="keterangan-box">
            {{ $biayaKapal->keterangan }}
        </div>
        @endif

        <div class="footer">
            <table class="signature-table">
                <tr>
                    <td>Dibuat Oleh,</td>
                    <td>Diperiksa Oleh,</td>
                    <td>Diketahui Oleh,</td>
                </tr>
                <tr style="height: 60px;">
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>(..........................)</td>
                    <td>(..........................)</td>
                    <td>(..........................)</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
