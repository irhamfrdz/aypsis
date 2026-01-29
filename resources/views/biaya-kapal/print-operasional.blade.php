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
            'fontSize' => '15px', 
            'headerH1' => '24px', 
            'tableFont' => '13px', 
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
            'fontSize' => '15px', 
            'headerH1' => '24px', 
            'tableFont' => '13px', 
        ],
        'Half-A4' => [
            'size' => '210mm 148.5mm',
            'width' => '210mm',
            'height' => '148.5mm',
            'containerWidth' => '210mm',
            'fontSize' => '13px',
            'headerH1' => '18px',
            'tableFont' => '11px',
        ]
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-A4'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Biaya Operasional - {{ $biayaKapal->nomor_invoice }}</title>
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
            font-weight: bold;
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
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 4px;
            color: #1a1a1a;
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

        .footer {
            margin-top: 15px;
            padding-top: 10px;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 12px;
        }
        
        .signature-box {
            text-align: center;
            font-size: 10px;
        }
        
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 3px;
            font-weight: bold;
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
            <h1>PERMOHONAN TRANSFER (OPERASIONAL)</h1>
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
                        @if($biayaKapal->penerima)
                        <tr>
                            <td style="width: 35%;">Penerima</td>
                            <td>: {{ $biayaKapal->penerima }}</td>
                        </tr>
                        @endif
                        @if($biayaKapal->nama_vendor)
                        <tr>
                            <td>Nama Vendor</td>
                            <td>: {{ $biayaKapal->nama_vendor }}</td>
                        </tr>
                        @endif
                        @if($biayaKapal->nomor_rekening)
                        <tr>
                            <td>Nomor Rekening</td>
                            <td>: {{ $biayaKapal->nomor_rekening }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Biaya Operasional -->
        <div style="margin-bottom: 12px;">
            <strong style="font-size: {{ $currentPaper['tableFont'] }};">Detail Biaya Operasional:</strong>
            <table class="table" style="margin-top: 6px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 8%;">No</th>
                        <th style="width: 35%;">Nama Kapal</th>
                        <th style="width: 25%;">No. Voyage</th>
                        <th style="width: 32%;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandTotal = 0;
                    @endphp
                    @forelse($biayaKapal->operasionalDetails as $index => $detail)
                        @php
                            $grandTotal += $detail->nominal;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $detail->kapal ?? '-' }}</td>
                            <td class="text-center">{{ $detail->voyage ?? '-' }}</td>
                            <td class="text-right">Rp {{ number_format($detail->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data detail operasional</td>
                        </tr>
                    @endforelse
                    
                    <tr class="total-row">
                        <td colspan="3" class="text-right"><strong>TOTAL PEMBAYARAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Keterangan -->
        <div style="margin-bottom: 12px; border: 2px solid #333; padding: 8px; min-height: 40px;">
            <strong>Keterangan:</strong><br>
            {{ $biayaKapal->keterangan }}
        </div>

        <!-- Signature Section -->
        <div class="footer">
            <div class="signatures">
                <div class="signature-box">
                    <div>Dibuat Oleh</div>
                    <div class="signature-line">
                        {{ $biayaKapal->creator->name ?? '-' }}
                    </div>
                </div>
                
                <div class="signature-box">
                    <div>Diperiksa Oleh</div>
                    <div class="signature-line">
                        &nbsp;
                    </div>
                </div>
                
                <div class="signature-box">
                    <div>Disetujui Oleh</div>
                    <div class="signature-line">
                        {{ $biayaKapal->approver->name ?? '-' }}
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 15px; font-size: 8px; color: #999;">
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</body>
</html>
