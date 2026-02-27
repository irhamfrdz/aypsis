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
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-A4'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Biaya TKBM - {{ $biayaKapal->nomor_invoice }}</title>
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

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
            table-layout: fixed;
        }

        .table th,
        .table td {
            border: 1px solid #333;
            padding: 1px 4px;
            text-align: left;
            vertical-align: middle;
        }

        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            font-size: {{ $currentPaper['tableFont'] }};
            text-align: center;
            border: 1.5px solid #333;
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
            background-color: #f0f0f0 !important;
            font-weight: bold !important;
            border: 1.5px solid #333 !important;
        }

        .signature-section {
            margin-top: 5px;
            page-break-inside: avoid;
        }

        .footer {
            margin-top: 5px;
            padding-top: 5px;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 5px;
        }
        
        .signature-box {
            text-align: center;
            font-size: {{ $currentPaper['fontSize'] }};
            font-weight: bold;
        }
        
        .signature-line {
            margin-top: 25px;
            border-top: 1px solid #333;
            padding-top: 2px;
            font-weight: bold;
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
            <h1>PERMOHONAN TRANSFER</h1>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <table class="info-table" style="width: 100%;">
                <tr>
                    <td style="width: 15%;">Tanggal</td>
                    <td style="width: 35%;">: {{ \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d/M/Y') }}</td>
                    <td style="width: 15%;">Penerima</td>
                    <td>: {{ $biayaKapal->penerima ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nomor</td>
                    <td>: {{ $biayaKapal->nomor_invoice }}</td>
                    <td>Nama Vendor</td>
                    <td>: {{ $biayaKapal->nama_vendor ?? '-' }}</td>
                </tr>
                <tr>
                    <td>No. Ref</td>
                    <td>: {{ $biayaKapal->nomor_referensi ?? $biayaKapal->nomor_invoice }}</td>
                    <td>No. Rekening</td>
                    <td>: {{ $biayaKapal->nomor_rekening ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- Detail Biaya Kapal -->
        <div style="margin-bottom: 8px;">
            <strong style="font-size: {{ $currentPaper['tableFont'] }};">Detail Biaya KTKBM:</strong>
            <table class="table" style="margin-top: 4px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 8%;">No</th>
                        <th style="width: 15%;">Tgl Inv</th>
                        <th style="width: 15%;">Voyage</th>
                        <th style="width: 27%;">Biaya</th>
                        <th style="width: 35%;">Grand Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        if ($biayaKapal->tkbmDetails && $biayaKapal->tkbmDetails->count() > 0) {
                            $groupedDetails = $biayaKapal->tkbmDetails->groupBy(function($item) {
                                 return ($item->kapal ?? '-') . '|' . ($item->voyage ?? '-') . '|' . ($item->no_referensi ?? '-') . '|' . ($item->tanggal_invoice_vendor ?? '-');
                            });
                        } else {
                            $groupedDetails = null;
                        }
                    @endphp
                    
                    @if($groupedDetails)
                        @php $rowNumber = 0; @endphp
                        @foreach($groupedDetails as $groupKey => $details)
                            @php
                                $rowNumber++;
                                list($groupKapal, $groupVoyage) = explode('|', $groupKey);
                                $groupSubtotal = $details->sum('subtotal');
                                $firstItem = $details->first();
                                $groupGrandTotal = $firstItem->grand_total ?? 0;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $rowNumber }}</td>
                                <td class="text-center">{{ $firstItem->tanggal_invoice_vendor ? \Carbon\Carbon::parse($firstItem->tanggal_invoice_vendor)->format('d/M/y') : '-' }}</td>
                                <td class="text-center">{{ $groupVoyage }}</td>
                                <td class="text-right">Rp {{ number_format($groupSubtotal, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($groupGrandTotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @endif
                    
                    <tr class="total-row">
                        <td colspan="4" class="text-right"><strong>TOTAL PEMBAYARAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($biayaKapal->nominal, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($biayaKapal->tkbmDetails && $biayaKapal->tkbmDetails->count() > 0)
        <!-- Detail Barang TKBM -->
        <div style="margin-bottom: 8px;">
            <strong style="font-size: {{ $currentPaper['tableFont'] }};">Detail Barang KTKBM:</strong>
            
            @php
                $combinedBarang = $biayaKapal->tkbmDetails->groupBy('pricelist_tkbm_id')->map(function($items) {
                    $first = $items->first();
                    return [
                        'barang' => $first->pricelistTkbm->nama_barang ?? '-',
                        'harga_satuan' => $first->pricelistTkbm->tarif ?? 0,
                        'jumlah' => $items->sum('jumlah'),
                        'subtotal' => $items->sum('subtotal'),
                    ];
                })->values();
                $overallTotal = $combinedBarang->sum('subtotal');
                $groups = $biayaKapal->tkbmDetails->groupBy(function($item) {
                     return ($item->kapal ?? '-') . '|' . ($item->voyage ?? '-') . '|' . ($item->no_referensi ?? '-') . '|' . ($item->tanggal_invoice_vendor ?? '-');
                });
                $totalPph = $groups->sum(function($group) { return $group->first()->pph ?? 0; });
                $totalAdjustment = $groups->sum(function($group) { return $group->first()->adjustment ?? 0; });
                $finalTotal = $overallTotal - $totalPph + $totalAdjustment;
            @endphp

            <table class="table" style="margin-top: 4px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 8%;">No</th>
                        <th style="width: 42%;">Jenis Barang</th>
                        <th style="width: 15%;">Qty</th>
                        <th style="width: 35%;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($combinedBarang as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item['barang'] }}</td>
                        <td class="text-center">{{ number_format($item['jumlah'], 2, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3" class="text-right"><strong>TOTAL PEMBAYARAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($finalTotal, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- Keterangan -->
        <div style="margin-bottom: 2px; border: 1.5px solid #333; padding: 2px; min-height: 20px;">
            <strong style="font-size: 8px;">Keterangan:</strong><br>
        </div>

        <!-- Signature Section -->
        <div class="footer">
            <table style="width: 100%; border-collapse: collapse; text-align: center;">
                <tr>
                    <td style="width: 33.33%;"><strong>Dibuat Oleh:</strong></td>
                    <td style="width: 33.33%;"><strong>Diperiksa Oleh:</strong></td>
                    <td style="width: 33.33%;"><strong>Disetujui Oleh:</strong></td>
                </tr>
                <tr>
                    <td style="height: 40px;"></td>
                    <td style="height: 40px;"></td>
                    <td style="height: 40px;"></td>
                </tr>
                <tr>
                    <td>( {{ $biayaKapal->creator->name ?? '__________' }} )</td>
                    <td>( __________ )</td>
                    <td>( {{ $biayaKapal->approver->name ?? '__________' }} )</td>
                </tr>
            </table>
            
            <div style="text-align: center; margin-top: 8px; font-size: 8px; color: #999;">
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </div>
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
