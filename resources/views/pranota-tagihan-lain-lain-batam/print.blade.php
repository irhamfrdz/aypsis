<!DOCTYPE html>
<html lang="id">
@php
    $paperSize = request('paper_size', 'Half-Folio');
    $paperSizes = [
        'Half-Folio' => ['size' => '215.9mm 165.1mm', 'width' => '195.9mm', 'font' => '8px'],
        'A4' => ['size' => 'A4', 'width' => '190mm', 'font' => '10px'],
        'Folio' => ['size' => '215.9mm 330.2mm', 'width' => '195.9mm', 'font' => '10px'],
    ];
    $paper = $paperSizes[$paperSize] ?? $paperSizes['Half-Folio'];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Permohonan Transfer - {{ $pranota->nomor_pranota }}</title>
    <style>
        @page { size: {{ $paper['size'] }}; margin: 8mm; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f4f6; color: #111; font-family: Arial, sans-serif; font-size: {{ $paper['font'] }}; line-height: 1.35; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; max-width: {{ $paper['width'] }}; margin: 16px auto; font-size: 12px; }
        .toolbar a, .toolbar button { display: inline-block; padding: 7px 10px; border: 1px solid #d1d5db; border-radius: 5px; background: white; color: #111; text-decoration: none; cursor: pointer; font: inherit; }
        .toolbar .active, .toolbar button { background: #4338ca; border-color: #4338ca; color: white; }
        .sheet { width: {{ $paper['width'] }}; min-height: 145mm; margin: 0 auto 20px; padding: 8mm 0; background: white; }
        h1 { margin: 0 0 8px; padding-bottom: 5px; border-bottom: 2px solid #222; text-align: center; font-size: 15px; letter-spacing: .4px; }
        .meta { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
        .meta td { padding: 2px 3px 2px 0; vertical-align: top; }
        .meta .label { width: 85px; font-weight: bold; }
        .meta .separator { width: 10px; }
        .items { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .items th, .items td { border: 1px solid #333; padding: 4px 3px; vertical-align: top; overflow-wrap: anywhere; }
        .items th { background: #f2f2f2; text-align: center; }
        .items thead { display: table-header-group; }
        .items tr { break-inside: avoid; page-break-inside: avoid; }
        .right { text-align: right; }
        .center { text-align: center; }
        .note { margin-top: 9px; padding: 6px; border: 1px dashed #777; white-space: pre-line; }
        .signatures { width: 100%; margin-top: 18px; border-collapse: collapse; break-inside: avoid; page-break-inside: avoid; }
        .signatures td { width: 33.33%; text-align: center; vertical-align: top; padding: 0 8px; }
        .signature-space { height: 36px; }
        .signature-name { display: inline-block; min-width: 110px; border-top: 1px solid #333; padding-top: 3px; }
        @media print {
            body { background: white; }
            .toolbar { display: none; }
            .sheet { width: 100%; min-height: 0; margin: 0; padding: 0; }
            .items th { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <span>Ukuran kertas:</span>
            @foreach (array_keys($paperSizes) as $size)
                <a href="{{ route('pranota-tagihan-lain-lain-batam.print', ['pranotaTagihanLainLainBatam' => $pranota, 'paper_size' => $size]) }}" class="{{ $paperSize === $size ? 'active' : '' }}">{{ $size }}</a>
            @endforeach
        </div>
        <button type="button" onclick="window.print()">Cetak Permohonan Transfer</button>
    </div>

    <main class="sheet">
        <h1>PERMOHONAN TRANSFER</h1>
        <table class="meta">
            <tr><td class="label">Nomor Pranota</td><td class="separator">:</td><td>{{ $pranota->nomor_pranota }}</td></tr>
            <tr><td class="label">Tanggal Pranota</td><td class="separator">:</td><td>{{ $pranota->tanggal_pranota?->format('d/m/Y') ?? '-' }}</td></tr>
            <tr><td class="label">Jenis Tagihan</td><td class="separator">:</td><td>Tagihan kendaraan dan tagihan lain lain Batam</td></tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 4%">No.</th>
                    <th style="width: 10%">Tanggal</th>
                    <th style="width: 14%">No. Tagihan</th>
                    <th style="width: 14%">Jenis / No. Polisi</th>
                    <th style="width: 17%">Vendor / Penerima</th>
                    <th style="width: 24%">Rincian</th>
                    <th style="width: 6%">PPh</th>
                    <th style="width: 11%">Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pranota->items as $item)
                    @php($tagihan = $item->tagihan)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td class="center">{{ $tagihan?->tanggal_service?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $tagihan?->nomor_tagihan ?? 'Tagihan dihapus' }}</td>
                        <td>
                            @if ($tagihan?->jenis_tagihan === 'kendaraan')
                                Kendaraan<br>{{ $tagihan->nomor_polisi ?: '-' }}
                            @elseif ($tagihan)
                                Lain lain
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $tagihan?->bengkel ?: '-' }}</td>
                        <td>{{ $tagihan?->rincian_service ?: '-' }}</td>
                        <td class="center">{{ $tagihan ? rtrim(rtrim(number_format((float) $tagihan->pph_rate, 1, ',', ''), '0'), ',').'%' : '-' }}</td>
                        <td class="right">{{ number_format((float) $item->total_biaya, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="right">TOTAL PERMOHONAN TRANSFER</th>
                    <th class="right">Rp {{ number_format((float) $pranota->total_biaya, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        @if ($pranota->keterangan)
            <div class="note"><strong>Keterangan:</strong> {{ $pranota->keterangan }}</div>
        @endif

        <table class="signatures">
            <tr>
                <td>Dibuat Oleh,<div class="signature-space"></div><span class="signature-name">{{ $pranota->createdBy?->karyawan?->nama_lengkap ?? $pranota->createdBy?->username ?? 'Operasional Batam' }}</span></td>
                <td>Diperiksa Oleh,<div class="signature-space"></div><span class="signature-name">Finance / Accounting</span></td>
                <td>Disetujui Oleh,<div class="signature-space"></div><span class="signature-name">Direksi / Management</span></td>
            </tr>
        </table>
    </main>
</body>
</html>
