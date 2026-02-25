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
            'headerH1' => '20px',
            'tableFont' => '11px',
        ],
        'Half-Folio' => [
            'size' => '215.9mm 165.1mm',
            'width' => '215.9mm',
            'height' => '165.1mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '14px',
            'headerH1' => '20px',
            'tableFont' => '12px',
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
            'size' => '210mm 148.5mm',
            'width' => '210mm',
            'height' => '148.5mm',
            'containerWidth' => '210mm',
            'fontSize' => '11px',
            'headerH1' => '16px',
            'tableFont' => '9px',
        ]
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-A4'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Biaya Labuh Tambat - {{ $biayaKapal->nomor_invoice }}</title>
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
            color: #000;
            background: white;
            margin: 0;
            padding: 0;
            font-weight: bold;
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
            font-size: {{ $currentPaper['fontSize'] }};
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

        .info-table td {
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
            font-weight: bold;
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
            font-size: {{ $currentPaper['fontSize'] }};
            font-weight: bold;
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
        <div class="header">
            <h1>PERMOHONAN TRANSFER</h1>
        </div>

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
                            <td>: {{ $biayaKapal->nomor_referensi ?? $biayaKapal->nomor_invoice }}</td>
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
                        @php $firstItem = $biayaKapal->labuhTambatDetails->first(); @endphp
                        @if($firstItem && $firstItem->vendor)
                        <tr>
                            <td>Nama Vendor</td>
                            <td>: {{ $firstItem->vendor }}</td>
                        </tr>
                        @endif
                        @if($firstItem && $firstItem->nomor_rekening)
                        <tr>
                            <td>Nomor Rekening</td>
                            <td>: {{ $firstItem->nomor_rekening }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 12px;">
            <strong style="font-size: {{ $currentPaper['tableFont'] }};">Detail Biaya Labuh Tambat:</strong>
            <table class="table" style="margin-top: 6px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Kapal</th>
                        <th style="width: 10%;">Voyage</th>
                        <th style="width: 15%;">Lokasi</th>
                        <th style="width: 20%;">Keterangan</th>
                        <th style="width: 10%;">GT</th>
                        <th style="width: 12%;">Harga</th>
                        <th style="width: 13%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($biayaKapal->labuhTambatDetails as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->kapal }}</td>
                        <td class="text-center">{{ $item->voyage }}</td>
                        <td>{{ $item->lokasi }}</td>
                        <td>{{ $item->type_keterangan }}</td>
                        <td class="text-right">{{ $item->is_lumpsum ? '-' : number_format($item->kuantitas, 2, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    
                    @php
                        $subtotal = $biayaKapal->labuhTambatDetails->sum('sub_total');
                        $pph = $biayaKapal->labuhTambatDetails->sum('pph');
                        $grandTotal = $biayaKapal->labuhTambatDetails->sum('grand_total');
                    @endphp
                    
                    <tr class="total-row">
                        <td colspan="7" class="text-right"><strong>SUBTOTAL</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="7" class="text-right"><strong>PPH (2%)</strong></td>
                        <td class="text-right"><strong>- Rp {{ number_format($pph, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="7" class="text-right"><strong>TOTAL MERUPAKAN PEMBAYARAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-bottom: 12px; border: 2px solid #333; padding: 8px; min-height: 40px;">
            <strong>Keterangan:</strong><br>
            {{ $biayaKapal->keterangan }}
        </div>

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

    <script>
        document.getElementById('startPrint')?.addEventListener('click', function() {
            window.print();
        });
    </script>
</body>
</html>
