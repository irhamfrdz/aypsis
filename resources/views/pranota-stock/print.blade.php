<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PERMOHONAN TRANSFER - {{ $pranota->nomor_pranota }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 10pt; margin: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16pt; color: #000; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 9pt; color: #666; }
        
        .info-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-table .label { width: 130px; font-weight: bold; font-size: 9pt; }
        .info-table .separator { width: 15px; text-align: center; }
        .info-table td:not(.label):not(.separator) { font-size: 9pt; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; table-layout: fixed; border: 1px solid #000; }
        .items-table th { background-color: #f2f2f2; border: 1px solid #000; padding: 6px 4px; text-align: center; font-size: 8.5pt; text-transform: uppercase; font-weight: bold; }
        .items-table td { border: 1px solid #000; padding: 6px 4px; font-size: 8.5pt; word-wrap: break-word; vertical-align: middle; }
        
        .footer { margin-top: 40px; width: 100%; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { width: 33.33%; text-align: center; vertical-align: bottom; height: 100px; }
        .signature-line { border-bottom: 1px solid #000; width: 160px; margin: 0 auto 5px; }
        .signature-label { font-size: 9pt; font-weight: bold; }

        .keterangan-section { margin-top: 15px; padding: 8px; border: 1px dashed #ccc; background-color: #fdfdfd; }
        .keterangan-title { font-weight: bold; font-size: 9pt; margin-bottom: 4px; text-decoration: underline; }
        .keterangan-text { font-size: 9pt; line-height: 1.3; color: #555; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        @media print {
            body { margin: 5mm; }
            .no-print { display: none; }
            .header { border-bottom-color: #000; }
            .items-table th { background-color: #eee !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PERMOHONAN TRANSFER</h1>

    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nomor Pranota</td>
            <td class="separator">:</td>
            <td><strong>{{ $pranota->nomor_pranota }}</strong></td>
            <td class="label">Penerima</td>
            <td class="separator">:</td>
            <td>{{ $pranota->penerima ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Pranota</td>
            <td class="separator">:</td>
            <td>{{ $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('d F Y') : '-' }}</td>
            <td class="label">Vendor</td>
            <td class="separator">:</td>
            <td>{{ $pranota->vendor ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Accurate</td>
            <td class="separator">:</td>
            <td>{{ $pranota->nomor_accurate ?? '-' }}</td>
            <td class="label">Rekening</td>
            <td class="separator">:</td>
            <td>{{ $pranota->rekening ?? '-' }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 3%;">NO</th>
                <th style="width: 10%;">TANGGAL</th>
                <th style="width: 18%;">KAPAL/ALAT/MOBIL/BUNTUT/LAIN</th>
                <th style="width: 9%;">TYPE</th>
                <th style="width: 15%;">NAMA BARANG</th>
                <th style="width: 12%;">KETERANGAN</th>
                <th style="width: 12%;">TOTAL BELANJA</th>
                <th style="width: 5%;">QTY</th>
                <th style="width: 8%;">SATUAN</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; $grandTotal = 0; @endphp
            @if(is_array($pranota->items))
                @foreach($pranota->items as $item)
                @php
                    $hargaSatuan = floatval($item['harga'] ?? 0);
                    $jumlahItems = floatval($item['jumlah'] ?? 0);
                    $adjustmentItem = floatval($item['adjustment'] ?? 0);
                    $totalBelanja = ($hargaSatuan * $jumlahItems) + $adjustmentItem;
                    $grandTotal += $totalBelanja;
                @endphp
                <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td class="text-center">{{ isset($item['tanggal']) ? \Carbon\Carbon::parse($item['tanggal'])->format('d/M/Y') : '-' }}</td>
                    <td class="text-center">{{ $item['reference'] ?? '-' }}</td>
                    <td class="text-center">{{ $item['type'] ?? '-' }}</td>
                    <td><span class="font-bold">{{ $item['nama_barang'] ?? ($item['nama'] ?? '-') }}</span></td>
                    <td>{{ $item['keterangan'] ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($jumlahItems, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item['satuan'] ?? '-' }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="6" class="text-right px-4">SUBTOTAL</td>
                    <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
                @if($pranota->adjustment != 0)
                <tr style="font-weight: bold;">
                    <td colspan="6" class="text-right px-4">ADJUSTMENT</td>
                    <td class="text-right">Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
                <tr style="background-color: #eee; font-weight: bold; font-size: 10pt;">
                    <td colspan="6" class="text-right px-4">TOTAL AKHIR</td>
                    <td class="text-right">Rp {{ number_format($grandTotal + $pranota->adjustment, 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
                @endif
            @else
                <tr><td colspan="9" class="text-center" style="color: #999;">Tidak ada data item</td></tr>
            @endif
        </tbody>
    </table>

    @if($pranota->keterangan)
    <div class="keterangan-section">
        <div class="keterangan-title">Catatan Tambahan:</div>
        <div class="keterangan-text">{{ $pranota->keterangan }}</div>
    </div>
    @endif

    <div style="width: 250px; margin-left: auto; margin-right: 20px; margin-top: 20px; margin-bottom: 30px;">
        @php
            $displayTypes = [
                'Perlengkapan' => 'Perlengkapan',
                'Transport' => 'Transportasi',
                'Pemakaian' => 'Pemakaian',
                'Perbaikan' => 'Perbaikan'
            ];
            $typeTotals = array_fill_keys(array_values($displayTypes), 0);
            
            // Track sub-details for EACH category
            $categoryDetails = [];
            foreach ($displayTypes as $label) {
                $categoryDetails[$label] = [
                    'Kendaraan' => 0,
                    'Truck' => 0,
                    'Kapal' => 0,
                    'Alat Berat' => 0,
                    'Buntut' => 0,
                    'Lain-lain' => 0
                ];
            }
            $otherTotal = 0;
            $summaryGrandTotal = 0;

            if (is_array($pranota->items)) {
                foreach ($pranota->items as $item) {
                    $itemType = $item['type'] ?? '';
                    $refType = $item['reference_type'] ?? '';
                    $itemNominal = (floatval($item['harga'] ?? 0) * floatval($item['jumlah'] ?? 0)) + floatval($item['adjustment'] ?? 0);
                    
                    $matched = false;
                    foreach ($displayTypes as $search => $label) {
                        if (stripos($itemType, $search) !== false) {
                            $typeTotals[$label] += $itemNominal;
                            $matched = true;
                            
                            if ($refType && isset($categoryDetails[$label][$refType])) {
                                $categoryDetails[$label][$refType] += $itemNominal;
                            }
                            break;
                        }
                    }
                    if (!$matched) {
                        $otherTotal += $itemNominal;
                    }
                    $summaryGrandTotal += $itemNominal;
                }
            }
        @endphp
        <table style="width: 100%; border-collapse: collapse; font-size: 10pt; font-family: 'Arial', sans-serif;">
            @foreach($typeTotals as $label => $val)
            <tr style="border-bottom: 1px solid #f0f0f0;">
                <td style="padding: 4px 0; width: 60%; font-weight: 500;">{{ $label }}</td>
                <td style="text-align: right; padding: 4px 0;">{{ $val > 0 ? number_format($val, 0, ',', '.') : '-' }}</td>
            </tr>
            @foreach($categoryDetails[$label] as $subLabel => $subVal)
                @if($subVal > 0)
                <tr>
                    <td style="padding: 1px 0 1px 20px; font-size: 8.5pt; color: #888; font-style: italic;">{{ $subLabel }}</td>
                    <td style="text-align: right; padding: 1px 0; font-size: 8.5pt; color: #888;">{{ number_format($subVal, 0, ',', '.') }}</td>
                </tr>
                @endif
            @endforeach
            @endforeach
            
            @if($otherTotal > 0)
            <tr style="border-bottom: 1px solid #f0f0f0;">
                <td style="padding: 4px 0; font-weight: 500;">Lain-lain</td>
                <td style="text-align: right; padding: 4px 0;">{{ number_format($otherTotal, 0, ',', '.') }}</td>
            </tr>
            @endif
            
            <tr style="border-top: 1.5px solid #000; font-weight: bold; font-size: 11pt;">
                <td style="padding: 8px 0;">Total Biaya</td>
                <td style="text-align: right; padding: 8px 0;">{{ number_format($summaryGrandTotal, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-label">Dibuat Oleh</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-label">Diperiksa Oleh</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-label">Disetujui Oleh</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="no-print" style="margin-top: 50px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #444; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
            Cetak Laporan
        </button>
    </div>

    <script>
        // Auto print or other logic
    </script>
</body>
</html>
