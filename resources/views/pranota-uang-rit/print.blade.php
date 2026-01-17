<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RITASI SUPIR - {{ $pranotaUangRit->no_pranota }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #f3f4f6;
        }
        
        .print-toolbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .print-toolbar h2 {
            color: white;
            margin: 0;
            font-size: 16px;
        }
        
        .print-toolbar-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-print {
            background: #10b981;
            color: white;
        }
        
        .btn-print:hover {
            background: #059669;
            transform: translateY(-1px);
        }
        
        .btn-back {
            background: #6b7280;
            color: white;
        }
        
        .btn-back:hover {
            background: #4b5563;
            transform: translateY(-1px);
        }
        
        .print-container {
            max-width: 210mm;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-decoration: underline;
        }
        
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-section div {
            margin-bottom: 3px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table, th, td {
            border: 1px solid #000;
        }
        
        th {
            background-color: #f0f0f0;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }
        
        td {
            padding: 4px;
            vertical-align: top;
            font-size: 10px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        
        .footer-notes {
            margin-top: 20px;
            font-size: 10px;
        }
        
        @media print {
            .print-toolbar {
                display: none !important;
            }
            
            body {
                background-color: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .print-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="print-toolbar">
        <h2><i class="fas fa-file-invoice-dollar"></i> Print Ritasi Supir - {{ $pranotaUangRit->no_pranota }}</h2>
        <div class="print-toolbar-buttons">
            <button onclick="window.print()" class="btn btn-print">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('pranota-uang-rit.index') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="print-container">
        <div class="header">
            <h1>RITASI SUPIR</h1>
        </div>
        
        <div class="info-section">
            <div>GAJI SUPIR TGL: {{ $pranotaUangRit->tanggal ? $pranotaUangRit->tanggal->format('d M Y') : '' }}</div>
            <div style="text-align: right; margin-top: -20px;">{{ $pranotaUangRit->no_pranota }}</div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 8%;">NIK</th>
                    <th style="width: 28%;">Supir</th>
                    <th style="width: 6%;">Rit</th>
                    <th style="width: 11%;">Total Uang</th>
                    <th style="width: 9%;">Hutang</th>
                    <th style="width: 9%;">Tabungan</th>
                    <th style="width: 8%;">BPJS</th>
                    <th style="width: 16%;">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @if($supirDetails && $supirDetails->count() > 0)
                    @foreach($supirDetails as $detail)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ str_pad($no-1, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="text-left">{{ strtoupper($detail->supir_nama) }}</td>
                        <td class="text-center">{{ $detail->total_uang_supir > 0 ? round($detail->total_uang_supir / 85000) : 0 }}</td>
                        <td class="text-right">{{ number_format($detail->total_uang_supir, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail->hutang, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail->tabungan, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail->bpjs, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($detail->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @else
                    {{-- Fallback jika tidak ada supir details --}}
                    @php
                        $suratJalanArray = explode(', ', $pranotaUangRit->no_surat_jalan);
                        $supirArray = explode(', ', $pranotaUangRit->supir_nama);
                        $uniqueSupir = array_unique($supirArray);
                    @endphp
                    @foreach($uniqueSupir as $index => $supir)
                    @php
                        $totalUangSupir = $pranotaUangRit->uang_rit_supir / count($uniqueSupir);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ str_pad($no-1, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="text-left">{{ strtoupper(trim($supir)) }}</td>
                        <td class="text-center">{{ $totalUangSupir > 0 ? round($totalUangSupir / 85000) : 0 }}</td>
                        <td class="text-right">{{ number_format($totalUangSupir, 0, ',', '.') }}</td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right">-</td>
                        <td class="text-right">{{ number_format($pranotaUangRit->grand_total_bersih / count($uniqueSupir), 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-center"><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>
                        @if($supirDetails && $supirDetails->count() > 0)
                            {{ $supirDetails->sum(function($detail) { return $detail->total_uang_supir > 0 ? round($detail->total_uang_supir / 85000) : 0; }) }}
                        @else
                            @php
                                $totalRit = 0;
                                $supirArray = explode(', ', $pranotaUangRit->supir_nama);
                                $uniqueSupir = array_unique($supirArray);
                                foreach($uniqueSupir as $supir) {
                                    $totalUangSupir = $pranotaUangRit->uang_rit_supir / count($uniqueSupir);
                                    $totalRit += $totalUangSupir > 0 ? round($totalUangSupir / 85000) : 0;
                                }
                                echo $totalRit;
                            @endphp
                        @endif
                    </strong></td>
                    <td class="text-right"><strong>
                        @if($supirDetails && $supirDetails->count() > 0)
                            {{ number_format($supirDetails->sum('total_uang_supir'), 0, ',', '.') }}
                        @else
                        {{ number_format($pranotaUangRit->uang_rit_supir, 0, ',', '.') }}
                    @endif
                </strong></td>
                <td class="text-right"><strong>
                    @if($supirDetails && $supirDetails->count() > 0)
                        {{ number_format($supirDetails->sum('hutang'), 0, ',', '.') }}
                    @else
                        {{ number_format(0, 0, ',', '.') }}
                    @endif
                </strong></td>
                <td class="text-right"><strong>
                    @if($supirDetails && $supirDetails->count() > 0)
                        {{ number_format($supirDetails->sum('tabungan'), 0, ',', '.') }}
                    @else
                        {{ number_format(0, 0, ',', '.') }}
                    @endif
                </strong></td>
                <td class="text-right"><strong>
                    @if($supirDetails && $supirDetails->count() > 0)
                        {{ number_format($supirDetails->sum('bpjs'), 0, ',', '.') }}
                    @else
                        {{ number_format(0, 0, ',', '.') }}
                    @endif
                </strong></td>
                <td class="text-right"><strong>
                    @if($supirDetails && $supirDetails->count() > 0)
                        {{ number_format($supirDetails->sum('grand_total'), 0, ',', '.') }}
                    @else
                        {{ number_format($pranotaUangRit->grand_total_bersih, 0, ',', '.') }}
                    @endif
                </strong></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer-notes">
        <div>Dibuat pada: {{ $pranotaUangRit->created_at ? $pranotaUangRit->created_at->format('d/m/Y H:i') : '' }}</div>
        <div>Status: {{ $pranotaUangRit->status_label }}</div>
        @if($pranotaUangRit->keterangan)
        <div>Keterangan: {{ $pranotaUangRit->keterangan }}</div>
        @endif
    </div>

    <script>
        // Auto print when page loads (for when opened via link)
        window.addEventListener('load', function() {
            // Small delay to ensure content is fully rendered
            setTimeout(function() {
                // Only auto-print if opened in a new window
                if (window.opener) {
                    window.print();
                }
            }, 500);
        });
    </script>
</body>
</html>