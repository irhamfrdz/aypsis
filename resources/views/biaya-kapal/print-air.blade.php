<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Biaya Air - {{ $biayaKapal->nomor_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 12px;
            font-weight: bold;
        }
        
        .info-section {
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .info-table {
            width: 100%;
        }
        
        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        
        .info-label {
            font-weight: bold;
            width: 130px;
        }
        
        .section-header {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .custom-table th, 
        .custom-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
        }
        
        .custom-table th {
            text-align: center;
            font-weight: bold;
            background-color: #fff;
            border-bottom: 2px solid #000; /* Thicker border for header */
        }
        
        .custom-table tr.total-row td {
            background-color: #e9ecef;
            font-weight: bold;
            border-top: 2px solid #000;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .keterangan-box {
            border: 2px solid #000;
            padding: 10px;
            margin-top: 20px;
            min-height: 60px;
        }
        
        .keterangan-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .footer {
            margin-top: 30px;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        
        .signature-table td {
            border: none;
            text-align: center;
            padding: 5px;
            width: 33.33%;
        }
        
        .signature-space {
            height: 60px;
        }
        
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            @page { margin: 1cm; size: auto; }
        }
        
        .btn-print {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .btn-back {
            position: fixed;
            top: 10px;
            right: 70px;
            padding: 5px 10px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 3px;
            text-decoration: none;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <button class="btn-print no-print" onclick="window.print()">Print</button>
    <a href="{{ route('biaya-kapal.index') }}" class="btn-back no-print">Kembali</a>

    <div class="header">
        <h1>PERMOHONAN TRANSFER</h1>
    </div>
    
    @php
        $vendorDisplay = $biayaKapal->nama_vendor ?? ($biayaKapal->airDetails->pluck('vendor')->filter()->unique()->values()->first() ?? '-');
        $penerimaDisplay = $biayaKapal->penerima ?? ($biayaKapal->airDetails->pluck('penerima')->filter()->unique()->values()->first() ?? '-');
        $rekeningDisplay = $biayaKapal->nomor_rekening ?? ($biayaKapal->airDetails->pluck('nomor_rekening')->filter()->unique()->values()->first() ?? '-');
        
        // Calculate Totals
        $totalQty = 0;
        $totalWaterCost = 0;
        $totalJasaAir = 0;
        $totalBiayaAgen = 0;
        $totalPPH = 0;
        $totalGrandTotal = 0;
        
        foreach($biayaKapal->airDetails as $detail) {
            $totalQty += $detail->kuantitas;
            
            // Perhitungan Harga Murni Air (Qty * Harga Satuan)
            // Jika harga null/0, maka 0.
            $waterCost = $detail->kuantitas * ($detail->harga ?? 0);
            $totalWaterCost += $waterCost;
            
            $totalJasaAir += $detail->jasa_air;
            $totalBiayaAgen += $detail->biaya_agen;
            $totalPPH += $detail->pph;
            $totalGrandTotal += $detail->grand_total;
        }
    @endphp

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td class="info-label">Nomor Invoice</td>
                <td>: <strong>{{ $biayaKapal->nomor_invoice }}</strong></td>
                <td class="info-label">Tanggal</td>
                <td>: {{ $biayaKapal->tanggal->format('d/M/Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Penerima</td>
                <td>: {{ $penerimaDisplay }}</td>
                <td class="info-label">Vendor</td>
                <td>: {{ $vendorDisplay }}</td>
            </tr>
            <tr>
                @if($biayaKapal->klasifikasiBiaya)
                <td class="info-label">Jenis Biaya</td>
                <td>: {{ $biayaKapal->klasifikasiBiaya->nama }}</td>
                @else
                <td class="info-label"></td>
                <td></td>
                @endif
                <td class="info-label">Nomor Rekening</td>
                <td>: {{ $rekeningDisplay }}</td>
            </tr>

        </table>
    </div>

    <!-- TABLE 1: DETAIL BIAYA KAPAL -->
    <div class="section-header">Detail Biaya Kapal:</div>
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">Referensi</th>
                <th>Jenis Biaya</th>
                <th style="width: 20%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($biayaKapal->airDetails as $index => $detail)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $detail->tanggal_invoice_vendor ? \Carbon\Carbon::parse($detail->tanggal_invoice_vendor)->format('d/M/Y') : '-' }}</td>
                <td>{{ $detail->nomor_referensi ?? '-' }}</td>
                <td>Biaya Air {{ $detail->kapal ?? '' }} {{ $detail->voyage ? '('.$detail->voyage.')' : '' }}</td>
                <td class="text-right">Rp {{ number_format($detail->grand_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada data detail.</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" style="text-align: left; padding-left: 10px;">TOTAL PEMBAYARAN</td>
                <td class="text-right">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TABLE 2: DETAIL BARANG (GABUNGAN) -->
    <div class="section-header">Detail Barang:</div>
    <div class="section-header">Detail Barang (Gabungan Semua Kapal)</div>
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th>Jenis Barang</th>
                <th style="width: 15%;">Jumlah</th>
                <th style="width: 20%;">Harga Satuan</th>
                <th style="width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            
            <!-- Row 1: Air Tawar (Base Cost) -->
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>AIR TAWAR</td>
                <td class="text-center">{{ number_format($totalQty, 2, ',', '.') }}</td>
                <td class="text-right">
                    @if($totalQty > 0)
                        Rp {{ number_format($totalWaterCost / $totalQty, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($totalWaterCost, 0, ',', '.') }}</td>
            </tr>
            
            <!-- Row 2: Jasa Air (Jika ada value) -->
            @if($totalJasaAir > 0)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>JASA AIR</td>
                <td class="text-center">1</td>
                <td class="text-right">Rp {{ number_format($totalJasaAir, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalJasaAir, 0, ',', '.') }}</td>
            </tr>
            @endif
            
            <!-- Row 3: Biaya Agen (If exists) -->
            @if($totalBiayaAgen > 0)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>BIAYA AGEN</td>
                <td class="text-center">1</td>
                <td class="text-right">Rp {{ number_format($totalBiayaAgen, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalBiayaAgen, 0, ',', '.') }}</td>
            </tr>
            @endif
            
            <!-- Row 4: PPH (If exists) -->
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
                <td colspan="4" style="text-align: left; padding-left: 10px;">TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    
    <!-- KETERANGAN BOX -->
    <div class="keterangan-box">
        <div class="keterangan-title">Keterangan:</div>
        <div>
            @php
                $keterangan = $biayaKapal->keterangan ?? '';
                // Split by common detail headers used in the controller
                if (stripos($keterangan, 'Detail Biaya Air:') !== false) {
                    $keterangan = explode('Detail Biaya Air:', $keterangan)[0];
                }
                if (stripos($keterangan, 'Detail Barang Buruh:') !== false) {
                    $keterangan = explode('Detail Barang Buruh:', $keterangan)[0];
                }
                if (stripos($keterangan, 'Detail Biaya TKBM:') !== false) {
                    $keterangan = explode('Detail Biaya TKBM:', $keterangan)[0];
                }
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
                <td class="signature-space"></td>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
            </tr>
            <tr>
                <td>___________</td>
                <td>___________</td>
                <td>___________</td>
            </tr>
        </table>
    </div>
</body>
</html>
