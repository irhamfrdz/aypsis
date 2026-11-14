<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pranota Top Up {{ $log->nomor_pranota }}</title>
    <style>
        body { margin: 0; background: #f3f4f6; color: #111827; font: 14px Arial, sans-serif; }
        .sheet { box-sizing: border-box; max-width: 780px; margin: 32px auto; padding: 40px; background: white; box-shadow: 0 4px 24px #0001; }
        h1 { margin: 0 0 4px; font-size: 22px; }
        h2 { margin: 32px 0 16px; font-size: 18px; text-align: center; }
        .muted { color: #6b7280; }
        .meta { display: grid; grid-template-columns: 160px 1fr; gap: 10px 16px; margin: 24px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #d1d5db; text-align: left; }
        th:last-child, td:last-child { text-align: right; }
        .total { font-weight: bold; background: #eff6ff; }
        .actions { max-width: 780px; margin: 20px auto 0; display: flex; gap: 10px; }
        .actions a, .actions button { border: 0; border-radius: 6px; padding: 10px 14px; background: #1d4ed8; color: white; text-decoration: none; cursor: pointer; font: inherit; }
        @media print { body { background: white; } .sheet { max-width: none; margin: 0; padding: 0; box-shadow: none; } .actions { display: none; } }
    </style>
</head>
<body>
    <div class="actions">
        @can('pranota-topup-kartu-bensin-batam-view')<a href="{{ route('pranota-topup-kartu-bensin-batam.index') }}">Daftar Pranota</a>@endcan
        @can('master-kartu-bensin-batam-view')<a href="{{ route('master-kartu-bensin-batam.history', $card->id) }}">Riwayat Kartu</a>@endcan
        <button type="button" onclick="window.print()">Cetak Pranota</button>
    </div>
    <main class="sheet">
        <h1>PT. ALEXINDO YAKIN PRIMA</h1>
        <div class="muted">Pranota pengisian saldo kartu bensin Batam</div>
        <h2>PRANOTA TOP UP KARTU BENSIN</h2>
        <div class="meta">
            <span>Nomor Pranota</span><strong>{{ $log->nomor_pranota }}</strong>
            <span>Tanggal</span><span>{{ $log->tanggal?->translatedFormat('d F Y') }}</span>
            <span>Nomor Kartu</span><span>{{ $card->nomor_kartu }}</span>
            <span>Nama Kartu</span><span>{{ $card->nama_kartu }}</span>
            <span>Provider</span><span>{{ $card->provider }}</span>
        </div>
        <table>
            <thead><tr><th>Rincian</th><th>Jumlah</th></tr></thead>
            <tbody>
                <tr><td>Top up saldo</td><td>Rp {{ number_format($log->nominal, 2, ',', '.') }}</td></tr>
                <tr><td>Biaya admin</td><td>Rp {{ number_format($log->biaya_admin, 2, ',', '.') }}</td></tr>
                <tr class="total"><td>Total pranota</td><td>Rp {{ number_format((float) $log->nominal + (float) $log->biaya_admin, 2, ',', '.') }}</td></tr>
            </tbody>
        </table>
        <p><strong>Keterangan:</strong> {{ $log->keterangan ?: '-' }}</p>
    </main>
</body>
</html>
