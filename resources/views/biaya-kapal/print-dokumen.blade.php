<!DOCTYPE html>
<html lang="id">
@php
    $paperSize = request('paper_size', 'Half Folio');
    $paperMap = [
        'Half Folio' => [
            'size' => '215.9mm 165.1mm',
            'width' => '215.9mm',
            'height' => '165.1mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '10px',
            'headerH1' => '16px',
            'tableFont' => '9px',
        ],
        'Folio' => [
            'size' => '215.9mm 330.2mm',
            'width' => '215.9mm',
            'height' => '330.2mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '11px',
            'headerH1' => '18px',
            'tableFont' => '10px',
        ],
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '210mm',
            'fontSize' => '11px',
            'headerH1' => '18px',
            'tableFont' => '10px',
        ],
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half Folio'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Biaya Dokumen - {{ $biayaKapal->nomor_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: {{ $currentPaper['size'] }} portrait;
            margin: 10mm;
        }

        html, body {
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
            font-family: 'Arial', sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.3;
            color: #000;
            background: white;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: calc({{ $currentPaper['containerWidth'] }} - 20mm);
            padding: 0 10mm;
            margin: 0 auto;
            box-sizing: border-box;
            min-height: calc({{ $currentPaper['height'] }} - 20mm);
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .document-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            margin-bottom: 15px;
            padding: 0;
            border: none;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .document-info .info-row {
            display: flex;
            padding: 2px 0;
        }

        .document-info .label {
            font-weight: normal;
            width: 140px;
            flex-shrink: 0;
        }

        .document-info .separator {
            margin: 0 8px;
            flex-shrink: 0;
        }

        .document-info .value {
            flex: 1;
            font-weight: normal;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: {{ $currentPaper['tableFont'] }};
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #333;
            padding: 5px;
            vertical-align: top;
        }

        .detail-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .detail-table td.center { text-align: center; }
        .detail-table td.right { text-align: right; }

        .summary-box {
            margin-top: 10px;
            padding: 0;
            background-color: transparent;
            border: none;
            width: 100%;
            display: flex;
            justify-content: flex-end;
        }

        .summary-table {
            width: 50%;
            border-collapse: collapse;
        }
        
        .summary-table td {
            padding: 3px 0;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .summary-table .label {
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
        }

        .summary-table .value {
            font-weight: bold;
            text-align: right;
            width: 130px;
        }

        .summary-table .total-row .label,
        .summary-table .total-row .value {
            border-top: 2px solid #000;
            padding-top: 5px;
            padding-bottom: 5px;
            font-size: calc({{ $currentPaper['fontSize'] }} + 1px);
        }

        .footer {
            margin-top: 30px;
            clear: both;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 40px;
            margin-bottom: 25px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box .title {
            font-weight: normal;
            margin-bottom: 85px;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .signature-box .line {
            border-top: 1px solid #000;
            margin: 0 auto;
            width: 80%;
            margin-bottom: 3px;
        }

        .signature-box .name {
            font-weight: normal;
            padding-top: 0;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .notes {
            margin-top: 10px;
            padding: 8px;
            border: 1px dashed #999;
            font-size: {{ $currentPaper['tableFont'] }};
            background-color: #fdfdfd;
        }

        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            
            /* Ensure page breaks don't cut rows */
            tr { page-break-inside: avoid; }
        }

        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .tt-chip {
            display: inline-block;
            background: #eee;
            padding: 2px 5px;
            border-radius: 3px;
            margin: 2px;
            font-size: {{ $currentPaper['tableFont'] }};
            border: 1px solid #ddd;
            white-space: nowrap;
        }

        .rincian-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <select id="paperSizeSelect" onchange="changePaperSize()" style="padding: 5px; margin-bottom: 5px; width: 100%;">
            <option value="Half Folio" {{ $paperSize == 'Half Folio' ? 'selected' : '' }}>Setengah Folio</option>
            <option value="Folio" {{ $paperSize == 'Folio' ? 'selected' : '' }}>Folio</option>
            <option value="A4" {{ $paperSize == 'A4' ? 'selected' : '' }}>A4</option>
        </select>
        <button onclick="window.print()" style="width: 100%; padding: 5px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;">🖨️ Cetak</button>
        <button onclick="window.close()" style="width: 100%; padding: 5px; margin-top: 5px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer;">❌ Tutup</button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PERMOHONAN TRANSFER</h1>
        </div>

        <!-- Document Info -->
        <div class="document-info">
            <div>
                <div class="info-row">
                    <span class="label">Tanggal</span>
                    <span class="separator">:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Nomor</span>
                    <span class="separator">:</span>
                    <span class="value"><strong>{{ $biayaKapal->nomor_invoice }}</strong></span>
                </div>
                @if($biayaKapal->nomor_referensi)
                <div class="info-row">
                    <span class="label">Nomor Referensi</span>
                    <span class="separator">:</span>
                    <span class="value">{{ $biayaKapal->nomor_referensi }}</span>
                </div>
                @else
                <div class="info-row">
                    <span class="label">Nomor Referensi</span>
                    <span class="separator">:</span>
                    <span class="value">-</span>
                </div>
                @endif
            </div>
            <div>
                @php
                    $vendorName = $biayaKapal->nama_vendor;
                    if (!$vendorName && $biayaKapal->vendor_id) {
                        $vendorData = \DB::table('pricelist_biaya_dokumen')
                            ->where('id', $biayaKapal->vendor_id)
                            ->first();
                        $vendorName = $vendorData->nama_vendor ?? null;
                    }
                @endphp
                @if($vendorName)
                <div class="info-row">
                    <span class="label">Vendor</span>
                    <span class="separator">:</span>
                    <span class="value">{{ $vendorName }}</span>
                </div>
                @endif
                @if($biayaKapal->penerima)
                <div class="info-row">
                    <span class="label">Penerima</span>
                    <span class="separator">:</span>
                    <span class="value">{{ $biayaKapal->penerima }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="label">Jenis Biaya</span>
                    <span class="separator">:</span>
                    <span class="value">{{ $biayaKapal->jenis_biaya_label }}</span>
                </div>
            </div>
        </div>

        @php
            $dokumenDetails = $biayaKapal->dokumens ?? collect();
            $totalKontainerCount = 0;
        @endphp

        <!-- Detail Table -->
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Kapal / Voyage</th>
                    <th style="width: 30%;">Daftar BL & Kontainer</th>
                    <th style="width: 25%;">Rincian Biaya</th>
                    <th style="width: 15%;">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $grandTotalSubtotal = 0; 
                    $grandTotalPph = 0;
                    $grandTotalFinal = 0;
                @endphp
                
                @if($dokumenDetails->isNotEmpty())
                    @foreach($dokumenDetails as $index => $detail)
                        @php
                            $detailBlNumbers = $detail->nomorBlArray;
                            $detailContainers = collect();
                            if (!empty($detailBlNumbers)) {
                                $detailContainers = \DB::table('manifests')
                                    ->whereIn('nomor_bl', $detailBlNumbers)
                                    ->where('no_voyage', $detail->voyage)
                                    ->get();
                                if ($detailContainers->isEmpty()) {
                                    $detailContainers = \DB::table('bls')
                                        ->whereIn('nomor_bl', $detailBlNumbers)
                                        ->where('no_voyage', $detail->voyage)
                                        ->get();
                                }
                            }
                            $uniqueDetailContainers = $detailContainers->unique('nomor_kontainer')->values();
                            $totalKontainerCount += $uniqueDetailContainers->count();

                            $subtotalDetail = (float) $detail->nominal;
                            $pphDetail = (float) $detail->pph;
                            $totalDetail = (float) $detail->total_biaya;
                            if ($totalDetail == 0 && $subtotalDetail > 0) {
                                $totalDetail = $subtotalDetail - $pphDetail;
                            }

                            $grandTotalSubtotal += $subtotalDetail;
                            $grandTotalPph += $pphDetail;
                            $grandTotalFinal += $totalDetail;

                            $detailVendorName = null;
                            if ($detail->vendor_id && $detail->vendor_id != $biayaKapal->vendor_id) {
                                $dVendor = \DB::table('pricelist_biaya_dokumen')->where('id', $detail->vendor_id)->first();
                                $detailVendorName = $dVendor->nama_vendor ?? null;
                            }
                        @endphp
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $detail->kapal }}</strong><br>
                                <span style="color: #555;">Voy: {{ $detail->voyage }}</span>
                                @if($detailVendorName)
                                    <br><span style="color: #6a0dad; font-size: 0.9em;">Vendor: {{ $detailVendorName }}</span>
                                @endif
                            </td>
                            <td>
                                @if(!empty($detail->nomor_bl))
                                    <div style="margin-bottom: 4px;">
                                        <strong>BL:</strong> <span style="color: #0d9488;">{{ $detail->nomor_bl }}</span>
                                    </div>
                                @endif
                                @if($uniqueDetailContainers->isNotEmpty())
                                    <div style="margin-top: 2px;">
                                        @foreach($uniqueDetailContainers as $k)
                                            <div style="margin-bottom: 2px;">
                                                <span class="tt-chip">{{ $k->nomor_kontainer }}</span>
                                                @if(!empty($k->size))
                                                    <span style="color: #666; font-size: 0.85em;">{{ $k->size }}'</span>
                                                @endif
                                                @if(!empty($k->no_seal))
                                                    <span style="color: #888; font-size: 0.8em;">(Seal: {{ $k->no_seal }})</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        <div style="font-size: 0.85em; color: #555; margin-top: 3px;">
                                            Total: {{ $uniqueDetailContainers->count() }} kontainer
                                        </div>
                                    </div>
                                @elseif(empty($detail->nomor_bl))
                                    <span style="color: #999; font-style: italic;">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="rincian-item">
                                    <span>Subtotal:</span>
                                    <span>{{ number_format($subtotalDetail, 0, ',', '.') }}</span>
                                </div>
                                @if($pphDetail > 0)
                                <div class="rincian-item">
                                    <span>PPh (2%):</span>
                                    <span>({{ number_format($pphDetail, 0, ',', '.') }})</span>
                                </div>
                                @endif
                            </td>
                            <td class="right">
                                <div style="font-weight: bold;">Rp {{ number_format($totalDetail, 0, ',', '.') }}</div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    {{-- Fallback untuk data transaksi lama tanpa rincian dokumens --}}
                    @php
                        $namaKapals = is_array($biayaKapal->nama_kapal) ? $biayaKapal->nama_kapal : [$biayaKapal->nama_kapal];
                        $noVoyages = is_array($biayaKapal->no_voyage) ? $biayaKapal->no_voyage : [$biayaKapal->no_voyage];
                        
                        $fallbackContainers = collect();
                        $noBls = is_array($biayaKapal->no_bl) ? $biayaKapal->no_bl : ($biayaKapal->no_bl ? [$biayaKapal->no_bl] : []);
                        if (count($noBls) > 0) {
                            $fallbackContainers = \DB::table('bls')->whereIn('id', $noBls)->get();
                        }
                        $uniqueFallbackContainers = $fallbackContainers->unique('id')->values();
                        $totalKontainerCount = $uniqueFallbackContainers->count();

                        $subtotalDetail = (float) ($biayaKapal->nominal ?? 0);
                        $pphDetail = (float) ($biayaKapal->pph_dokumen ?? 0);
                        $totalDetail = $biayaKapal->grand_total_dokumen !== null
                            ? (float) $biayaKapal->grand_total_dokumen
                            : $subtotalDetail - $pphDetail;

                        $grandTotalSubtotal = $subtotalDetail;
                        $grandTotalPph = $pphDetail;
                        $grandTotalFinal = $totalDetail;
                    @endphp
                    <tr>
                        <td class="center">1</td>
                        <td>
                            <strong>{{ implode(', ', array_filter($namaKapals)) ?: '-' }}</strong><br>
                            <span style="color: #555;">Voy: {{ implode(', ', array_filter($noVoyages)) ?: '-' }}</span>
                        </td>
                        <td>
                            @if($uniqueFallbackContainers->isNotEmpty())
                                @foreach($uniqueFallbackContainers as $k)
                                    <div style="margin-bottom: 2px;">
                                        <span class="tt-chip">{{ $k->nomor_kontainer }}</span>
                                        @if(!empty($k->nomor_bl))
                                            <span style="color: #666; font-size: 0.85em;">(BL: {{ $k->nomor_bl }})</span>
                                        @endif
                                    </div>
                                @endforeach
                                <div style="font-size: 0.85em; color: #555; margin-top: 3px;">
                                    Total: {{ $uniqueFallbackContainers->count() }} kontainer
                                </div>
                            @else
                                <span style="color: #999; font-style: italic;">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="rincian-item">
                                <span>Subtotal:</span>
                                <span>{{ number_format($subtotalDetail, 0, ',', '.') }}</span>
                            </div>
                            @if($pphDetail > 0)
                            <div class="rincian-item">
                                <span>PPh (2%):</span>
                                <span>({{ number_format($pphDetail, 0, ',', '.') }})</span>
                            </div>
                            @endif
                        </td>
                        <td class="right">
                            <div style="font-weight: bold;">Rp {{ number_format($totalDetail, 0, ',', '.') }}</div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-box">
            <table class="summary-table">
                @if($totalKontainerCount > 0)
                <tr>
                    <td class="label">Total Kontainer:</td>
                    <td class="value">{{ $totalKontainerCount }} unit</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">Rp {{ number_format($grandTotalSubtotal, 0, ',', '.') }}</td>
                </tr>
                @if($grandTotalPph > 0)
                <tr>
                    <td class="label">PPh (2%):</td>
                    <td class="value">(Rp {{ number_format($grandTotalPph, 0, ',', '.') }})</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label">TOTAL BIAYA:</td>
                    <td class="value">Rp {{ number_format($grandTotalFinal, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer with Signatures -->
        <div class="footer">
            <div class="signatures">
                <div class="signature-box">
                    <div class="title">Dibuat Oleh</div>
                    <div class="line"></div>
                    <div class="name"></div>
                </div>
                <div class="signature-box">
                    <div class="title">Diperiksa Oleh</div>
                    <div class="line"></div>
                    <div class="name"></div>
                </div>
                <div class="signature-box">
                    <div class="title">Disetujui Oleh</div>
                    <div class="line"></div>
                    <div class="name"></div>
                </div>
            </div>

            @php
                $keterangan = $biayaKapal->keterangan ?? '';
                if (stripos($keterangan, 'Detail Biaya Agen:') !== false) {
                    $keterangan = explode('Detail Biaya Agen:', $keterangan)[0];
                }
                if (stripos($keterangan, 'Detail Barang Buruh:') !== false) {
                    $keterangan = explode('Detail Barang Buruh:', $keterangan)[0];
                }
                if (stripos($keterangan, 'Detail Biaya TKBM:') !== false) {
                    $keterangan = explode('Detail Biaya TKBM:', $keterangan)[0];
                }
            @endphp
            @if(trim($keterangan))
            <div class="notes">
                <strong>Catatan:</strong><br>
                {!! nl2br(e(trim($keterangan))) !!}
            </div>
            @endif
        </div>
    </div>

    <script>
        function changePaperSize() {
            const paperSize = document.getElementById('paperSizeSelect').value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('paper_size', paperSize);
            window.location.href = currentUrl.toString();
        }

        // Auto print on load if requested
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('auto_print') === '1') {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
