<!DOCTYPE html>
<html lang="id">
@php
    // Fixed paper size: Half-Folio
    $paperSize = 'Half-Folio';
    
    $currentPaper = [
        'size' => '215.9mm 165.1mm',
        'width' => '215.9mm',
        'height' => '165.1mm',
        'containerWidth' => '215.9mm',
        'fontSize' => '9px',
        'headerH1' => '14px',
        'tableFont' => '8px',
        'signatureBottom' => '3mm'
    ];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judulPranota ?? 'PRANOTA OB ANTAR GUDANG' }} - {{ $pranota->nomor_pranota }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: {{ $currentPaper['size'] }};
            margin: 0;
        }

        html {
            width: {{ $currentPaper['width'] }};
        }

        body {
            font-family: Arial, sans-serif;
            font-size: {{ $currentPaper['fontSize'] }};
            line-height: 1.3;
            color: #333;
            background: white;
            position: relative;
            width: {{ $currentPaper['width'] }};
            margin: 0;
            padding: 0;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            html, body { height: auto; margin: 0; padding: 0; }
            .print-page {
                height: 164mm !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }
            .container {
                min-height: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }
            .items-table thead { display: table-header-group; }
            .items-table tr, .footer-signatures, .keterangan-section {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }

        .print-page {
            width: {{ $currentPaper['containerWidth'] }};
            height: {{ $currentPaper['height'] }};
            margin: 0 auto;
            padding: 4mm 6mm;
            box-sizing: border-box;
            background: white;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            position: relative;
            box-sizing: border-box;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 3px;
            margin-bottom: 4px;
        }

        .header h1 {
            font-size: {{ $currentPaper['headerH1'] }};
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            margin-bottom: 4px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 8px;
        }

        .info-table .label {
            width: 110px;
            font-weight: bold;
        }

        .info-table .separator {
            width: 15px;
            text-align: center;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            table-layout: fixed;
        }

        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 1px 3px;
            font-size: {{ $currentPaper['tableFont'] }};
            line-height: 1.1;
            vertical-align: middle;
        }

        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .items-table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .keterangan-section {
            margin-top: 3px;
            padding: 3px;
            border: 1px dashed #ccc;
            background-color: #fdfdfd;
            font-size: 8px;
        }

        .footer-signatures {
            margin-top: 5px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding-top: 18px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 140px;
            margin: 0 auto 3px;
        }

        .signature-label {
            font-size: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Instruction Banner -->
    <div class="no-print" style="background: #fef3c7; padding: 10px; border: 1px solid #f59e0b; margin: 10px; font-size: 11px; border-radius: 5px;">
        <strong>Cetak Setengah Folio</strong><br>Ukuran kertas: <b>215,9 &times; 165,1 mm</b> (21,59 &times; 16,51 cm).<br>Gunakan ukuran kertas khusus tersebut, skala <b>100%</b>, margin <b>None / Tidak ada</b>, dan matikan header/footer browser.
    </div>

    <!-- Print Button -->
    <div class="no-print" style="text-align: right; margin: 0 10px 10px 0;">
        <button onclick="window.print()" style="padding: 6px 15px; background: #0f766e; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            🖨️ Cetak Pranota
        </button>
    </div>

    <div class="print-page" id="pranota-print-page">
    <div class="container" id="pranota-print-content">
        <!-- Header -->
        <div class="header">
            <h1>{{ $judulPranota ?? 'PRANOTA OB ANTAR GUDANG' }}</h1>
        </div>

        <!-- Info Section -->
        <table class="info-table">
            <tr>
                <td class="label">Nomor Pranota</td>
                <td class="separator">:</td>
                <td><strong>{{ $pranota->nomor_pranota }}</strong></td>
                <td class="label">Dibuat Oleh</td>
                <td class="separator">:</td>
                <td>{{ $pranota->creator->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pranota</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($pranota->tanggal_pranota)->format('d F Y') }}</td>
                <td class="label">Tanggal Cetak</td>
                <td class="separator">:</td>
                <td>{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 15%;">TANGGAL TAGIHAN</th>
                    <th style="width: 20%;">NO. KONTAINER</th>
                    <th style="width: 20%;">NAMA SUPIR</th>
                    <th style="width: 25%;">KE</th>
                    <th style="width: 15%;">BIAYA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pranota->items as $index => $item)
                    @php($detail = $item->snapshot ?? $item->tagihanOb)
                    @if($detail)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $item->snapshot ? \Carbon\Carbon::parse($detail->tanggal_ob ?? $detail->created_at)->format('d/m/Y H:i') : $detail->created_at->format('d/m/Y H:i') }}</td>
                            <td class="font-bold text-center">{{ $detail->nomor_kontainer }}</td>
                            <td>{{ $detail->nama_supir }}</td>
                            <td>{{ $detail->tujuan_gudang ?? '-' }}</td>
                            <td class="text-right font-bold">Rp {{ number_format($detail->biaya, 0, ',', '.') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td colspan="5" class="text-center italic text-gray-400">Data tagihan tidak ditemukan</td>
                        </tr>
                    @endif
                @endforeach
                
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="5" class="text-right">SUBTOTAL (NOMINAL)</td>
                    <td class="text-right">Rp {{ number_format($pranota->nominal, 0, ',', '.') }}</td>
                </tr>
                @if($pranota->adjustment != 0)
                    <tr style="font-weight: bold;">
                        <td colspan="5" class="text-right">
                            ADJUSTMENT 
                            @if($pranota->alasan_adjustment)
                                <span style="font-size: 7px; font-weight: normal; font-style: italic;">({{ $pranota->alasan_adjustment }})</span>
                            @endif
                        </td>
                        <td class="text-right {{ $pranota->adjustment > 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($pranota->adjustment, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr style="background-color: #f2f2f2; font-weight: bold; border-top: 1.5px solid #000;">
                        <td colspan="5" class="text-right">GRAND TOTAL</td>
                        <td class="text-right">Rp {{ number_format($pranota->grand_total, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if($pranota->keterangan)
        <div class="keterangan-section">
            <strong>Catatan Tambahan:</strong> {{ $pranota->keterangan }}
        </div>
        @endif

        <div class="footer-signatures">
            <table class="signature-table">
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
    </div>
    </div>
    <script>
        // Measure all rows, totals, notes, and signatures before scaling the full document.
        function fitPranotaToPage() {
            const page = document.getElementById('pranota-print-page');
            const content = document.getElementById('pranota-print-content');
            content.style.zoom = '1';
            const styles = window.getComputedStyle(page);
            const availableWidth = page.clientWidth - parseFloat(styles.paddingLeft) - parseFloat(styles.paddingRight);
            const availableHeight = page.clientHeight - parseFloat(styles.paddingTop) - parseFloat(styles.paddingBottom) - 8;
            content.style.width = `${availableWidth}px`;
            const contentHeight = Math.max(content.scrollHeight, content.getBoundingClientRect().height);
            let scale = Math.min(1, availableHeight / Math.max(contentHeight, 1));
            content.style.zoom = String(scale);
            // Table borders and text rounding can change height after applying zoom.
            for (let attempt = 0; attempt < 4; attempt++) {
                const printedHeight = content.getBoundingClientRect().height;
                if (printedHeight <= availableHeight) break;
                scale *= availableHeight / printedHeight;
                content.style.zoom = String(scale);
            }
        }

        window.addEventListener('load', fitPranotaToPage);
        window.addEventListener('beforeprint', fitPranotaToPage);
        window.addEventListener('afterprint', fitPranotaToPage);
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(fitPranotaToPage);
        }
    </script>
</body>
</html>
