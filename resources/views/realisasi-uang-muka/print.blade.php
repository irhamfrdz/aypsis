<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Realisasi Uang Muka - {{ $realisasi->nomor_pembayaran }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
            background: white;
            width: 100vw;
            height: 100vh;
            box-sizing: border-box;
        }

        .container {
            max-width: 100%;
            width: 100%;
            height: 100vh;
            margin: 0;
            background: white;
            border: 1px solid #000;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            padding: 2mm;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding: 1mm;
            background: #f8f9fa;
            flex-shrink: 0;
        }

        .header h1 {
            margin: 0;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header h2 {
            margin: 1px 0 0 0;
            font-size: 9px;
            color: #666;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            padding: 2mm;
            border-bottom: 1px solid #ddd;
            background: #fff;
            gap: 5mm;
            flex-shrink: 0;
        }

        .info-left, .info-right {
            width: 48%;
            flex: 1;
        }

        .info-item {
            display: flex;
            margin-bottom: 4px;
            align-items: center;
            padding: 1px 0;
        }

        .info-label {
            font-weight: bold;
            width: 70px;
            flex-shrink: 0;
            font-size: 10px;
            padding-right: 5px;
        }

        .info-value {
            flex: 1;
            border-bottom: 1px solid #333;
            min-height: 14px;
            padding-left: 3px;
            padding-top: 1px;
            padding-bottom: 1px;
            font-size: 10px;
        }



        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1mm;
            font-size: 10px;
            height: 100%;
        }

        .table th,
        .table td {
            border: 1px solid #333;
            padding: 1mm;
            text-align: left;
            min-height: 8mm;
        }

        .table th {
            text-align: center;
            padding: 2px 1px;
            font-weight: bold;
            font-size: 10px;
            line-height: 1;
            height: 4mm;
            vertical-align: middle;
        }

        .table td {
            font-size: 10px;
        }

        .table .text-right {
            text-align: right;
        }

        .table .text-center {
            text-align: center;
        }

        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .table tr:hover {
            background-color: #f3f4f6;
        }

        .content-section {
            padding: 1mm;
            flex: 1;
        }

        .section-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 2px;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            padding-bottom: 1px;
        }

        .summary {
            margin-top: 1mm;
            text-align: right;
            padding: 1mm;
        }

        .summary-item {
            margin-bottom: 4px;
            font-size: 10px;
        }

        .summary-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
            text-align: right;
            padding-right: 5px;
        }

        .total-amount {
            font-size: 12px;
            font-weight: bold;
            color: #059669;
            border-top: 1px solid #333;
            padding-top: 2mm;
            margin-top: 2mm;
        }



        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 2mm;
            padding: 1mm;
            flex-shrink: 0;
        }

        .signature-box {
            text-align: center;
            width: 120px;
            font-size: 8px;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 8px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 25px;
            margin-bottom: 3px;
        }

        .signature-name {
            font-size: 8px;
            color: #666;
        }



        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .print-button:hover {
            background: #0056b3;
        }

        @media print {
            @page {
                size: 215mm 165mm;
                margin: 3mm;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                font-family: Arial, sans-serif;
                width: 209mm;
                height: 159mm;
                overflow: hidden;
            }

            .container {
                width: 100%;
                height: 100%;
                max-width: none;
                border: 1px solid #000;
                margin: 0;
                padding: 2mm;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
            }

            .header {
                padding: 1mm;
                margin-bottom: 1mm;
            }

            .header h1 {
                font-size: 14px;
                margin-bottom: 1mm;
            }

            .header h2 {
                font-size: 10px;
            }

            .info-section {
                padding: 2mm;
                margin-bottom: 1.5mm;
                gap: 5mm;
            }

            .print-button {
                display: none !important;
            }

            .table {
                margin-bottom: 2mm;
                font-size: 11px;
            }

            .table th,
            .table td {
                padding: 1mm;
                min-height: auto;
            }

            .table th {
                text-align: center;
                padding: 2px 1px;
                font-weight: bold;
                font-size: 10px;
                line-height: 1;
                height: 2.5mm;
                max-height: 2.5mm;
                vertical-align: middle;
            }

            .content-section {
                padding: 1mm;
                flex: 0 0 auto;
                display: flex;
                flex-direction: column;
            }

            .summary {
                margin-top: 1mm;
                padding: 1mm;
            }

            .signatures {
                margin-top: 1mm;
                padding: 1mm;
            }

            .signature-box {
                width: 100px;
                font-size: 9px;
            }

            .signature-line {
                height: 20px;
                margin-bottom: 2mm;
            }
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Dokumen</button>

    <div class="container">
        <!-- Header -->
        <div class="header" style="position: relative;">
            <div style="position: absolute; left: 0; top: 1mm; font-size: 13px; font-weight: bold;">
                {{ $realisasi->nomor_pembayaran }}
            </div>
            <div style="position: absolute; right: 0; top: 1mm; font-size: 13px; font-weight: bold;">
                {{ $realisasi->tanggal_pembayaran->format('d M Y') }}
            </div>
            <h1>REALISASI UANG MUKA</h1>
            <h2>PT. ALEXINDO YAKINPRIMA</h2>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-item">
                    <span class="info-label">Kegiatan:</span>
                    <span class="info-value">{{ $realisasi->masterKegiatan->nama_kegiatan ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Akun Kas/Bank:</span>
                    <span class="info-value">{{ $realisasi->kasBankAkun->nama_akun ?? '-' }}</span>
                </div>
            </div>
            <div class="info-right">
                @if($uangMukaData && $uangMukaData->nomor_pembayaran)
                <div class="info-item">
                    <span class="info-label">Nomor Voucher UM:</span>
                    <span class="info-value" style="font-weight: bold; color: #059669;">{{ $uangMukaData->nomor_pembayaran }}</span>
                </div>
                @elseif($realisasi->dp_amount > 0)
                <div class="info-item">
                    <span class="info-label">Nomor Voucher UM:</span>
                    <span class="info-value" style="color: #dc2626; font-style: italic;">Linking...</span>
                </div>
                @endif
                @if($uangMukaData && $uangMukaData->tanggal_pembayaran)
                <div class="info-item">
                    <span class="info-label">Tanggal Voucher UM:</span>
                    <span class="info-value" style="font-weight: bold; color: #059669;">{{ $uangMukaData->tanggal_pembayaran->format('d M Y') }}</span>
                </div>
                @elseif($realisasi->dp_amount > 0)
                <div class="info-item">
                    <span class="info-label">Tanggal Voucher UM:</span>
                    <span class="info-value" style="color: #dc2626; font-style: italic;">Linking...</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Table Section -->
        <div class="content-section" style="flex: 0 0 auto; display: flex; flex-direction: column;">
            <div class="section-title">Detail Realisasi</div>
            <table class="table" style="margin-bottom: 2mm;"
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">
                        @if($realisasi->item_type === 'mobil')
                            Mobil/Kendaraan
                        @elseif($realisasi->item_type === 'penerima')
                            Penerima
                        @else
                            Supir
                        @endif
                    </th>
                    <th style="width: 17%;">Uang Muka</th>
                    <th style="width: 17%;">Realisasi</th>
                    <th style="width: 16%;">Selisih</th>
                    <th style="width: 20%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $jumlahPerItem = $realisasi->jumlah_per_supir ?? [];
                    $totalRealisasi = 0;
                    $totalUangMuka = 0;
                @endphp
                @forelse($itemList as $index => $item)
                    @php
                        $itemId = $item->id;
                        $realisasiAmount = $jumlahPerItem[$itemId] ?? 0;
                        $totalRealisasi += $realisasiAmount;

                        // Calculate uang muka per item
                        $uangMukaAmount = 0;
                        $tanggalUangMuka = null;

                        if($uangMukaData) {
                            $tanggalUangMuka = $uangMukaData->tanggal_pembayaran;

                            // Check if this specific item received uang muka
                            if($realisasi->item_type === 'mobil') {
                                // For mobil, check if mobil_id matches
                                if(isset($uangMukaData->mobil_id) && $uangMukaData->mobil_id == $itemId) {
                                    $uangMukaAmount = $uangMukaData->total_pembayaran;
                                }
                            } elseif($realisasi->item_type === 'penerima') {
                                // For penerima, check if any supir_ids in uang muka matches
                                $uangMukaSupirIds = $uangMukaData->supir_ids ?? [];
                                if(in_array($itemId, $uangMukaSupirIds)) {
                                    // If there's jumlah_per_supir, use that specific amount
                                    $uangMukaPerSupir = $uangMukaData->jumlah_per_supir ?? [];
                                    if(isset($uangMukaPerSupir[$itemId])) {
                                        $uangMukaAmount = $uangMukaPerSupir[$itemId];
                                    } else {
                                        // Otherwise divide total by number of recipients
                                        $uangMukaAmount = count($uangMukaSupirIds) > 0 ? $uangMukaData->total_pembayaran / count($uangMukaSupirIds) : 0;
                                    }
                                }
                            } elseif($realisasi->item_type === 'supir') {
                                // For supir (OB), use jumlah_per_supir
                                $uangMukaPerSupir = $uangMukaData->jumlah_per_supir ?? [];
                                $uangMukaAmount = $uangMukaPerSupir[$itemId] ?? 0;
                            }
                        }

                        // FALLBACK: If no uangMukaAmount calculated but dp_amount exists
                        // Use dp_amount and distribute equally among items
                        if($uangMukaAmount == 0 && $realisasi->dp_amount > 0) {
                            $itemCount = count($itemList);
                            $uangMukaAmount = $itemCount > 0 ? $realisasi->dp_amount / $itemCount : 0;
                            // Use realisasi tanggal as fallback for uang muka date
                            $tanggalUangMuka = $realisasi->tanggal_pembayaran;
                        }
                        $totalUangMuka += $uangMukaAmount;

                        $selisih = $realisasiAmount - $uangMukaAmount;

                        // Get individual keterangan for this item
                        $keteranganPerItem = $realisasi->keterangan_per_supir ?? [];
                        $itemKeterangan = $keteranganPerItem[$itemId] ?? '';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            @if($realisasi->item_type === 'mobil')
                                <strong>{{ $item->plat }}</strong><br>
                                <small style="color: #666;">{{ $item->aktiva ?? '-' }}</small>
                            @else
                                <strong>{{ $item->nama_lengkap }}</strong><br>
                                <small style="color: #666;">
                                    @if($item->nama_panggilan)
                                        {{ $item->nama_panggilan }} •
                                    @endif
                                    NIK: {{ $item->nik }}
                                </small>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($uangMukaAmount > 0)
                                Rp {{ number_format($uangMukaAmount, 0, ',', '.') }}
                            @else
                                <span style="color: #999;">-</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <strong>Rp {{ number_format($realisasiAmount, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-right">
                            @if($selisih != 0)
                                <span style="font-weight: bold;">
                                    Rp {{ number_format(abs($selisih), 0, ',', '.') }}
                                </span>
                            @else
                                <span style="font-weight: bold;">
                                    Rp 0
                                </span>
                            @endif
                        </td>
                        <td style="font-size: 10px;">
                            @if($itemKeterangan)
                                {{ $itemKeterangan }}
                            @else
                                <span style="color: #999;">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="color: #999;">
                            Tidak ada data
                        </td>
                    </tr>
                @endforelse

                <!-- Total Row -->
                @if($itemList->count() > 0)
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td class="text-center" colspan="2">TOTAL</td>
                    <td class="text-right">Rp {{ number_format($totalUangMuka, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                    <td class="text-right">
                        @php $totalSelisih = $totalRealisasi - $totalUangMuka; @endphp
                        @if($totalSelisih != 0)
                            <span>Rp {{ number_format(abs($totalSelisih), 0, ',', '.') }}</span>
                        @else
                            <span>Rp 0</span>
                        @endif
                    </td>
                    <td></td>
                </tr>
                @endif
            </tbody>
            </table>

            <!-- Summary -->
            <div class="summary">
            @if($realisasi->dp_amount > 0)
            <div class="summary-item">
                <span class="summary-label">Uang Muka:</span>
                <span>Rp {{ number_format($realisasi->dp_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="summary-item total-amount">
                <span class="summary-label">TOTAL REALISASI:</span>
                <span>Rp {{ number_format($realisasi->total_pembayaran, 0, ',', '.') }}</span>
            </div>
            @if($realisasi->dp_amount > 0)
            <div class="summary-item" style="margin-top: 10px; font-size: 13px;">
                @php $totalSelisihFinal = $realisasi->total_pembayaran - $realisasi->dp_amount; @endphp
                @if($totalSelisihFinal > 0)
                    <span class="summary-label">Kurang Bayar:</span>
                    <span style="font-weight: bold; color: #dc2626;">Rp {{ number_format($totalSelisihFinal, 0, ',', '.') }}</span>
                @elseif($totalSelisihFinal < 0)
                    <span class="summary-label">Lebih Bayar:</span>
                    <span style="font-weight: bold; color: #2563eb;">Rp {{ number_format(abs($totalSelisihFinal), 0, ',', '.') }}</span>
                @else
                    <span class="summary-label">Seimbang:</span>
                    <span style="font-weight: bold; color: #059669;">Rp 0</span>
                @endif
            </div>
            @endif
            </div>
        </div>

        <!-- Catatan Manual -->
        <div class="content-section" style="flex-shrink: 0; padding: 1mm;">
            <div class="section-title">Catatan</div>
            <div style="border: 1px solid #333; padding: 1mm; min-height: 15mm; background: #fff; font-size: 9px;">
                &nbsp;
            </div>
        </div>

        <!-- Keterangan -->
        @if($realisasi->keterangan)
        <div class="content-section" style="flex-shrink: 0; padding: 1mm;">
            <div class="section-title">Keterangan</div>
            <div style="border: 1px solid #ddd; padding: 1mm; min-height: 8mm; background: #f9f9f9; font-size: 9px;">
                {{ $realisasi->keterangan }}
            </div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Mengetahui</strong></div>
                <div>Kepala Bagian</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Dibuat Oleh</strong></div>
                <div>{{ $realisasi->pembuatPembayaran->name ?? 'Admin' }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div><strong>Diterima Oleh</strong></div>
                <div>Penerima</div>
            </div>
        </div>
    </div>

    <!-- Print Script -->
    <script>
        // Auto print when page loads with parameter
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('auto_print') === '1') {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        }
    </script>
</body>
</html>
