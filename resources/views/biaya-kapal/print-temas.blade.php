<!DOCTYPE html>
<html lang="id">
@php
    // Calculate Vendor first to determine default paper size
    $vendorDisplay = $biayaKapal->nama_vendor ?? ($temasDetails->pluck('vendor')->filter()->unique()->values()->first() ?? 'TEMAS');
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
    <title>Invoice Biaya Temas - {{ $biayaKapal->nomor_invoice }}</title>
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
            word-wrap: break-word;
            word-break: break-all;
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
            $penerimaDisplay = $biayaKapal->penerima ?? ($temasDetails->pluck('penerima')->filter()->unique()->values()->first() ?? '-');
            $rekeningDisplay = $biayaKapal->nomor_rekening ?? ($temasDetails->pluck('nomor_rekening')->filter()->unique()->values()->first() ?? '-');
            $temasManifestGroups = $temasManifestGroups ?? collect();
            
            // Nomor seperti 01-1, 01-2, dan seterusnya merupakan satu BL induk 01.
            $normalizeNomorBl = static function ($nomorBl) {
                $nomorBl = trim((string) $nomorBl);
                return preg_replace('/-\d+$/', '', $nomorBl) ?: $nomorBl;
            };
            $temasByBl = $temasDetails->groupBy(function ($detail) use ($normalizeNomorBl) {
                return ($detail->kapal ?? '-').'|'.($detail->voyage ?? '-').'|'.$normalizeNomorBl($detail->nomor_bl);
            });

            // Calculate Totals
            $totalSubtotal = 0;
            $totalPPH = 0; // Total PPH yang benar-benar memotong (untuk kalkulasi)
            $totalPPN = 0; // Total PPN yang benar-benar menambah (untuk kalkulasi)
            $displayPPH = 0; // Untuk tampilan baris di tabel
            $displayPPN = 0; // Untuk tampilan baris di tabel
            $totalAdjustment = 0;
            $totalMaterai = 0;
            $totalAdmin = 0;
            $totalGrandTotal = 0;
            
            foreach($temasDetails as $detail) {
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
                $totalAdmin += $detail->biaya_admin;
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
                    <th style="width: 15%;">Tanggal Ref.</th>
                    <th style="width: 20%;">Referensi</th>
                    <th style="width: 15%;">Nomor Voyage</th>
                    <th style="width: 25%;">Nomor BL</th>
                    <th style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $perSection = $temasDetails->groupBy(function($item) use ($normalizeNomorBl) {
                        return ($item->kapal ?? '-') . '|' . ($item->voyage ?? '-') . '|' . ($item->nomor_referensi ?? '-') . '|' . $normalizeNomorBl($item->nomor_bl);
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
                    $firstDetail = $details->first();
                    $blInduk = $normalizeNomorBl($firstDetail->nomor_bl) ?: '-';
                    $manifestInfo = $temasManifestGroups->get(($firstDetail->voyage ?? '').'|'.$blInduk);
                    $blVariants = collect($manifestInfo['variants'] ?? [])
                        ->whenEmpty(fn ($items) => $items->push($firstDetail->nomor_bl))
                        ->filter()->unique()->values();
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
                    <td class="text-center">
                        {{ $blInduk }}
                        @if($blVariants->isNotEmpty() && !($blVariants->count() === 1 && $blVariants->first() === $blInduk))
                            <br><small>({{ $blVariants->implode(', ') }})</small>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($sectionGrandTotal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data detail.</td>
                </tr>
                @endforelse
                <tr class="total-row">
                    <td colspan="5" class="text-right">TOTAL PEMBAYARAN</td>
                    <td class="text-right">Rp {{ number_format($totalGrandTotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        @php
            $temasStages = \App\Models\BiayaKapalTemasStage::where('biaya_kapal_id', $biayaKapal->id)->get();
            $dpDiperhitungkan = $temasStages->sum('dp_diperhitungkan');
        @endphp
        @if($temasStages->isNotEmpty())
            <div class="keterangan-box">
                @foreach($temasStages as $stage)
                    <div>
                        <strong>{{ $stage->kapal }} / {{ $stage->voyage }}:</strong>
                        @if($stage->payment_mode === 'dp')
                            DP / Uang muka Rp {{ number_format($stage->nominal_dibayar, 0, ',', '.') }}. Tagihan akhir belum ditentukan.
                            <a class="no-print" href="{{ route('biaya-kapal.print-temas-dp', $stage->id) }}" target="_blank" style="margin-left: 8px; color: #1d4ed8; text-decoration: underline;">Cetak bukti DP</a>
                        @elseif($stage->payment_mode === 'pelunasan_dp')
                            Tagihan akhir Rp {{ number_format($stage->nilai_tagihan, 0, ',', '.') }}
                            - DP Rp {{ number_format($stage->dp_diperhitungkan, 0, ',', '.') }}
                            = Pelunasan Rp {{ number_format($stage->nominal_dibayar, 0, ',', '.') }}.
                        @else
                            Bayar langsung Rp {{ number_format($stage->nominal_dibayar, 0, ',', '.') }}.
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- TABLE 2: DETAIL BIAYA PER BL -->
        <div class="section-header">Detail Biaya per Nomor BL:</div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Nomor BL</th>
                    <th style="width: 25%;">Kontainer</th>
                    <th style="width: 27%;">Jenis Biaya</th>
                    <th style="width: 10%;">Jumlah</th>
                    <th style="width: 18%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp

                @foreach($temasByBl as $blDetails)
                    @php
                        $firstBlDetail = $blDetails->first();
                        $blInduk = $normalizeNomorBl($firstBlDetail->nomor_bl) ?: '-';
                        $manifestInfo = $temasManifestGroups->get(($firstBlDetail->voyage ?? '').'|'.$blInduk);
                        $blVariants = collect($manifestInfo['variants'] ?? [])
                            ->whenEmpty(fn ($items) => $items->push($firstBlDetail->nomor_bl))
                            ->filter()->unique()->values();
                        $containers = collect($manifestInfo['containers'] ?? []);
                        if ($containers->isEmpty()) {
                            $containers = $blDetails->pluck('nomor_kontainer')
                                ->flatMap(fn ($numbers) => array_map('trim', explode(',', (string) $numbers)))
                                ->filter()->unique()->values();
                        }
                        $rowspan = $blDetails->count();
                    @endphp
                    @foreach($blDetails as $detail)
                        @php
                            $status = '';
                            if ($detail->is_muat && $detail->is_bongkar) $status = ' (MUAT/BONGKAR)';
                            elseif ($detail->is_muat) $status = ' (MUAT)';
                            elseif ($detail->is_bongkar) $status = ' (BONGKAR)';
                        @endphp
                        <tr>
                            @if($loop->first)
                                <td class="text-center" rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                                <td class="text-center" rowspan="{{ $rowspan }}">
                                    {{ $blInduk }}
                                    @if($blVariants->isNotEmpty() && !($blVariants->count() === 1 && $blVariants->first() === $blInduk))
                                        <br><small>({{ $blVariants->implode(', ') }})</small>
                                    @endif
                                </td>
                                <td rowspan="{{ $rowspan }}">{!! $containers->isNotEmpty() ? $containers->map(fn ($container) => e($container))->implode('<br>') : '-' !!}</td>
                            @endif
                            <td>{{ strtoupper(($detail->jenis_biaya ?? 'BIAYA TEMAS').$status) }}</td>
                            <td class="text-center">{{ rtrim(rtrim(number_format($detail->kuantitas, 2, ',', '.'), '0'), ',') }}</td>
                            <td class="text-right">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endforeach
                
                @if($displayPPH > 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td colspan="3">PPH (2%) {{ $totalPPH <= 0 ? '(Reimburse)' : '' }}</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($displayPPH, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($totalPPN > 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td colspan="3">PPN (11%)</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($totalPPN, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($totalMaterai > 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td colspan="3">BIAYA MATERAI</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($totalMaterai, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($totalAdmin > 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td colspan="3">BIAYA ADMIN</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($totalAdmin, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($totalAdjustment != 0)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td colspan="3">ADJUSTMENT</td>
                    <td class="text-center">1</td>
                    <td class="text-right">Rp {{ number_format($totalAdjustment, 0, ',', '.') }}</td>
                </tr>
                @endif
                
                @if($dpDiperhitungkan > 0)
                <tr>
                    <td colspan="5" class="text-right">DIKURANGI DP YANG SUDAH DIBAYAR</td>
                    <td class="text-right">- Rp {{ number_format($dpDiperhitungkan, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="5" class="text-right">TOTAL</td>
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
