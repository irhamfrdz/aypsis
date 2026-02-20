<!DOCTYPE html>
<html lang="id">
@php
    $paperSize = request('paper_size', 'Half Folio');
    $paperMap = [
        'Half Folio' => [
            'size'           => '215.9mm 165.1mm',
            'width'          => '215.9mm',
            'height'         => '165.1mm',
            'containerWidth' => '215.9mm',
            'fontSize'       => '10px',
            'headerH1'       => '16px',
            'tableFont'      => '9px',
        ],
        'Folio' => [
            'size'           => '215.9mm 330.2mm',
            'width'          => '215.9mm',
            'height'         => '330.2mm',
            'containerWidth' => '215.9mm',
            'fontSize'       => '11px',
            'headerH1'       => '18px',
            'tableFont'      => '10px',
        ],
        'A4' => [
            'size'           => 'A4',
            'width'          => '210mm',
            'height'         => '297mm',
            'containerWidth' => '210mm',
            'fontSize'       => '11px',
            'headerH1'       => '18px',
            'tableFont'      => '10px',
        ],
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half Folio'];

    // Resolve nama kapal & voyage dari array atau string
    $namaKapal = is_array($biayaKapal->nama_kapal)
        ? implode(', ', array_filter($biayaKapal->nama_kapal))
        : ($biayaKapal->nama_kapal ?? '-');

    $noVoyage = is_array($biayaKapal->no_voyage)
        ? implode(', ', array_filter($biayaKapal->no_voyage))
        : ($biayaKapal->no_voyage ?? '-');
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Biaya Perlengkapan - {{ $biayaKapal->nomor_invoice }}</title>
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
            font-family: 'Arial', sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.4;
            color: #000;
            background: white;
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
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header .subtitle {
            font-size: {{ $currentPaper['fontSize'] }};
            color: #444;
        }

        .document-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            margin-bottom: 18px;
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

        /* Detail box */
        .detail-box {
            border: 1px solid #333;
            border-radius: 4px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .detail-box .box-header {
            background-color: #f2f2f2;
            padding: 6px 10px;
            font-weight: bold;
            font-size: {{ $currentPaper['fontSize'] }};
            border-bottom: 1px solid #ccc;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: {{ $currentPaper['tableFont'] }};
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #bbb;
            padding: 6px 8px;
            vertical-align: top;
        }

        .detail-table th {
            background-color: #ececec;
            font-weight: bold;
            text-align: center;
        }

        .detail-table td.right { text-align: right; }
        .detail-table td.center { text-align: center; }

        /* Total summary */
        .summary-box {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .summary-table {
            width: 50%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 3px 0;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .summary-table .lbl {
            font-weight: bold;
            text-align: right;
            padding-right: 15px;
        }

        .summary-table .val {
            font-weight: bold;
            text-align: right;
            width: 130px;
        }

        .summary-table .total-row .lbl,
        .summary-table .total-row .val {
            border-top: 2px solid #000;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        /* Keterangan notes */
        .notes {
            margin-bottom: 15px;
            padding: 8px 10px;
            border: 1px dashed #999;
            font-size: {{ $currentPaper['tableFont'] }};
            background-color: #fafafa;
        }

        /* Signature area */
        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-box .title {
            font-weight: normal;
            margin-bottom: 55px;
            font-size: {{ $currentPaper['fontSize'] }};
        }

        .signature-box .line {
            border-top: 1px solid #000;
            margin: 0 auto;
            width: 80%;
            margin-bottom: 3px;
        }

        .signature-box .name {
            font-size: {{ $currentPaper['fontSize'] }};
        }

        /* Print controls (hidden when printing) */
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

        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Print Controls (non-print) -->
    <div class="print-controls no-print">
        <select id="paperSizeSelect" onchange="changePaperSize()" style="padding:5px;margin-bottom:5px;width:100%;">
            <option value="Half Folio" {{ $paperSize == 'Half Folio' ? 'selected' : '' }}>Setengah Folio</option>
            <option value="Folio"      {{ $paperSize == 'Folio'      ? 'selected' : '' }}>Folio</option>
            <option value="A4"         {{ $paperSize == 'A4'         ? 'selected' : '' }}>A4</option>
        </select>
        <button onclick="window.print()" style="width:100%;padding:5px;background:#007bff;color:white;border:none;border-radius:3px;cursor:pointer;">
            🖨️ Cetak
        </button>
    </div>

    <div class="container">

        <!-- Header -->
        <div class="header">
            <h1>PERMOHONAN BIAYA PERLENGKAPAN</h1>
            <div class="subtitle">{{ $biayaKapal->klasifikasiBiaya->nama ?? 'Biaya Perlengkapan' }}</div>
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
                    <span class="label">Nomor Invoice</span>
                    <span class="separator">:</span>
                    <span class="value"><strong>{{ $biayaKapal->nomor_invoice }}</strong></span>
                </div>
                @if($biayaKapal->nomor_referensi)
                <div class="info-row">
                    <span class="label">Nomor Referensi</span>
                    <span class="separator">:</span>
                    <span class="value">{{ $biayaKapal->nomor_referensi }}</span>
                </div>
                @endif
            </div>
            <div>
                <div class="info-row">
                    <span class="label">Nama Kapal</span>
                    <span class="separator">:</span>
                    <span class="value"><strong>{{ $namaKapal }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="label">Nomor Voyage</span>
                    <span class="separator">:</span>
                    <span class="value">{{ $noVoyage }}</span>
                </div>
                @if($biayaKapal->penerima)
                <div class="info-row">
                    <span class="label">Penerima</span>
                    <span class="separator">:</span>
                    <span class="value">{{ $biayaKapal->penerima }}</span>
                </div>
                @endif
                @if($biayaKapal->nama_vendor)
                <div class="info-row">
                    <span class="label">Nama Vendor</span>
                    <span class="separator">:</span>
                    <span class="value">{{ $biayaKapal->nama_vendor }}</span>
                </div>
                @endif
                @if($biayaKapal->nomor_rekening)
                <div class="info-row">
                    <span class="label">Nomor Rekening</span>
                    <span class="separator">:</span>
                    <span class="value">{{ $biayaKapal->nomor_rekening }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Detail Biaya Table -->
        <div class="detail-box">
            <div class="box-header">Detail Biaya Perlengkapan</div>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th style="width:5%;">No</th>
                        <th style="width:30%;">Nama Kapal</th>
                        <th style="width:25%;">Nomor Voyage</th>
                        <th style="width:40%;">Jumlah Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="center">1</td>
                        <td>{{ $namaKapal }}</td>
                        <td>{{ $noVoyage }}</td>
                        <td class="right">
                            <strong>Rp {{ number_format($biayaKapal->nominal ?? 0, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Summary / Total -->
        <div class="summary-box">
            <table class="summary-table">
                <tr class="total-row">
                    <td class="lbl">TOTAL BIAYA:</td>
                    <td class="val">Rp {{ number_format($biayaKapal->nominal ?? 0, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Keterangan -->
        @if($biayaKapal->keterangan)
        <div class="notes">
            <strong>Keterangan:</strong><br>
            {!! nl2br(e($biayaKapal->keterangan)) !!}
        </div>
        @endif

        <!-- Tanda Tangan -->
        <div class="signatures">
            <div class="signature-box">
                <div class="title">Dibuat Oleh</div>
                <div class="line"></div>
                <div class="name">&nbsp;</div>
            </div>
            <div class="signature-box">
                <div class="title">Diperiksa Oleh</div>
                <div class="line"></div>
                <div class="name">&nbsp;</div>
            </div>
            <div class="signature-box">
                <div class="title">Disetujui Oleh</div>
                <div class="line"></div>
                <div class="name">&nbsp;</div>
            </div>
        </div>

    </div><!-- /.container -->

    <script>
        function changePaperSize() {
            const paperSize = document.getElementById('paperSizeSelect').value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('paper_size', paperSize);
            window.location.href = currentUrl.toString();
        }
    </script>
</body>
</html>
