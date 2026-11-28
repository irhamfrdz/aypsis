<!DOCTYPE html>
<html lang="id">
@php
    $papers = [
        'A4' => ['297mm 210mm', '287mm'],
        'Custom-215' => ['297mm 215mm', '287mm'],
        'Folio' => ['330.2mm 215.9mm', '320.2mm'],
        'Half-A4' => ['210mm 148.5mm', '200mm'],
        'Half-Custom-215' => ['215mm 148.5mm', '205mm'],
        'Half-Folio' => ['215.9mm 165.1mm', '205.9mm'],
    ];
    $paperSize = isset($papers[$paperSize]) ? $paperSize : 'A4';
    [$pageSize, $pageWidth] = $papers[$paperSize];
    $headers = $exportRows[4];
    $dataRows = array_slice($exportRows, 5, count($exportRows) - 10);
    $totalRow = $exportRows[count($exportRows) - 5];
    $tanggal = $exportRows[count($exportRows) - 2][8];
    $creator = $exportRows[count($exportRows) - 1][8];
    $columnWidths = [6, 11, 34, 22, 18, 9, 14, 11, 15, 17, 15, 15, 15, 15, 16];
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUML - {{ $puml->nomor_pranota }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { size: {{ $pageSize }}; margin: 5mm; }
        body { margin: 0; color: #000; background: #fff; font-family: Calibri, Arial, sans-serif; }
        .controls { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 12px; background: #f3f4f6; font-size: 13px; }
        .controls button { padding: 8px 16px; border: 0; border-radius: 4px; background: #4f46e5; color: white; cursor: pointer; }
        .sheet { width: {{ $pageWidth }}; margin: 8mm auto; }
        h1 { margin: 0 0 3mm; text-align: center; font-size: 12pt; }
        .period { margin: 0 0 5mm; text-align: center; font-size: 11pt; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 8pt; }
        th, td { border: 1px solid #000; padding: 4px 3px; vertical-align: middle; overflow-wrap: anywhere; }
        th { white-space: pre-line; text-align: center; }
        th, .total td { background: #ffff00; font-weight: bold; }
        .number { text-align: right; }
        .center { text-align: center; }
        .text { text-align: left; }
        .footer { margin-top: 10mm; margin-left: 56%; text-align: center; font-size: 10pt; font-weight: bold; break-inside: avoid; }
        .footer p { margin: 0 0 2mm; }
        thead { display: table-header-group; }
        tr { break-inside: avoid; }
        @media print {
            .controls { display: none; }
            .sheet { margin: 0; width: 100%; }
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="controls">
        <span>Cetak PUML — orientasi landscape. Data mengikuti Export Excel yang tersimpan.</span>
        @include('components.paper-selector', ['selectedSize' => $paperSize])
        <button type="button" onclick="window.print()">Cetak PUML</button>
    </div>
    <main class="sheet">
        <h1>{{ $exportRows[1][0] }}</h1>
        <p class="period">{{ $exportRows[2][0] }}</p>
        <table>
            <colgroup>
                @foreach($columnWidths as $width)
                    <col style="width: {{ $width / array_sum($columnWidths) * 100 }}%">
                @endforeach
            </colgroup>
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($dataRows as $row)
                    <tr>
                        @foreach($row as $column => $value)
                            <td class="{{ in_array($column, [0, 1, 5, 7]) ? 'center' : ($column >= 6 ? 'number' : 'text') }}">{{ $value === null || $value === '' ? '' : ($column >= 5 ? number_format($value, 0, ',', '.') : $value) }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="15" class="center">Tidak ada rincian penerima dalam pranota ini.</td></tr>
                @endforelse
                <tr class="total">
                    <td colspan="2"></td>
                    @foreach(array_slice($totalRow, 2, null, true) as $column => $value)
                        <td class="{{ $column >= 5 ? 'number' : 'center' }}">{{ $value === null || $value === '' ? '' : ($column >= 5 ? number_format($value, 0, ',', '.') : $value) }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        <div class="footer">
            <p>{{ $tanggal }}</p>
            <p>{{ $creator }}</p>
        </div>
    </main>
</body>
</html>
