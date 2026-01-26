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
            'fontSize' => '15px', // Increased from 13px
            'headerH1' => '24px', // Increased from 20px
            'tableFont' => '13px', // Increased from 11px
        ],
        'Half-Folio' => [
            'size' => '215.9mm 165.1mm',
            'width' => '215.9mm',
            'height' => '165.1mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '14px', // Increased from 13px
            'headerH1' => '22px', // Increased from 20px
            'tableFont' => '12px', // Increased from 11px
        ],
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '210mm',
            'fontSize' => '15px', // Increased from 13px
            'headerH1' => '24px', // Increased from 20px
            'tableFont' => '13px', // Increased from 11px
        ],
        'Half-A4' => [
            'size' => '210mm 148.5mm',
            'width' => '210mm',
            'height' => '148.5mm',
            'containerWidth' => '210mm',
            'fontSize' => '13px', // Increased from 11px
            'headerH1' => '18px', // Increased from 16px
            'tableFont' => '11px', // Increased from 9px
        ]
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-A4'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Biaya Kapal - {{ $biayaKapal->nomor_invoice }}</title>
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
            font-weight: bold; /* Added bold */
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
            font-size: 10px;
        }
        
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 3px;
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

        <!-- Detail Biaya Kapal -->
        <div style="margin-bottom: 12px;">
            <strong style="font-size: {{ $currentPaper['tableFont'] }};">Detail Biaya Kapal:</strong>
            <table class="table" style="margin-top: 6px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 8%;">No</th>
                        <th style="width: 32%;">Nama Kapal</th>
                        <th style="width: 20%;">Tanggal</th>
                        <th style="width: 20%;">No. Voyage</th>
                        <th style="width: 20%;">Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Untuk biaya buruh (KB024), ambil kapal dan voyage dari barangDetails
                        if ($biayaKapal->jenis_biaya === 'KB024' && $biayaKapal->barangDetails && $biayaKapal->barangDetails->count() > 0) {
                            $groupedDetails = $biayaKapal->barangDetails->groupBy(function($item) {
                                return ($item->kapal ?? '-') . '|' . ($item->voyage ?? '-');
                            });
                        } else {
                            $namaKapals = is_array($biayaKapal->nama_kapal) ? $biayaKapal->nama_kapal : [$biayaKapal->nama_kapal];
                            $noVoyages = is_array($biayaKapal->no_voyage) ? $biayaKapal->no_voyage : ($biayaKapal->no_voyage ? [$biayaKapal->no_voyage] : []);
                            $groupedDetails = null;
                        }
                    @endphp
                    
                    @if($groupedDetails)
                        {{-- Biaya Buruh: Tampilkan per grup kapal + voyage --}}
                        @php $rowNumber = 0; @endphp
                        @foreach($groupedDetails as $groupKey => $details)
                            @php
                                $rowNumber++;
                                list($groupKapal, $groupVoyage) = explode('|', $groupKey);
                                $groupSubtotal = $details->sum('subtotal');
                            @endphp
                            <tr>
                                <td class="text-center">{{ $rowNumber }}</td>
                                <td>{{ $groupKapal }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d/M/Y') }}</td>
                                <td class="text-center">{{ $groupVoyage }}</td>
                                <td class="text-right">Rp {{ number_format($groupSubtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @else
                        {{-- Jika tidak ada detail barang, tampilkan per kapal/voyage --}}
                        @php $maxCount = max(count($namaKapals ?? []), count($noVoyages ?? []), 1); @endphp
                        @for($i = 0; $i < $maxCount; $i++)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $namaKapals[$i] ?? ($i == 0 ? '-' : '') }}</td>
                            <td class="text-center">{{ $i == 0 ? \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d/M/Y') : '' }}</td>
                            <td class="text-center">{{ $noVoyages[$i] ?? '-' }}</td>
                            <td class="text-right">{{ $i == 0 ? 'Rp ' . number_format($biayaKapal->nominal, 0, ',', '.') : '' }}</td>
                        </tr>
                        @endfor
                    @endif
                    
                    <tr class="total-row">
                        <td colspan="4" class="text-right"><strong>TOTAL PEMBAYARAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($biayaKapal->nominal, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($biayaKapal->barangDetails && $biayaKapal->barangDetails->count() > 0)
        <!-- Detail Barang -->
        <div style="margin-bottom: 12px;">
            <strong style="font-size: {{ $currentPaper['tableFont'] }};">Detail Barang:</strong>
            
            @if($biayaKapal->jenis_biaya === 'KB024' && isset($groupedDetails) && $groupedDetails->count() > 0)
                {{-- Biaya Buruh: Gabungkan semua barang menjadi satu tabel (gabungan semua kapal) --}}
                @php
                    // Combine all barang across groups into one list
                    $combinedBarang = $biayaKapal->barangDetails->groupBy('pricelist_buruh_id')->map(function($items) {
                        $first = $items->first();
                        return [
                            'barang' => $first->pricelistBuruh->barang ?? '-',
                            'harga_satuan' => $first->pricelistBuruh->tarif ?? 0,
                            'jumlah' => $items->sum('jumlah'),
                            'subtotal' => $items->sum('subtotal'),
                        ];
                    })->values();
                    $overallTotal = $combinedBarang->sum('subtotal');
                @endphp

                <div style="margin-top:6px; margin-bottom:6px; font-size:{{ $currentPaper['tableFont'] }};">
                    <strong>Detail Barang (Gabungan Semua Kapal)</strong>
                </div>

                <table class="table" style="margin-top: 6px; margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="width: 6%;">No</th>
                            <th style="width: 37%;">Jenis Barang</th>
                            <th style="width: 12%;">Jumlah</th>
                            <th style="width: 17%;">Harga Satuan</th>
                            <th style="width: 18%;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($combinedBarang as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item['barang'] }}</td>
                            <td class="text-center">{{ number_format($item['jumlah'], 2, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                            <td class="text-right"><strong>Rp {{ number_format($overallTotal, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tbody>
                </table>

            @else
                {{-- Default: Gabungkan semua barang yang sama --}}
                <table class="table" style="margin-top: 6px; margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="width: 6%;">No</th>
                            <th style="width: 37%;">Jenis Barang</th>
                            <th style="width: 12%;">Jumlah</th>
                            <th style="width: 17%;">Harga Satuan</th>
                            <th style="width: 18%;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Gabungkan semua barang yang sama
                            $combinedBarang = $biayaKapal->barangDetails->groupBy('pricelist_buruh_id')->map(function($items) {
                                $first = $items->first();
                                return [
                                    'barang' => $first->pricelistBuruh->barang ?? '-',
                                    'harga_satuan' => $first->pricelistBuruh->tarif ?? 0,
                                    'jumlah' => $items->sum('jumlah'),
                                    'subtotal' => $items->sum('subtotal'),
                                ];
                            })->values();
                        @endphp
                        @foreach($combinedBarang as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item['barang'] }}</td>
                            <td class="text-center">{{ number_format($item['jumlah'], 2, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                            <td class="text-right"><strong>Rp {{ number_format($combinedBarang->sum('subtotal'), 0, ',', '.') }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
        @endif

        <!-- Keterangan -->
        <div style="margin-bottom: 12px; border: 2px solid #333; padding: 8px; min-height: 40px;">
            <strong>Keterangan:</strong><br>
            {{-- Kosong untuk ditulis manual --}}
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

    <script>
        document.getElementById('startPrint')?.addEventListener('click', function() {
            window.print();
        });
        
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
