<!DOCTYPE html>
<html lang="id">
@php
    $demurrageDetails = $biayaKapal->demurrageDetails;
    $vendorDisplay = $biayaKapal->nama_vendor ?? ($demurrageDetails->pluck('vendor')->filter()->unique()->values()->first() ?? '-');
    $isAbqori = str_contains(strtoupper($vendorDisplay), 'ABQORI');

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
    <title>Invoice Biaya Demurrage - {{ $biayaKapal->nomor_invoice }}</title>
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
            background-color: #eee;
            padding: 2px 5px;
            border: 1px solid #333;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
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
            <small>{{ $currentPaper['width'] }} &times; {{ $currentPaper['height'] }}</small>
        </div>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">Print</button>
        <a href="{{ route('biaya-kapal.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded text-sm no-underline" style="text-decoration: none;">Kembali</a>
    </div>

    <div class="container">
        <div class="header">
            <h1>PERMOHONAN TRANSFER - BIAYA DEMURRAGE</h1>
        </div>
        
        @php
            $penerimaDisplay = $biayaKapal->penerima ?? ($demurrageDetails->pluck('penerima')->filter()->unique()->values()->first() ?? '-');
            $rekeningDisplay = $biayaKapal->nomor_rekening ?? ($demurrageDetails->pluck('rekening')->filter()->unique()->values()->first() ?? '-');
        @endphp

        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td style="width: 15%;">Nomor</td>
                    <td style="width: 35%;">: {{ $biayaKapal->nomor_invoice }}</td>
                    <td style="width: 15%;">Tanggal</td>
                    <td>: {{ $biayaKapal->tanggal->format('d/M/Y') }}</td>
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
                    <td>Jenis Biaya</td>
                    <td>: {{ $biayaKapal->klasifikasiBiaya->nama ?? 'BIAYA DEMURRAGE' }}</td>
                </tr>
            </table>
        </div>

        @php $grandTotalSeluruhnya = 0; @endphp

        @foreach($demurrageDetails as $detail)
            <div class="section-header">
                {{ $loop->iteration }}. KAPAL: {{ $detail->kapal ?? '-' }} | VOYAGE: {{ $detail->voyage ?? '-' }} | LOKASI: {{ $detail->lokasi ?? '-' }}
            </div>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 40%;">Nomor Kontainer</th>
                        <th style="width: 25%;">Size</th>
                        <th style="width: 30%;">Hari</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $kontainers = is_array($detail->kontainer_ids) ? $detail->kontainer_ids : [];
                    @endphp
                    @forelse($kontainers as $idx => $kontainer)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center">{{ $kontainer['nomor_kontainer'] ?? '-' }}</td>
                        <td class="text-center">{{ $kontainer['size'] ?? '-' }}</td>
                        <td class="text-center">{{ $kontainer['hari'] ?? 0 }} Hari</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data kontainer.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <table class="custom-table" style="margin-left: auto; width: 60%;">
                <tbody>
                    <tr>
                        <td style="width: 60%; background-color: #f9f9f9;">Subtotal</td>
                        <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($detail->ppn > 0)
                    <tr>
                        <td style="background-color: #f9f9f9;">PPN</td>
                        <td class="text-right">Rp {{ number_format($detail->ppn, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($detail->pph > 0)
                    <tr>
                        <td style="background-color: #f9f9f9;">PPh 2%</td>
                        <td class="text-right">- Rp {{ number_format($detail->pph, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($detail->biaya_materai > 0)
                    <tr>
                        <td style="background-color: #f9f9f9;">Biaya Materai</td>
                        <td class="text-right">Rp {{ number_format($detail->biaya_materai, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($detail->adjustment != 0)
                    <tr>
                        <td style="background-color: #f9f9f9;">Adjustment @if($detail->notes_adjustment)<br><small style="font-weight: normal; color: #666;">({{ $detail->notes_adjustment }})</small>@endif</td>
                        <td class="text-right">{{ $detail->adjustment < 0 ? '-' : '' }} Rp {{ number_format(abs($detail->adjustment), 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td style="border: 1.5px solid #333 !important;">TOTAL SEKSI INI</td>
                        <td class="text-right" style="border: 1.5px solid #333 !important;">Rp {{ number_format($detail->total_biaya, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            @php $grandTotalSeluruhnya += $detail->total_biaya; @endphp
            <div style="margin-bottom: 20px;"></div>
        @endforeach

        <table class="custom-table" style="margin-top: 10px;">
            <tr class="total-row" style="font-size: 1.2em;">
                <td class="text-right" style="width: 70%; padding: 8px; border: 2px solid #000 !important;">GRAND TOTAL PEMBAYARAN</td>
                <td class="text-right" style="padding: 8px; border: 2px solid #000 !important;">Rp {{ number_format($grandTotalSeluruhnya, 0, ',', '.') }}</td>
            </tr>
        </table>
        
        <!-- KETERANGAN BOX -->
        <div class="keterangan-box">
            <strong style="font-size: 10px;">Keterangan / Catatan:</strong><br>
            <div style="font-size: 10px; margin-top: 5px;">
                {!! nl2br(e($biayaKapal->keterangan)) !!}
            </div>
        </div>
        
        <!-- FOOTER SIGNATURES -->
        <div class="footer" style="margin-top: 30px;">
            <table class="signature-table">
                <tr>
                    <td><strong>Dibuat Oleh:</strong></td>
                    <td><strong>Diperiksa Oleh:</strong></td>
                    <td><strong>Disetujui Oleh:</strong></td>
                </tr>
                <tr>
                    <td style="height: 70px;"></td>
                    <td style="height: 70px;"></td>
                    <td style="height: 70px;"></td>
                </tr>
                <tr>
                    <td>( {{ $biayaKapal->creator->name ?? '__________' }} )</td>
                    <td>( __________ )</td>
                    <td>( {{ $biayaKapal->approver->name ?? '__________' }} )</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
