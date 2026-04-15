<!DOCTYPE html>
<html lang="id">
@php
    // Calculate Vendor first to determine default paper size
    $vendorDisplay = $biayaKapal->nama_vendor ?? ($meratusDetails->pluck('vendor')->filter()->unique()->values()->first() ?? 'MERATUS');
    $isAbqori = str_contains(strtoupper($vendorDisplay), 'ABQORI');

    $paperSize = request('paper_size', $isAbqori ? 'Half-Folio' : 'Half-Folio'); // Defaulting to Half-Folio as it's common for these invoices
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
    <title>Invoice Biaya Meratus - {{ $biayaKapal->nomor_invoice }}</title>
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
            $penerimaDisplay = $biayaKapal->penerima ?? ($meratusDetails->pluck('penerima')->filter()->unique()->values()->first() ?? '-');
            $rekeningDisplay = $biayaKapal->nomor_rekening ?? ($meratusDetails->pluck('nomor_rekening')->filter()->unique()->values()->first() ?? '-');
            
            // Calculate Totals and Group by Type
            $meratusByType = [];
            $totalSubtotal = 0;
            $totalPPH = 0; // Total PPH yang benar-benar memotong (untuk kalkulasi)
            $totalPPN = 0; // Total PPN yang benar-benar menambah (untuk kalkulasi)
            $displayPPH = 0; // Untuk tampilan baris di tabel
            $displayPPN = 0; // Untuk tampilan baris di tabel
            $totalAdjustment = 0;
            $totalMaterai = 0;
            $totalGrandTotal = 0;
            
            foreach($meratusDetails as $detail) {
                // Group by Type (Jenis Biaya) and include Muat/Bongkar status
                $status = '';
                if ($detail->is_muat && $detail->is_bongkar) $status = ' (MUAT/BONGKAR)';
                elseif ($detail->is_muat) $status = ' (MUAT)';
                elseif ($detail->is_bongkar) $status = ' (BONGKAR)';

                $typeKey = ($detail->jenis_biaya ?? 'BIAYA MERATUS') . $status;
                
                if (!isset($meratusByType[$typeKey])) {
                    $meratusByType[$typeKey] = [
                        'qty' => 0, 
                        'cost' => 0
                    ];
                }
                
                $meratusByType[$typeKey]['qty'] += $detail->kuantitas;
                $meratusByType[$typeKey]['cost'] += $detail->sub_total;
                
                $totalSubtotal += $detail->sub_total;
                
                $isPphActive = ($detail->pph_active ?? true);
                $isPpnActive = ($detail->ppn_active ?? false);
                
                $totalPPH += ($isPphActive ? $detail->pph : 0);
                $totalPPN += ($isPpnActive ? $detail->ppn : 0);
                
                // Selalu jumlahkan untuk tampilan
                $displayPPH += $detail->pph;
                $displayPPN += $detail->ppn;
                
                $totalAdjustment += $detail->adjustment;
                $totalMaterai += $detail->biaya_materai;
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
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>

        <!-- TABLE 1: DETAIL BIAYA KAPAL -->
        <div class="section-header">Detail Biaya Kapal:</div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 18%;">Tanggal Ref.</th>
                    <th style="width: 37%;">Referensi</th>
                    <th style="width: 20%;">Nomor Voyage</th>
                    <th style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $perSection = $meratusDetails->groupBy(function($item) {
                        return ($item->kapal ?? '-') . '|' . ($item->voyage ?? '-') . '|' . ($item->nomor_referensi ?? '-');
                    });
                @endphp
                
                @forelse($perSection as $sectionKey => $details)
                @php
                    $firstDate = $details->min('tanggal_invoice_vendor');
                    $lastDate = $details->max('tanggal_invoice_vendor');
                    $isSameDate = $firstDate == $lastDate;
                    $formattedDate = $firstDate ? \Carbon\Carbon::parse($firstDate)->format('d/M/Y') : '-';
                    if (!$isSameDate && $firstDate && $lastDate) {
                        $formattedDate = \Carbon\Carbon::parse($firstDate)->format('d/M/Y') . ' - ' . \Carbon\Carbon::parse($lastDate)->format('d/M/Y');
                    }
                    
                    $references = $details->pluck('nomor_referensi')->filter()->unique()->values();
                    $sectionGrandTotal = $details->sum('grand_total');
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
                    <td class="text-center">{{ $details->first()->voyage ?? '-' }}</td>

                    <td class="text-right">Rp {{ number_format($sectionGrandTotal, 0, ',', '.') }}</td>
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

        <!-- TABLE 2: DETAIL BIAYA (GABUNGAN) -->
        <div class="section-header">Detail Biaya (Gabungan):</div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 57%;">Jenis Biaya</th>
                    <th style="width: 15%;">Jumlah</th>
                    <th style="width: 20%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                
                @foreach($meratusByType as $typeName => $data)
                    @if($data['cost'] > 0 || $data['qty'] > 0)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ strtoupper($typeName) }}</td>
                        <td class="text-center">{{ rtrim(rtrim(number_format($data['qty'], 2, ',', '.'), '0'), ',') }}</td>
                        <td class="text-right">Rp {{ number_format($data['cost'], 0, ',', '.') }}</td>
                    </tr>
                    @endif
                @endforeach
                
                @if($displayPPH > 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>PPH (2%) {{ $totalPPH <= 0 ? '(Reimburse)' : '' }}</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($displayPPH, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($totalPPN > 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>PPN (11%)</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($totalPPN, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($totalMaterai > 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>BIAYA MATERAI</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($totalMaterai, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($totalAdjustment != 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>ADJUSTMENT</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($totalAdjustment, 0, ',', '.') }}</td>
                </tr>
                @endif
                
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL</td>
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
                @endphp
                {!! nl2br(e(trim($keterangan))) !!}
            </div>
        </div>
        
        <!-- FOOTER SIGNATURES -->
        <div class="footer" style="margin-top: 40px;">
            <table class="signature-table">
                <tr>
                    <td><strong>Dibuat Oleh:</strong></td>
                    <td><strong>Diperiksa Oleh:</strong></td>
                    <td><strong>Disetujui Oleh:</strong></td>
                </tr>
                <tr>
                    <td style="height: 85px;"></td>
                    <td style="height: 85px;"></td>
                    <td style="height: 85px;"></td>
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
