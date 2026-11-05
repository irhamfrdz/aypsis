<!DOCTYPE html>
<html lang="id">
@php
    $paperSize = request('paper_size', 'A4');
    $paperSizes = ['A4' => 'A4 landscape', 'A3' => 'A3 landscape', 'Folio' => '330.2mm 215.9mm'];
    $paperSize = array_key_exists($paperSize, $paperSizes) ? $paperSize : 'A4';
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Report Biaya Agen</title>
    <style>
        @page { size: {{ $paperSizes[$paperSize] }}; margin: 9mm; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #f3f4f6; color: #111; font-family: Arial, sans-serif; font-size: 9px; line-height: 1.35; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; max-width: 390mm; margin: 16px auto; font-size: 12px; }
        .toolbar a, .toolbar button { display: inline-block; padding: 7px 10px; border: 1px solid #d1d5db; border-radius: 5px; background: white; color: #111; text-decoration: none; cursor: pointer; font: inherit; }
        .toolbar .active, .toolbar button { background: #4338ca; border-color: #4338ca; color: white; }
        .sheet { max-width: 390mm; margin: 0 auto 20px; padding: 9mm; background: white; }
        h1 { margin: 0 0 10px; padding-bottom: 5px; border-bottom: 2px solid #222; text-align: center; font-size: 17px; letter-spacing: .4px; }
        .meta { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .meta td { padding: 2px 4px 2px 0; vertical-align: top; }
        .meta .label { width: 90px; font-weight: bold; }
        .meta .separator { width: 10px; }
        .items { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .items th, .items td { border: 1px solid #333; padding: 4px 3px; vertical-align: top; overflow-wrap: anywhere; }
        .items th { background: #f2f2f2; text-align: center; }
        .items thead { display: table-header-group; }
        .items tfoot { display: table-row-group; }
        .items tr { break-inside: avoid; page-break-inside: avoid; }
        .right { text-align: right; }
        .center { text-align: center; }
        .small { color: #555; font-size: 8px; }
        @media print {
            body { background: white; }
            .toolbar { display: none; }
            .sheet { max-width: none; margin: 0; padding: 0; }
            .items th { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <span>Ukuran kertas:</span>
            @foreach(array_keys($paperSizes) as $size)
                <a href="{{ route('report.biaya-agen.print', array_merge($validated, ['paper_size' => $size])) }}" class="{{ $paperSize === $size ? 'active' : '' }}">{{ $size }}</a>
            @endforeach
        </div>
        <button type="button" onclick="window.print()">Cetak Report</button>
    </div>

    <main class="sheet">
        <h1>REPORT BIAYA AGEN</h1>
        <table class="meta">
            <tr><td class="label">Kapal</td><td class="separator">:</td><td>{{ $validated['kapal'] ?? 'Semua kapal' }}</td></tr>
            <tr><td class="label">Periode</td><td class="separator">:</td><td>{{ isset($validated['start_date']) ? \Carbon\Carbon::parse($validated['start_date'])->format('d/m/Y').' - '.\Carbon\Carbon::parse($validated['end_date'])->format('d/m/Y') : 'Semua tanggal' }}</td></tr>
            <tr><td class="label">Jenis Biaya</td><td class="separator">:</td><td>{{ empty($validated['jenis_biaya']) ? 'Semua jenis biaya' : implode(', ', $validated['jenis_biaya']) }}</td></tr>
            <tr><td class="label">Jumlah Item</td><td class="separator">:</td><td>{{ $rows->count() }}</td></tr>
        </table>

        <table class="items">
            <thead><tr>
                <th style="width:3%">No.</th><th style="width:7%">Tanggal</th><th style="width:9%">Invoice / Referensi</th>
                <th style="width:9%">Kapal / Voyage</th><th style="width:9%">Vendor / Lokasi</th><th style="width:11%">Jenis Biaya</th>
                <th style="width:5%">Qty</th><th style="width:8%">Harga</th><th style="width:8%">Jasa Air</th>
                <th style="width:8%">Subtotal</th><th style="width:7%">PPh</th><th style="width:8%">Penyesuaian</th><th style="width:8%">Total</th>
            </tr></thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td class="center">{{ \Carbon\Carbon::parse($row->tanggal_invoice_vendor ?: $row->tanggal_biaya_kapal)->format('d/m/Y') }}</td>
                        <td>{{ $row->nomor_invoice ?: '-' }}@if($row->nomor_referensi)<br><span class="small">{{ $row->nomor_referensi }}</span>@endif</td>
                        <td>{{ $row->kapal ?: '-' }}<br><span class="small">Voyage {{ $row->voyage ?: '-' }}</span></td>
                        <td>{{ $row->vendor ?: '-' }}<br><span class="small">{{ $row->lokasi ?: '-' }}</span></td>
                        <td>{{ $row->type_keterangan ?: '-' }}{{ $row->is_lumpsum ? ' (Lumpsum)' : '' }}</td>
                        <td class="right">{{ $row->is_lumpsum ? '-' : number_format($row->kuantitas, 2, ',', '.') }}</td>
                        <td class="right">{{ number_format($row->harga, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($row->jasa_air, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($row->sub_total, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($row->pph, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($row->adjustment, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($row->grand_total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="13" class="center">Tidak ada biaya agen untuk pilihan ini.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr><th colspan="9" class="right">TOTAL BIAYA AGEN (Rp)</th><th class="right">{{ number_format($summary['subtotal'], 0, ',', '.') }}</th><th class="right">{{ number_format($summary['pph'], 0, ',', '.') }}</th><th class="right">{{ number_format($summary['adjustment'], 0, ',', '.') }}</th><th class="right">{{ number_format($summary['grand_total'], 0, ',', '.') }}</th></tr>
            </tfoot>
        </table>
    </main>
</body>
</html>
