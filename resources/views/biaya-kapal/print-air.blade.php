<!DOCTYPE html>
<html lang="id">
@php
    // Calculate Vendor first to determine default paper size
    $vendorDisplay = $biayaKapal->nama_vendor ?? ($biayaKapal->airDetails->pluck('vendor')->filter()->unique()->values()->first() ?? '-');
    $isAbqori = str_contains(strtoupper($vendorDisplay), 'ABQORI');

    $paperSize = request('paper_size', $isAbqori ? 'Half-Folio' : 'A4');
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
    <title>Invoice Biaya Air - {{ $biayaKapal->nomor_invoice }}</title>
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
        <a href="{{ route('biaya-kapal.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded text-sm no-underline" style="text-decoration: none;">Kembali</a>
    </div>

    <div class="container">
        <div class="header">
            <h1>PERMOHONAN TRANSFER</h1>
        </div>
        
        @php
            $penerimaDisplay = $biayaKapal->penerima ?? ($biayaKapal->airDetails->pluck('penerima')->filter()->unique()->values()->first() ?? '-');
            $rekeningDisplay = $biayaKapal->nomor_rekening ?? ($biayaKapal->airDetails->pluck('nomor_rekening')->filter()->unique()->values()->first() ?? '-');
            
            // Calculate Totals and Group by Type
            $waterByType = [];
            $totalJasaAir = 0;
            $countJasaAir = 0;
            $totalPPH = 0;
            $totalGrandTotal = 0;
            
            foreach($biayaKapal->airDetails as $detail) {
                // Group by Type Keterangan
                $typeKey = $detail->type_keterangan ?? 'AIR TAWAR';
                if (!isset($waterByType[$typeKey])) {
                    $waterByType[$typeKey] = [
                        'qty' => 0, 
                        'cost' => 0
                    ];
                }
                
                $waterByType[$typeKey]['qty'] += $detail->kuantitas;
                if ($detail->is_lumpsum) {
                    $waterByType[$typeKey]['cost'] += $detail->harga;
                } else {
                    $waterByType[$typeKey]['cost'] += ($detail->kuantitas * ($detail->harga ?? 0));
                }
                
                if ($detail->jasa_air > 0) {
                    $totalJasaAir += $detail->jasa_air;
                    $countJasaAir++;
                }
                
                $totalPPH += $detail->pph;
                $totalGrandTotal += $detail->grand_total;
            }
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
                    <td>: {{ $biayaKapal->klasifikasiBiaya->nama ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <!-- TABLE 1: DETAIL BIAYA KAPAL -->
        <div class="section-header">Detail Biaya Kapal:</div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 20%;">Tanggal Ref.</th>
                    <th style="width: 20%;">Referensi</th>
                    <th style="width: 32%;">Jenis Biaya</th>
                    <th style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $perKapal = $biayaKapal->airDetails->groupBy(function($item) {
                        return ($item->kapal ?? '-') . ($item->voyage ? ' ('.$item->voyage.')' : '');
                    });
                @endphp
                
                @forelse($perKapal as $kapalName => $details)
                @php
                    $firstDate = $details->min('tanggal_invoice_vendor');
                    $lastDate = $details->max('tanggal_invoice_vendor');
                    $isSameDate = $firstDate == $lastDate;
                    $formattedDate = $firstDate ? \Carbon\Carbon::parse($firstDate)->format('d/M/Y') : '-';
                    if (!$isSameDate && $firstDate && $lastDate) {
                        $formattedDate = \Carbon\Carbon::parse($firstDate)->format('d/M/Y') . ' - ' . \Carbon\Carbon::parse($lastDate)->format('d/M/Y');
                    }
                    
                    $references = $details->pluck('nomor_referensi')->filter()->unique()->values();
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $formattedDate }}</td>
                    <td>
                        @foreach($references as $ref)
                            {{ $ref }}{{ !$loop->last ? ',' : '' }}
                            @if(!$loop->last && $loop->iteration % 2 == 0) <br> @endif
                        @endforeach
                        @if($references->isEmpty()) - @endif
                    </td>
                    <td>Biaya Air {{ $kapalName }}</td>
                    <td class="text-right">Rp {{ number_format($details->sum('grand_total'), 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data detail.</td>
                </tr>
                @endforelse
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL PEMBAYARAN</td>
                    <td class="text-right">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- TABLE 2: DETAIL BARANG (GABUNGAN) -->
        <div class="section-header">Detail Barang (Gabungan):</div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 37%;">Jenis Barang</th>
                    <th style="width: 15%;">Jumlah</th>
                    <th style="width: 20%;">Harga Satuan</th>
                    <th style="width: 20%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                
                @foreach($waterByType as $typeName => $data)
                    @if($data['cost'] > 0 || $data['qty'] > 0)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>AIR TAWAR ({{ strtoupper($typeName) }})</td>
                        <td class="text-center">{{ number_format($data['qty'], 2, ',', '.') }}</td>
                        <td class="text-right">
                            @if($data['qty'] > 0)
                                Rp {{ number_format($data['cost'] / $data['qty'], 2, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">Rp {{ number_format($data['cost'], 0, ',', '.') }}</td>
                    </tr>
                    @endif
                @endforeach
                
                @if($totalJasaAir > 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>JASA AIR</td>
                    <td class="text-center">{{ $countJasaAir }}</td>
                    <td class="text-right">Rp {{ number_format($totalJasaAir / $countJasaAir, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalJasaAir, 0, ',', '.') }}</td>
                </tr>
                @endif
                
                @if($totalPPH > 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>PPH (2%)</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($totalPPH, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalPPH, 0, ',', '.') }}</td>
                </tr>
                @endif
                
                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL</td>
                    <td class="text-right">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- KETERANGAN BOX -->
        <div class="keterangan-box">
            <strong style="font-size: 8px;">Keterangan:</strong><br>
            <div style="font-size: 8px;">
                @php
                    $keterangan = $biayaKapal->keterangan ?? '';
                    if (stripos($keterangan, 'Detail Biaya Air:') !== false) $keterangan = explode('Detail Biaya Air:', $keterangan)[0];
                    if (stripos($keterangan, 'Detail Barang Buruh:') !== false) $keterangan = explode('Detail Barang Buruh:', $keterangan)[0];
                    if (stripos($keterangan, 'Detail Biaya TKBM:') !== false) $keterangan = explode('Detail Biaya TKBM:', $keterangan)[0];
                @endphp
                {!! nl2br(e(trim($keterangan))) !!}
            </div>
        </div>
        
        <!-- FOOTER SIGNATURES -->
        <div class="footer">
            <table class="signature-table">
                <tr>
                    <td><strong>Dibuat Oleh:</strong></td>
                    <td><strong>Diperiksa Oleh:</strong></td>
                    <td><strong>Disetujui Oleh:</strong></td>
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
</body>
</html>
