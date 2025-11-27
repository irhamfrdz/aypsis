<!DOCTYPE html>
<html lang="id">
@php
    // Get paper size from request or default to Half-A4
    $paperSize = request('paper_size', 'Half-A4');

    // Define paper dimensions and styles
    $paperMap = [
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '210mm',
            'fontSize' => '11px',
            'headerH1' => '18px',
            'tableFont' => '9px',
            'signatureBottom' => '15mm'
        ],
        'Half-A4' => [
            'size' => '210mm 148.5mm',
            'width' => '210mm',
            'height' => '148.5mm',
            'containerWidth' => '210mm',
            'fontSize' => '9px',
            'headerH1' => '14px',
            'tableFont' => '7px',
            'signatureBottom' => '5mm'
        ]
        ,
        'Folio' => [
            'size' => '215.9mm 330.2mm', // 8.5in x 13in
            'width' => '215.9mm',
            'height' => '330.2mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '11px',
            'headerH1' => '18px',
            'tableFont' => '9px',
            'signatureBottom' => '12mm'
        ]
    ];

    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-A4'];

    // Get unique invoices from tagihan items
    $invoices = $tagihanItems->map(function($item) {
        return $item->invoice;
    })->filter()->unique('id')->values();
    
    $vendorList = $tagihanItems->pluck('vendor')->unique()->filter()->values();
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Pranota {{ $pranota->no_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: {{ $currentPaper['size'] }} portrait;
            margin: 0;
        }

        html, body {
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
            font-family: Arial, sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.2;
            color: #333;
            background: white;
            margin: 0;
            padding: 0;
        }

        .container {
            width: {{ $currentPaper['containerWidth'] }};
            padding: 5mm;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .header-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 9px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            table-layout: fixed;
        }

        .table th,
        .table td {
            border: 1px solid #333;
            padding: 2px 4px;
            text-align: left;
            vertical-align: middle;
            word-wrap: break-word;
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
            color: #333 !important;
            font-weight: bold !important;
            border: 2px solid #333 !important;
        }

        .summary {
            margin-top: 10px;
            text-align: right;
            font-size: 9px;
        }

        .signature-section {
            margin-top: 15px;
            page-break-inside: avoid;
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
        }

        .keterangan-table {
            margin: 8px 0;
        }

        .keterangan-table td {
            border: 2px solid #333;
            padding: 8px;
            height: 40px;
            vertical-align: top;
        }

        @media print {
            .no-print {
                display: none;
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
    <div class="no-print">
        <strong>Paper:</strong>
        <a href="?paper_size=Half-A4" style="text-decoration: none; margin-right: 6px;">Half-A4</a>
        | <a href="?paper_size=A4" style="text-decoration: none; margin: 0 6px;">A4</a>
        | <a href="?paper_size=Folio" style="text-decoration: none; margin-left: 6px;">Folio</a>
        <div style="margin-top: 6px; font-size: 12px;">Current: <strong>{{ $paperSize }}</strong>&nbsp; <small>({{ $currentPaper['width'] }} × {{ $currentPaper['height'] }})</small></div>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-info">
                <div>
                    <strong>PT. ALEXINDO YAKINPRIMA</strong><br>
                    <span>Jalan Pluit Raya No.8 Blok B No.12, Jakarta Utara 14440</span>
                </div>
            </div>
            <div class="header-meta">
                <span><strong>Tanggal: {{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d-M-Y') }}</strong></span>
                <span><strong>{{ $pranota->no_invoice }}</strong></span>
            </div>
            <h1>PRANOTA TAGIHAN KONTAINER</h1>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div>
                @if($vendorList->isNotEmpty())
                    <strong>Vendor:</strong> {{ $vendorList->implode(', ') }}
                @endif
            </div>
            <div>
                <!-- Additional info if needed -->
            </div>
        </div>

        <!-- Invoice Table (if invoices exist) -->
        @if($invoices->isNotEmpty())
        <h3 style="font-size: 12px; font-weight: bold; margin-bottom: 5px; color: #333;">INVOICE YANG DIGUNAKAN:</h3>
        <table class="table" style="margin-bottom: 8px;">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 30%;">Nomor Invoice</th>
                    <th style="width: 25%;">Vendor</th>
                    <th style="width: 22%;">Invoice Vendor</th>
                    <th style="width: 15%;">Total Invoice</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $index => $invoice)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $invoice->nomor_invoice }}</td>
                    <td>{{ $invoice->vendor_name ?: '-' }}</td>
                    <td>
                        @php
                            $invoiceVendor = $tagihanItems->where('invoice_id', $invoice->id)->first()->invoice_vendor ?? '-';
                        @endphp
                        {{ $invoiceVendor }}
                    </td>
                    <td class="text-right">{{ number_format($invoice->total ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" class="text-center">TOTAL INVOICE</td>
                    <td class="text-right">{{ number_format($invoices->sum('total'), 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        <!-- Pranota Table -->
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">No. Kontainer</th>
                    <th style="width: 5%;">Size</th>
                    <th style="width: 15%;">Masa</th>
                    <th style="width: 10%;">DPP</th>
                    <th style="width: 10%;">Adjustment</th>
                    <th style="width: 8%;">PPN</th>
                    <th style="width: 8%;">PPH</th>
                    <th style="width: 12%;">Grand Total</th>
                    <th style="width: 15%;">Invoice Vendor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tagihanItems as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nomor_kontainer }}</td>
                    <td class="text-center">{{ $item->size }}</td>
                    <td class="text-center">
                        @if($item->masa)
                            @php
                                if(strpos($item->masa, ' - ') !== false) {
                                    $dates = explode(' - ', $item->masa);
                                    if(count($dates) == 2) {
                                        try {
                                            $startDate = \Carbon\Carbon::parse($dates[0])->format('d-M-y');
                                            $endDate = \Carbon\Carbon::parse($dates[1])->format('d-M-y');
                                            echo $startDate . ' - ' . $endDate;
                                        } catch (Exception $e) {
                                            echo $item->masa;
                                        }
                                    } else {
                                        echo $item->masa;
                                    }
                                } else {
                                    echo $item->masa;
                                    if(strpos($item->masa, 'bulan') === false && strpos($item->masa, 'hari') === false && is_numeric($item->masa)) {
                                        echo ' hari';
                                    }
                                }
                            @endphp
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->dpp ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->adjustment ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->ppn ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->pph ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->grand_total ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->invoice_vendor ?: '-' }}</td>
                </tr>
                @endforeach
                
                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="4" class="text-center">TOTAL</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('dpp'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('adjustment'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('ppn'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('pph'), 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tagihanItems->sum('grand_total'), 0, ',', '.') }}</td>
                    <td class="text-center">-</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <strong>PEMBAYARAN: Rp {{ number_format($tagihanItems->sum('grand_total'), 0, ',', '.') }}</strong>
        </div>

        <!-- Keterangan Table -->
        <div class="keterangan-table">
            <table style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <tr>
                        <td style="border: 2px solid #333; padding: 8px; height: 40px; vertical-align: top; font-size: 10px;">
                            {{ $pranota->keterangan ?: '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <div style="font-weight: bold; margin-bottom: 25px; font-size: 10px;">Dibuat Oleh</div>
                        <div style="height: 30px; margin-bottom: 8px;"></div>
                        <div style="border-top: 1px solid #333; padding-top: 3px; font-size: 9px;">(_____________)</div>
                    </td>
                    <td>
                        <div style="font-weight: bold; margin-bottom: 25px; font-size: 10px;">Disetujui Oleh</div>
                        <div style="height: 30px; margin-bottom: 8px;"></div>
                        <div style="border-top: 1px solid #333; padding-top: 3px; font-size: 9px;">(_____________)</div>
                    </td>
                    <td>
                        <div style="font-weight: bold; margin-bottom: 25px; font-size: 10px;">Diterima Oleh</div>
                        <div style="height: 30px; margin-bottom: 8px;"></div>
                        <div style="border-top: 1px solid #333; padding-top: 3px; font-size: 9px;">(_____________)</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Auto Print Script -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>