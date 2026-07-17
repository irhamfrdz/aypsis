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
    <title>Biaya Umum - {{ $biayaKapal->nomor_invoice }}</title>
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
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 10px; right: 10px; background: white; padding: 10px; border: 1px solid #ccc; border-radius: 5px; z-index: 1000; display: flex; gap: 8px;">
        @include('components.paper-selector', ['selectedSize' => $paperSize ?? 'Half-A4'])
        <button onclick="window.print()" class="bg-blue-600 text-white px-3 py-2 rounded text-sm">Print</button>
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
                            <td>: {{ \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d/M/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Nomor</td>
                            <td>: {{ $biayaKapal->nomor_invoice }}</td>
                        </tr>
                        <tr>
                            <td>Nomor Referensi</td>
                            <td>: {{ $biayaKapal->nomor_referensi ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div style="flex: 1;">
                    <table class="info-table">
                        @if($biayaKapal->penerima || ($biayaKapal->umumDetails->first() && $biayaKapal->umumDetails->first()->penerima))
                        <tr>
                            <td style="width: 35%;">Penerima</td>
                            <td>: {{ $biayaKapal->penerima ?: $biayaKapal->umumDetails->first()->penerima }}</td>
                        </tr>
                        @endif
                        @if($biayaKapal->nama_vendor || ($biayaKapal->umumDetails->first() && $biayaKapal->umumDetails->first()->nama_vendor))
                        <tr>
                            <td>Nama Vendor</td>
                            <td>: {{ $biayaKapal->nama_vendor ?: $biayaKapal->umumDetails->first()->nama_vendor }}</td>
                        </tr>
                        @endif
                        @if($biayaKapal->bank_id || ($biayaKapal->umumDetails->first() && $biayaKapal->umumDetails->first()->bank_id))
                        <tr>
                            <td>Bank</td>
                            <td>: {{ $biayaKapal->bank->name ?? ($biayaKapal->umumDetails->first()->bank->name ?? '-') }}</td>
                        </tr>
                        @endif
                        @if($biayaKapal->nomor_rekening || ($biayaKapal->umumDetails->first() && $biayaKapal->umumDetails->first()->nomor_rekening))
                        <tr>
                            <td>Nomor Rekening</td>
                            <td>: {{ $biayaKapal->nomor_rekening ?: $biayaKapal->umumDetails->first()->nomor_rekening }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Biaya Umum -->
        <div style="margin-bottom: 12px;">
            <div class="section-header">Detail Biaya Umum:</div>
            <table class="custom-table" style="margin-top: 6px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Nama Kapal</th>
                        <th style="width: 15%;">No. Voyage</th>
                        <th style="width: 20%;">Keterangan</th>
                        <th style="width: 15%;">Nominal</th>
                        <th style="width: 15%;">PPh</th>
                        <th style="width: 15%;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandTotal = 0;
                    @endphp
                    @forelse($biayaKapal->umumDetails as $index => $detail)
                        @php
                            $subtotal = $detail->nominal - $detail->pph;
                            $grandTotal += $subtotal;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $detail->kapal ?? '-' }}</td>
                            <td class="text-center">{{ $detail->voyage ?? '-' }}</td>
                            <td>{{ $detail->keterangan ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($detail->nominal, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($detail->pph, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data detail umum</td>
                        </tr>
                    @endforelse
                    
                    <tr class="total-row">
                        <td colspan="6" class="text-right"><strong>TOTAL PEMBAYARAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Keterangan -->
        <div class="keterangan-box">
            <strong>Keterangan:</strong><br>
            {{ $biayaKapal->keterangan }}
        </div>

        <!-- Signature Section -->
        <div class="footer">
            <table class="signature-table">
                <tr>
                    <td>
                        <div>Dibuat Oleh</div>
                        <div style="margin-top: 50px; border-bottom: 1px solid #333; display: inline-block; width: 80%;">
                            {{ $biayaKapal->creator->name ?? '-' }}
                        </div>
                    </td>
                    <td>
                        <div>Diperiksa Oleh</div>
                        <div style="margin-top: 50px; border-bottom: 1px solid #333; display: inline-block; width: 80%;">
                            &nbsp;
                        </div>
                    </td>
                    <td>
                        <div>Disetujui Oleh</div>
                        <div style="margin-top: 50px; border-bottom: 1px solid #333; display: inline-block; width: 80%;">
                            {{ $biayaKapal->approver->name ?? '-' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
