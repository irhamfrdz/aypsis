<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RITASI Kenek - {{ $pranotaUangRitKenek->no_pranota }}</title>
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
        
        .no-print {
            display: none;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="display: block; text-align: center; margin-bottom: 10px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Print
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            ❌ Tutup
        </button>
    </div>

    <div class="header">
        <h1>RITASI Kenek</h1>
    </div>
    
    <div class="info-section">
        <div>GAJI Kenek TGL: {{ $pranotaUangRitKenek->tanggal ? $pranotaUangRitKenek->tanggal->format('d M Y') : '' }}</div>
        <div style="text-align: right; margin-top: -20px;">{{ $pranotaUangRitKenek->no_pranota }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 10%;">NIK</th>
                <th style="width: 25%;">Kenek</th>
                <th style="width: 8%;">Total</th>
                <th style="width: 5%;">Rit</th>
                <th style="width: 12%;">Total</th>
                <th style="width: 10%;">Hutang</th>
                <th style="width: 10%;">Tabungan</th>
                <th style="width: 10%;">BPJS</th>
                <th style="width: 12%;">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @if($KenekDetails && $KenekDetails->count() > 0)
                @foreach($KenekDetails as $detail)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ str_pad($no-1, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="text-left">{{ strtoupper($detail->Kenek_nama) }}</td>
                    <td class="text-center">
                        @php
                            $jumlahRit = $groupedPranota->where('Kenek_nama', $detail->Kenek_nama)->count();
                        @endphp
                        {{ $jumlahRit }}
                    </td>
                    <td class="text-center">Rit</td>
                    <td class="text-right">{{ number_format($detail->total_uang_Kenek, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->hutang, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->tabungan, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->bpjs, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($detail->grand_total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                {{-- Fallback jika tidak ada Kenek details --}}
                @php
                    $suratJalanArray = explode(', ', $pranotaUangRitKenek->no_surat_jalan);
                    $KenekArray = explode(', ', $pranotaUangRitKenek->Kenek_nama);
                    $uniqueKenek = array_unique($KenekArray);
                @endphp
                @foreach($uniqueKenek as $index => $Kenek)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ str_pad($no-1, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="text-left">{{ strtoupper(trim($Kenek)) }}</td>
                    <td class="text-center">
                        {{ array_count_values($KenekArray)[trim($Kenek)] ?? 1 }}
                    </td>
                    <td class="text-center">Rit</td>
                    <td class="text-right">{{ number_format($pranotaUangRitKenek->uang_rit_Kenek / count($uniqueKenek), 0, ',', '.') }}</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">{{ number_format($pranotaUangRitKenek->grand_total_bersih / count($uniqueKenek), 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-center"><strong>TOTAL</strong></td>
                <td class="text-center">
                    <strong>
                        @if($KenekDetails && $KenekDetails->count() > 0)
                            {{ $groupedPranota->count() }}
                        @else
                            {{ count(explode(', ', $pranotaUangRitKenek->no_surat_jalan)) }}
                        @endif
                    </strong>
                </td>
                <td class="text-center"><strong></strong></td>
                <td class="text-right"><strong>{{ number_format($pranotaUangRitKenek->total_uang, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($pranotaUangRitKenek->total_hutang, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($pranotaUangRitKenek->total_tabungan, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($pranotaUangRitKenek->total_bpjs, 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($pranotaUangRitKenek->grand_total_bersih, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer-notes">
        <div>Dibuat pada: {{ $pranotaUangRitKenek->created_at ? $pranotaUangRitKenek->created_at->format('d/m/Y H:i') : '' }}</div>
        <div>Status: {{ $pranotaUangRitKenek->status_label }}</div>
        @if($pranotaUangRitKenek->keterangan)
        <div>Keterangan: {{ $pranotaUangRitKenek->keterangan }}</div>
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
