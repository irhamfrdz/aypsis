<!DOCTYPE html>
<html lang="id">
@php
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
    <title>Invoice Utilities - {{ $invoice->nomor_invoice }}</title>
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
            padding: 2px 4px;
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
        <a href="{{ route('invoice-aktivitas-lain.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded text-sm no-underline" style="text-decoration: none;">Kembali</a>
    </div>

    <div class="container">
        <div class="header">
            <h1>PERMOHONAN TRANSFER (UTILITIES)</h1>
        </div>

        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td style="width: 20%;">Nomor Invoice</td>
                    <td style="width: 30%;">: {{ $invoice->nomor_invoice }}</td>
                    <td style="width: 25%;">Tanggal Invoice</td>
                    <td>: {{ $invoice->tanggal_invoice->format('d/M/Y') }}</td>
                </tr>
                <tr>
                    <td>Vendor</td>
                    <td>: 
                        @php
                            $vendor = null;
                            if ($invoice->biayaUtility->isNotEmpty()) {
                                $vendor = $invoice->biayaUtility->first()->vendor;
                            }
                        @endphp
                        {{ $vendor ?? '-' }}
                    </td>
                    <td>Penerima</td>
                    <td>: 
                        @php
                            $penerima = $invoice->penerima;
                            if (empty($penerima) && $invoice->biayaUtility->isNotEmpty()) {
                                $penerima = $invoice->biayaUtility->first()->penerima;
                            }
                        @endphp
                        {{ $penerima ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td>Kode Bayar</td>
                    <td>: 
                        @php
                            $kodeBayar = null;
                            if ($invoice->biayaUtility->isNotEmpty()) {
                                $kodeBayar = $invoice->biayaUtility->first()->kode_bayar;
                            }
                        @endphp
                        {{ $kodeBayar ?? '-' }}
                    </td>
                    <td>Referensi</td>
                    <td>: 
                        @php
                            $referensi = $invoice->referensi;
                            if (empty($referensi) && $invoice->biayaUtility->isNotEmpty()) {
                                $referensi = $invoice->biayaUtility->first()->referensi;
                            }
                        @endphp
                        {{ $referensi ?? '-' }}
                    </td>
                </tr>
            </table>
        </div>

        @php
            $totalDPP = 0;
            $totalPPH = 0;
            $totalPPN = 0;
            $totalGrandTotal = 0;
        @endphp

        <!-- TABLE: DETAIL BIAYA UTILITIES -->
        <div class="section-header">Detail Biaya Utilities (Alat Berat):</div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 18%;">Alat Berat</th>
                    <th style="width: 10%;">Periode</th>
                    <th style="width: 13%;">Tarif Satuan</th>
                    <th style="width: 12%;">DPP</th>
                    <th style="width: 10%;">PPN (11%)</th>
                    <th style="width: 10%;">PPH (10%)</th>
                    <th style="width: 13%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->biayaUtility as $utility)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $utility->tanggal ? \Carbon\Carbon::parse($utility->tanggal)->format('d/M/Y') : '-' }}</td>
                        <td>
                            <div>[{{ $utility->alatBerat->kode_alat ?? '-' }}] {{ $utility->alatBerat->nama ?? '-' }}</div>
                            <div style="font-size: 8px; font-weight: normal; color: #444;">
                                {{ $utility->alatBerat->merk ?? '' }}
                                @if($utility->vendor) | Vendor: {{ $utility->vendor }} @endif
                                @if($utility->kode_bayar) | Kode: {{ $utility->kode_bayar }} @endif
                                @if($utility->referensi) | Ref: {{ $utility->referensi }} @endif
                            </div>
                        </td>
                        <td class="text-center">
                            {{ $utility->jumlah_periode }} {{ ucfirst($utility->jenis_tarif == 'harian' ? 'Hari' : 'Bulan') }}
                        </td>
                        <td class="text-right">Rp {{ number_format($utility->tarif_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($utility->dpp, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($utility->ppn, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($utility->pph, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($utility->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        $totalDPP += $utility->dpp;
                        $totalPPH += $utility->pph;
                        $totalPPN += $utility->ppn;
                        $totalGrandTotal += $utility->grand_total;
                    @endphp
                @endforeach
                
                @php
                    $adjPPN = $invoice->biaya_adjustment * 0.11;
                    $adjPPH = $invoice->biaya_adjustment * 0.10;
                    
                    $totalDPP_Final = $totalDPP + $invoice->biaya_adjustment;
                    $totalPPN_Final = round($totalPPN + $adjPPN);
                    $totalPPH_Final = ($invoice->pph > 0) ? $invoice->pph : round($totalPPH + $adjPPH);
                    $totalGrand_Final = $totalDPP_Final + $totalPPN_Final - $totalPPH_Final + $invoice->biaya_materai;
                @endphp
                
                <tr class="total-row">
                    <td colspan="5" class="text-right">TOTAL KESELURUHAN (ADJUSTED)</td>
                    <td class="text-right">Rp {{ number_format($totalDPP_Final, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalPPN_Final, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalPPH_Final, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalDPP_Final + $totalPPN_Final - $totalPPH_Final, 0, ',', '.') }}</td>
                </tr>
                
                @if($invoice->biaya_adjustment != 0)
                <tr class="total-row">
                    <td colspan="8" class="text-right text-xs">Biaya Adjustment (Adjusted to DPP)</td>
                    <td class="text-right text-xs">Rp {{ number_format($invoice->biaya_adjustment, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($invoice->biaya_materai > 0)
                <tr class="total-row">
                    <td colspan="8" class="text-right">Biaya Materai</td>
                    <td class="text-right">Rp {{ number_format($invoice->biaya_materai, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($invoice->biaya_materai > 0 || $invoice->biaya_adjustment != 0)
                <tr class="total-row">
                    <td colspan="8" class="text-right" style="font-size: 1.1em;">GRAND TOTAL</td>
                    <td class="text-right" style="font-size: 1.1em;">Rp {{ number_format($totalGrand_Final, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- KETERANGAN BOX -->
        @if($invoice->deskripsi || $invoice->catatan || $invoice->biayaUtility->whereNotNull('keterangan')->isNotEmpty())
        <div class="keterangan-box">
            <strong style="font-size: 9px;">Keterangan / Catatan:</strong><br>
            <div style="font-size: 9px;">
                @if($invoice->deskripsi)
                    <div><strong>Deskripsi:</strong> {{ $invoice->deskripsi }}</div>
                @endif
                @if($invoice->catatan)
                    <div><strong>Catatan:</strong> {{ $invoice->catatan }}</div>
                @endif
                @foreach($invoice->biayaUtility as $utility)
                    @if($utility->keterangan)
                        <div><strong>{{ $utility->alatBerat->nama ?? 'Alat' }}:</strong> {{ $utility->keterangan }}</div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
        
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
                    <td>( {{ $invoice->createdBy->name ?? '__________' }} )</td>
                    <td>( __________ )</td>
                    <td>( {{ $invoice->approver->name ?? '__________' }} )</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
