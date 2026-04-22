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
    $pbmDetails = $invoice->pbm_detail_array;
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Biaya PBM - {{ $invoice->nomor_invoice }}</title>
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
            word-wrap: break-word;
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
            margin-top: 30px;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 40px;
        }
        
        .signature-box {
            text-align: center;
            font-size: {{ $currentPaper['fontSize'] }};
            font-weight: bold;
        }
        
        .signature-line {
            margin-top: 85px;
            border-top: 1px solid #333;
            padding-top: 5px;
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
                    <td style="width: 35%;">: {{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('d/M/Y') }}</td>
                    <td style="width: 15%;">Jenis Biaya</td>
                    <td>: {{ $invoice->klasifikasiBiaya->nama ?? 'BIAYA PBM' }}</td>
                </tr>
                <tr>
                    <td style="width: 15%;">Nomor</td>
                    <td style="width: 35%;">: {{ $invoice->nomor_invoice }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>No. Ref</td>
                    <td>: {{ $invoice->referensi ?? $invoice->nomor_invoice }}</td>
                    <td colspan="2"></td>
                </tr>
            </table>
        </div>

        <!-- Detail Biaya PBM -->
        <div style="margin-bottom: 8px;">
            <strong style="font-size: {{ $currentPaper['tableFont'] }};">Rincian Biaya PBM:</strong>
            <table class="table" style="margin-top: 4px; margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 3%;">No</th>
                        <th style="width: 12%;">No. Referensi</th>
                        <th style="width: 15%;">Voyage</th>
                        <th style="width: 12%;">Penerima</th>
                        <th style="width: 18%;">No. Rekening</th>
                        <th style="width: 12%;">Nominal Bayar</th>
                        <th style="width: 8%;">Adm</th>
                        <th style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $totalNominal = 0;
                        $totalAdmin = 0;
                        $grandTotalAll = 0;
                    @endphp
                    @forelse($pbmDetails as $index => $pbm)
                        @php
                            $nominal = (float)str_replace(['.', ','], '', $pbm['nominal_bayar'] ?? 0);
                            $admin = (float)str_replace(['.', ','], '', $pbm['biaya_admin'] ?? 0);
                            $total = (float)str_replace(['.', ','], '', $pbm['grand_total'] ?? 0);
                            
                            $totalNominal += $nominal;
                            $totalAdmin += $admin;
                            $grandTotalAll += $total;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $pbm['referensi'] ?? '-' }}</td>
                            <td>{{ $pbm['nomor_voyage'] ?? '-' }}</td>
                            <td>{{ $pbm['penerima'] ?? '-' }}</td>
                            <td>{{ $pbm['nomor_bank'] ?? '-' }}</td>
                            <td class="text-right">{{ number_format($nominal, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($admin, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <!-- Fallback for legacy single-row data -->
                        @php
                            $nominal = (float)$invoice->nominal_bayar;
                            $admin = (float)$invoice->biaya_admin;
                            $total = $nominal + $admin;
                            $grandTotalAll = $total;
                        @endphp
                        <tr>
                            <td class="text-center">1</td>
                            <td>{{ $invoice->referensi ?? '-' }}</td>
                            <td>{{ $invoice->nomor_voyage ?? '-' }}</td>
                            <td>{{ $invoice->penerima ?? '-' }}</td>
                            <td>{{ $invoice->nomor_bank ?? '-' }}</td>
                            <td class="text-right">{{ number_format($nominal, 0, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($admin, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    @endforelse
                    
                    @if($invoice->biaya_materai > 0 || $invoice->biaya_adjustment != 0 || $invoice->pph > 0)
                        <tr>
                            <td colspan="7" class="text-right">Subtotal</td>
                            <td class="text-right">Rp {{ number_format($grandTotalAll, 0, ',', '.') }}</td>
                        </tr>
                        @if($invoice->biaya_materai > 0)
                        <tr>
                            <td colspan="7" class="text-right">Materai</td>
                            <td class="text-right">Rp {{ number_format($invoice->biaya_materai, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($invoice->biaya_adjustment != 0)
                        <tr>
                            <td colspan="7" class="text-right">Adjustment</td>
                            <td class="text-right">Rp {{ ($invoice->biaya_adjustment > 0 ? '+' : '') . number_format($invoice->biaya_adjustment, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($invoice->pph > 0)
                        <tr>
                            <td colspan="7" class="text-right">PPH</td>
                            <td class="text-right">Rp ({{ number_format($invoice->pph, 0, ',', '.') }})</td>
                        </tr>
                        @endif
                    @endif

                    <tr class="total-row">
                        <td colspan="7" class="text-right"><strong>TOTAL PEMBAYARAN</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($invoice->grand_total ?? $invoice->total, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Keterangan -->
        <div style="margin-bottom: 2px; border: 1.5px solid #333; padding: 4px; min-height: 40px;">
            <strong style="font-size: 8px;">Keterangan:</strong><br>
            <div style="font-size: 9px;">
                {!! nl2br(e($invoice->keterangan)) !!}
            </div>
        </div>

        <!-- Signature Section -->
        <div class="footer" style="margin-top: 40px;">
            <table style="width: 100%; border-collapse: collapse; text-align: center;">
                <tr>
                    <td style="width: 33.33%;"><strong>Dibuat Oleh:</strong></td>
                    <td style="width: 33.33%;"><strong>Diperiksa Oleh:</strong></td>
                    <td style="width: 33.33%;"><strong>Disetujui Oleh:</strong></td>
                </tr>
                <tr>
                    <td style="height: 85px;"></td>
                    <td style="height: 85px;"></td>
                    <td style="height: 85px;"></td>
                </tr>
                <tr>
                    <td>( {{ $invoice->createdBy->name ?? '__________' }} )</td>
                    <td>( __________ )</td>
                    <td>( {{ $invoice->approvedBy->name ?? '__________' }} )</td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('startPrint')?.addEventListener('click', function() {
            window.print();
        });
    </script>
</body>
</html>
