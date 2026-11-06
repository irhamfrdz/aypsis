<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Rekap Pemakaian Ban</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; font-size: 11px; margin: 24px; }
        h1 { font-size: 19px; margin: 0 0 12px; } p { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #cbd5e1; padding: 7px; text-align: left; vertical-align: top; overflow-wrap: anywhere; }
        th { background: #f1f5f9; } thead { display: table-header-group; } tr { break-inside: avoid; }
        .actions { margin-bottom: 20px; } button { padding: 8px 16px; cursor: pointer; }
        @media print { @page { size: A4 landscape; margin: 10mm; } body { margin: 0; } .actions { display: none; } }
    </style>
</head>
<body>
    <div class="actions"><button type="button" onclick="window.print()">Cetak</button></div>
    <h1>Rekap Pemakaian Ban</h1>
    <p><strong>Unit:</strong> {{ $selectedUnitName ?: ($selectedUnit?->nomor_polisi && $selectedUnit->nomor_polisi !== '-' ? $selectedUnit->nomor_polisi : ($selectedUnit?->no_kir ?: '-')) }}</p>
    <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    <p><strong>Total pemakaian:</strong> {{ $items->count() }} ban</p>
    <table>
        <thead><tr><th>Tanggal</th><th>Bukti Pakai</th><th>Tipe Ban</th><th>Merek / Ukuran</th><th>Nomor Seri</th><th>Unit</th><th>Penerima</th><th>Keterangan</th></tr></thead>
        <tbody>
            @forelse($items as $ban)
            <tr><td>{{ $ban->tanggal_digunakan?->format('d/m/Y') ?? '-' }}</td><td>{{ $ban->nomor_bukti_pakai ?: '-' }}</td><td>{{ $ban->nama_ban ?: '-' }}</td><td>{{ $ban->merk ?: '-' }} / {{ $ban->ukuran ?: '-' }}</td><td>{{ $ban->nomor_seri ?: '-' }}</td><td>{{ $ban->unit_name ?: ($ban->mobil?->nomor_polisi ?: ($ban->mobil?->no_kir ?: '-')) }}</td><td>{{ $ban->penerima_name ?: '-' }}</td><td>{{ $ban->keterangan ?: '-' }}</td></tr>
            @empty
            <tr><td colspan="8">Tidak ada pemakaian ban untuk unit dan periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    <script>window.addEventListener('load', () => window.print());</script>
</body>
</html>
