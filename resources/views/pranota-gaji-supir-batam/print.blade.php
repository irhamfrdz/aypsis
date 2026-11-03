<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak {{ $pranota->nomor_pranota }}</title>
    <style>
        @page { size: A4; margin: 15mm; }
        body { font-family: Arial, sans-serif; color: #111827; font-size: 12px; }
        .toolbar { text-align: right; margin-bottom: 20px; }
        .toolbar button { padding: 8px 14px; cursor: pointer; }
        h1 { text-align: center; font-size: 18px; margin: 0 0 20px; }
        .meta { margin-bottom: 20px; line-height: 1.8; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #374151; padding: 7px; vertical-align: top; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
        .center { text-align: center; }
        .note { margin-top: 18px; white-space: pre-line; }
        .sign { margin-top: 55px; text-align: right; }
        .sign span { display: inline-block; min-width: 180px; text-align: center; border-top: 1px solid #111827; padding-top: 5px; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Cetak Pranota</button></div>
    <h1>PRANOTA GAJI SUPIR BATAM</h1>
    <div class="meta">Nomor: {{ $pranota->nomor_pranota }}<br>Tanggal: {{ $pranota->tanggal_pranota->format('d/m/Y') }}<br>Jumlah supir: {{ $pranota->items->count() }}</div>
    <table>
        <thead><tr><th width="5%">No.</th><th>Nama Supir</th><th>Periode Gaji</th><th width="20%" class="right">Gaji Bersih</th></tr></thead>
        <tbody>
            @foreach($pranota->items as $item)
                <tr><td class="center">{{ $loop->iteration }}</td><td>{{ $item->gaji?->karyawan?->nama_lengkap ?: 'Supir tidak tersedia' }}</td><td>{{ $item->gaji?->periode_text ?: '-' }}</td><td class="right">Rp {{ number_format((float) $item->total_gaji, 0, ',', '.') }}</td></tr>
            @endforeach
        </tbody>
        <tfoot><tr><th colspan="3" class="right">Total Pranota</th><th class="right">Rp {{ number_format((float) $pranota->total_gaji, 0, ',', '.') }}</th></tr></tfoot>
    </table>
    @if($pranota->keterangan)<p class="note"><strong>Keterangan:</strong> {{ $pranota->keterangan }}</p>@endif
    <div class="sign">Dibuat oleh,<br><br><br><br><span>{{ $pranota->creator?->name ?: '........................' }}</span></div>
</body>
</html>
