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
            'fontSize' => '9px',
            'headerH1' => '14px',
            'tableFont' => '8px',
        ],
        'Folio' => [
            'size' => '215.9mm 330.2mm',
            'width' => '215.9mm',
            'height' => '330.2mm',
            'containerWidth' => '215.9mm',
            'fontSize' => '11px',
            'headerH1' => '18px',
            'tableFont' => '9px',
        ],
        'A4' => [
            'size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
            'containerWidth' => '210mm',
            'fontSize' => '11px',
            'headerH1' => '18px',
            'tableFont' => '9px',
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
            line-height: 1.4;
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
            margin-bottom: 8px;
            border-bottom: 2px double #000;
            padding-bottom: 6px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header p {
            font-size: {{ $currentPaper['fontSize'] }};
            color: #555;
            margin: 1px 0;
        }

        .document-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 6px 8px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 2px;
        }

        .document-info .left,
        .document-info .right {
            flex: 1;
        }

        .document-info .label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
            margin-bottom: 1px;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .document-info .value {
            display: inline-block;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .section-title {
            font-size: {{ $currentPaper['fontSize'] }};
            font-weight: bold;
            margin: 8px 0 5px;
            padding: 4px 8px;
            background-color: #e9ecef;
            border-left: 3px solid #0066cc;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .info-table td {
            padding: 3px 6px;
            font-size: {{ $currentPaper['fontSize'] }};
            vertical-align: top;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-table td:first-child {
            width: 35%;
            font-weight: bold;
            color: #555;
        }

        .info-table td:nth-child(2) {
            width: 5%;
            text-align: center;
        }

        .container-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .container-table thead {
            background-color: #0066cc;
            color: white;
        }

        .container-table th,
        .container-table td {
            border: 1px solid #333;
            padding: 4px;
            text-align: left;
            font-size: {{ $currentPaper['tableFont'] }};
        }

        .container-table th {
            font-weight: bold;
            text-align: center;
        }

        .container-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .container-table tbody tr:hover {
            background-color: #e9ecef;
        }

        .summary-box {
            margin-top: 8px;
            padding: 0;
            background-color: transparent;
            border: none;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: {{ $currentPaper['fontSize'] }};
            padding: 2px 0;
        }

        .summary-row.total {
            border-top: 2px solid #333;
            padding-top: 4px;
            margin-top: 4px;
            font-weight: bold;
            font-size: calc({{ $currentPaper['fontSize'] }} + 2px);
        }

        .summary-row .label {
            font-weight: 600;
        }

        .summary-row .value {
            font-weight: bold;
            text-align: right;
            margin-left: 20px;
        }

        .footer {
            margin-top: 12px;
            clear: both;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box .title {
            font-weight: bold;
            margin-bottom: 30px;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .signature-box .name {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 2px;
            display: inline-block;
            min-width: 120px;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .notes {
            margin-top: 8px;
            padding: 6px;
            background-color: #fff3cd;
            border-left: 3px solid #ffc107;
            font-size: {{ $currentPaper['tableFont'] }};
        }

        .notes strong {
            display: block;
            margin-bottom: 3px;
        }

        @media print {
            .no-print {
                display: none;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .print-controls button,
        .print-controls select {
            margin: 5px 0;
            padding: 8px 15px;
            font-size: 12px;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .print-controls button {
            background-color: #0066cc;
            color: white;
            border: none;
        }

        .print-controls button:hover {
            background-color: #0052a3;
        }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <select id="paperSizeSelect" onchange="changePaperSize()">
            <option value="Half Folio" {{ $paperSize == 'Half Folio' ? 'selected' : '' }}>Setengah Folio</option>
            <option value="Folio" {{ $paperSize == 'Folio' ? 'selected' : '' }}>Folio</option>
            <option value="A4" {{ $paperSize == 'A4' ? 'selected' : '' }}>A4</option>
        </select>
        <button onclick="window.print()">🖨️ Cetak</button>
        <button onclick="window.close()">❌ Tutup</button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>BIAYA DOKUMEN</h1>
            <p>{{ config('app.name', 'Aypsis') }}</p>
            <p style="font-size: 10px;">Dokumen Pengeluaran Biaya Dokumen Kapal</p>
        </div>

        <!-- Document Info -->
        <div class="document-info">
            <div class="left">
                <div>
                    <span class="label">No. Invoice</span>
                    <span class="value">: <strong>{{ $biayaKapal->nomor_invoice }}</strong></span>
                </div>
                <div>
                    <span class="label">Tanggal</span>
                    <span class="value">: {{ \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d F Y') }}</span>
                </div>
                @if($biayaKapal->nomor_referensi)
                <div>
                    <span class="label">No. Referensi</span>
                    <span class="value">: {{ $biayaKapal->nomor_referensi }}</span>
                </div>
                @endif
            </div>
            <div class="right">
                <div>
                    <span class="label">Jenis Biaya</span>
                    <span class="value">: {{ $biayaKapal->jenis_biaya_label }}</span>
                </div>
                @if($biayaKapal->vendor_id)
                <div>
                    <span class="label">Vendor</span>
                    @php
                        $vendorData = \DB::table('pricelist_biaya_dokumen')
                            ->where('id', $biayaKapal->vendor_id)
                            ->first();
                    @endphp
                    <span class="value">: {{ $vendorData->nama_vendor ?? '-' }}</span>
                </div>
                @endif
                @if($biayaKapal->penerima)
                <div>
                    <span class="label">Penerima</span>
                    <span class="value">: {{ $biayaKapal->penerima }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Detail Informasi -->
        <div class="section-title">INFORMASI DETAIL</div>
        <table class="info-table">
            <tr>
                <td>Nama Kapal</td>
                <td>:</td>
                <td>
                    @php
                        $namaKapals = is_array($biayaKapal->nama_kapal) ? $biayaKapal->nama_kapal : [$biayaKapal->nama_kapal];
                    @endphp
                    {{ implode(', ', $namaKapals) }}
                </td>
            </tr>
            <tr>
                <td>Nomor Voyage</td>
                <td>:</td>
                <td>
                    @php
                        $noVoyages = is_array($biayaKapal->no_voyage) ? $biayaKapal->no_voyage : [$biayaKapal->no_voyage];
                    @endphp
                    {{ implode(', ', $noVoyages) }}
                </td>
            </tr>
            @if($biayaKapal->keterangan)
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td>{{ $biayaKapal->keterangan }}</td>
            </tr>
            @endif
        </table>

        <!-- Daftar Kontainer -->
        @php
            $noBls = is_array($biayaKapal->no_bl) ? $biayaKapal->no_bl : ($biayaKapal->no_bl ? [$biayaKapal->no_bl] : []);
        @endphp
        @if(count($noBls) > 0)
        <div class="section-title">DAFTAR KONTAINER</div>
        <table class="container-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 42%;">Nomor Kontainer</th>
                    <th style="width: 25%;">Nomor Voyage</th>
                    <th style="width: 25%;">Nama Kapal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($noBls as $index => $blId)
                    @php
                        // Ambil data BL dari database untuk mendapatkan kontainer, seal, voyage, dan kapal
                        $bl = \DB::table('bls')
                            ->where('id', $blId)
                            ->first();
                    @endphp
                    @if($bl)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $bl->nomor_kontainer }}</strong>
                            @if($bl->no_seal)
                            <br><small style="color: #666;">Seal: {{ $bl->no_seal }}</small>
                            @endif
                        </td>
                        <td>{{ $bl->no_voyage }}</td>
                        <td>{{ $bl->nama_kapal }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold; background-color: #e9ecef;">
                        Total Kontainer: {{ count($noBls) }} unit
                    </td>
                </tr>
            </tfoot>
        </table>
        @endif

        <!-- Summary Box -->
        <div class="summary-box">
            @php
                $jumlahKontainer = count($noBls);
                $tarifPerKontainer = $jumlahKontainer > 0 ? ($biayaKapal->nominal / $jumlahKontainer) : $biayaKapal->nominal;
            @endphp
            @if($jumlahKontainer > 0)
            <div class="summary-row">
                <span class="label">Tarif per Kontainer:</span>
                <span class="value">Rp {{ number_format($tarifPerKontainer, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">Jumlah Kontainer:</span>
                <span class="value">{{ $jumlahKontainer }} unit</span>
            </div>
            @endif
            <div class="summary-row">
                <span class="label">Subtotal:</span>
                <span class="value">Rp {{ number_format($biayaKapal->nominal ?? 0, 0, ',', '.') }}</span>
            </div>
            @if($biayaKapal->pph_dokumen)
            <div class="summary-row">
                <span class="label">PPh (2%):</span>
                <span class="value">(Rp {{ number_format($biayaKapal->pph_dokumen, 0, ',', '.') }})</span>
            </div>
            @endif
            <div class="summary-row total">
                <span class="label">TOTAL BIAYA:</span>
                <span class="value">Rp {{ number_format($biayaKapal->grand_total_dokumen ?? $biayaKapal->nominal, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Footer with Signatures -->
        <div class="footer">
            <div class="signatures">
                <div class="signature-box" style="width: 30%;">
                    <div class="title">Dibuat Oleh,</div>
                    <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
                </div>
                <div class="signature-box" style="width: 30%;">
                    <div class="title">Mengetahui,</div>
                    <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
                </div>
                <div class="signature-box" style="width: 30%;">
                    <div class="title">Menyetujui,</div>
                    <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
                </div>
            </div>

            @if($biayaKapal->keterangan)
            <div class="notes">
                <strong>Catatan:</strong>
                {{ $biayaKapal->keterangan }}
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
