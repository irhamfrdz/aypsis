<!DOCTYPE html>
<html lang="id">
@php
    $paperSize = request('paper_size', 'A4');
    $paperMap = [
        'Half-Folio' => [
            'size' => '215.9mm 165.1mm', 'width' => '215.9mm', 'height' => '165.1mm',
            'containerWidth' => '215.9mm', 'fontSize' => '9px', 'headerH1' => '14px', 'tableFont' => '8px',
        ],
        'Folio' => [
            'size' => '330.2mm 215.9mm',
            'width' => '330.2mm',
            'height' => '215.9mm',
            'containerWidth' => '330.2mm',
            'fontSize' => '13px', 'headerH1' => '20px', 'tableFont' => '11px',
        ],
        'A4' => [
            'size' => '297mm 210mm',
            'width' => '297mm',
            'height' => '210mm',
            'containerWidth' => '297mm',
            'fontSize' => '13px', 'headerH1' => '20px', 'tableFont' => '11px',
        ],
        'Half-A4' => [
            'size' => '210mm 148.5mm', 'width' => '210mm', 'height' => '148.5mm',
            'containerWidth' => '210mm', 'fontSize' => '9px', 'headerH1' => '14px', 'tableFont' => '8px',
        ],
    ];
    $currentPaper = $paperMap[$paperSize] ?? $paperMap['Half-A4'];

    // Gunakan relasi perlengkapanDetails jika ada, fallback ke array lama
    $details = $biayaKapal->perlengkapanDetails ?? collect();

    // Hitung total dari relasi detail, atau dari nominal di parent jika tidak ada
    $grandTotal = $details->sum('jumlah_biaya') ?: ($biayaKapal->nominal ?? 0);
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width={{ $currentPaper['width'] }}, initial-scale=1.0">
    <title>Biaya Perlengkapan - {{ $biayaKapal->nomor_invoice }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: {{ $currentPaper['size'] }};
            margin: 5mm;
        }

        html, body {
            width: {{ $currentPaper['width'] }};
            height: {{ $currentPaper['height'] }};
            font-family: 'Arial', sans-serif;
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
            min-height: calc({{ $currentPaper['height'] }} - 20mm);
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
        .info-section { margin-bottom: 8px; font-size: {{ $currentPaper['fontSize'] }}; }
        .info-table, .table { width: 100%; border-collapse: collapse; }
        .info-table { margin-bottom: 8px; }
        .info-table td { padding: 2px 4px; font-size: {{ $currentPaper['tableFont'] }}; vertical-align: top; font-weight: bold; }
        .table {
            table-layout: fixed;
            margin-bottom: 5mm;
        }
        .table th, .table td {
            border: 1px solid #333;
            padding: 1px 4px;
            text-align: left;
            vertical-align: middle;
        }
        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            font-size: {{ $currentPaper['tableFont'] }};
            text-align: center;
            border: 1.5px solid #333;
        }
        .table td { font-size: {{ $currentPaper['tableFont'] }}; font-weight: bold; }
        .table td.right { text-align: right; }
        .table td.center { text-align: center; }
        .table tr.total-row td { background-color: #f0f0f0 !important; font-weight: bold !important; border: 1.5px solid #333 !important; }
        .signature-table { width: 100%; border-collapse: collapse; text-align: center; margin-top: 10px; font-size: {{ $currentPaper['tableFont'] }}; }
        .signature-table td { width: 33.33%; padding: 2px; }
        .signature-space td { height: 55px; }
        .notes { margin: 2px 0; border: 1.5px solid #333; padding: 2px; min-height: 20px; font-size: 8px; }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    <!-- Print Controls -->
    <div class="no-print" style="position:fixed;top:10px;right:10px;background:#fff;padding:10px;border:1px solid #ccc;border-radius:5px;z-index:1000;display:flex;gap:8px;align-items:flex-end;">
        @include('components.paper-selector', ['selectedSize' => $paperSize])
        <button id="startPrint" style="background:#2563eb;color:#fff;padding:8px 12px;border-radius:4px;border:0;cursor:pointer;">Print</button>
    </div>

    <div class="container">

        <!-- Header -->
        <div class="header">
            <h1>PERMOHONAN TRANSFER</h1>
        </div>

        <!-- Document Info -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td style="width:15%">Tanggal</td><td style="width:35%">: {{ \Carbon\Carbon::parse($biayaKapal->tanggal)->format('d/m/Y') }}</td>
                    <td style="width:15%">Penerima</td><td>: {{ $biayaKapal->penerima ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nomor</td><td>: {{ $biayaKapal->nomor_invoice }}</td>
                    <td>Nama Vendor</td><td>: {{ $biayaKapal->nama_vendor ?? '-' }}</td>
                </tr>
                <tr>
                    <td>No. Ref</td><td>: {{ $biayaKapal->nomor_referensi ?? $biayaKapal->nomor_invoice }}</td>
                    <td>No. Rekening</td><td>: {{ ($biayaKapal->bank->name ?? '') ? $biayaKapal->bank->name.' - ' : '' }}{{ $biayaKapal->nomor_rekening ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div style="margin-bottom:8px"><strong style="font-size:{{ $currentPaper['tableFont'] }}">Detail Biaya Perlengkapan:</strong>
        <table class="table" style="margin-top:4px;margin-bottom:0">
            <thead>
                <tr>
                    <th style="width:6%;">No</th>
                    <th style="width:20%;">Nama Kapal</th>
                    <th style="width:16%;">Nomor Voyage</th>
                    <th style="width:11%;">Tanggal</th>
                    <th style="width:32%;">Keterangan</th>
                    <th style="width:15%;">Jumlah Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $i => $item)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $item->nama_kapal ?? '-' }}</td>
                    <td>{{ $item->no_voyage ?? '-' }}</td>
                    <td>{{ $item->tanggal?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($item->jumlah_biaya, 0, ',', '.') }}</td>
                </tr>
                @empty
                {{-- fallback: data lama disimpan di biaya_kapals --}}
                <tr>
                    <td class="center">1</td>
                    <td>
                        @php
                            $nk = is_array($biayaKapal->nama_kapal)
                                ? implode(', ', array_filter($biayaKapal->nama_kapal))
                                : ($biayaKapal->nama_kapal ?? '-');
                        @endphp
                        {{ $nk }}
                    </td>
                    <td>
                        @php
                            $nv = is_array($biayaKapal->no_voyage)
                                ? implode(', ', array_filter($biayaKapal->no_voyage))
                                : ($biayaKapal->no_voyage ?? '-');
                        @endphp
                        {{ $nv }}
                    </td>
                    <td>-</td>
                    <td>{{ $biayaKapal->keterangan ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($biayaKapal->nominal ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforelse
                <!-- Total row -->
                <tr class="total-row">
                    <td colspan="5" class="right">TOTAL:</td>
                    <td class="right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        </div>

        <!-- Keterangan global (opsional) -->
        @if($biayaKapal->keterangan)
        <div class="notes">
            <strong>Keterangan:</strong><br>
            {!! nl2br(e($biayaKapal->keterangan)) !!}
        </div>
        @endif

        <!-- Tanda Tangan -->
        <table class="signature-table">
            <tr><td><strong>Dibuat Oleh:</strong></td><td><strong>Diperiksa Oleh:</strong></td><td><strong>Disetujui Oleh:</strong></td></tr>
            <tr class="signature-space"><td></td><td></td><td></td></tr>
            <tr><td>( {{ $biayaKapal->creator->name ?? '__________' }} )</td><td>( __________ )</td><td>( {{ $biayaKapal->approver->name ?? '__________' }} )</td></tr>
        </table>
        </div>

    </div><!-- /.container -->

    <script>document.getElementById('startPrint')?.addEventListener('click', () => window.print());</script>
</body>
</html>
