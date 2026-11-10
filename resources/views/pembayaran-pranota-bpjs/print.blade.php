<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak {{ $payment->nomor_pembayaran }}</title>
    <style>
        body{font-family:Arial,sans-serif;color:#111;margin:28px;font-size:12px}h1{font-size:20px;margin:0 0 4px}h2{font-size:14px;margin:22px 0 8px}.muted{color:#555}.meta{display:grid;grid-template-columns:1fr 1fr;gap:10px 28px;margin:20px 0}table{width:100%;border-collapse:collapse}th,td{padding:8px;border:1px solid #bbb;text-align:left}th{background:#f1f3f5}td.right,th.right{text-align:right}.total{font-weight:bold;background:#eefbf2}.toolbar{margin-bottom:20px}.toolbar button{padding:8px 14px}@page{size:A4 landscape;margin:12mm}thead{display:table-header-group}tr{break-inside:avoid}.right{white-space:nowrap}@media print{body{margin:0}.toolbar{display:none}}
    </style>
</head>
<body>
    <div class="toolbar"><button onclick="window.print()">Cetak</button></div>
    <h1>Pembayaran Pranota BPJS</h1><div class="muted">Nomor Pembayaran: {{ $payment->nomor_pembayaran }}</div>
    <div class="meta">
        <div><strong>Tanggal Pembayaran</strong><br>{{ $payment->tanggal_pembayaran?->format('d/m/Y') ?? '-' }}</div>
        <div><strong>Status</strong><br>Lunas</div>
        <div><strong>Akun Bank</strong>
            @forelse($payment->bank_allocations ?? [] as $allocation)
                <div>{{ $allocation['nomor_akun'] }} - {{ $allocation['nama_akun'] }}<br>Rp {{ number_format((float) $allocation['subtotal'], 2, ',', '.') }}</div>
            @empty
                <div>{{ $payment->bank?->nama_akun ?? '-' }}</div>
            @endforelse
        </div>
        <div><strong>Bank yang Digunakan</strong><br>{{ $payment->akunCoa?->nama_akun ?? '-' }}</div>
        <div><strong>Nomor Accurate</strong><br>{{ $payment->nomor_accurate ?: '-' }}</div>
        <div><strong>Dibuat Oleh</strong><br>{{ $payment->createdBy?->name ?? '-' }}</div>
        <div><strong>Keterangan</strong><br>{{ $payment->keterangan ?: '-' }}</div>
    </div>
    <h2>Pranota yang Dibayar</h2>
    <table><thead><tr><th>Nomor Pranota</th><th>Periode</th><th class="right">Dibayar</th></tr></thead><tbody>
        @foreach($payment->pranotas as $pranota)<tr><td>{{ $pranota->nomor_pranota }}</td><td>{{ sprintf('%02d', $pranota->periode_bulan) }}/{{ $pranota->periode_tahun }}</td><td class="right">Rp {{ number_format($pranota->pivot->subtotal, 2, ',', '.') }}</td></tr>@endforeach
    </tbody><tfoot><tr class="total"><td colspan="2">Total Pembayaran</td><td class="right">Rp {{ number_format($payment->total_pembayaran, 2, ',', '.') }}</td></tr></tfoot></table>
    @if($parts->isNotEmpty())
    <h2>Rincian Tipe yang Dibayar</h2>
    <table><thead><tr><th>Tipe</th><th>Akun Bank yang Digunakan</th><th class="right">Nominal</th><th class="right">TUNJANGAN</th><th class="right">BIAYA</th><th class="right">HUTANG</th></tr></thead><tbody>
        @foreach($parts as $part)
            @php($amounts = $journalAmounts[$part->komponen.'|'.$part->tipe] ?? ['tunjangan' => 0, 'biaya' => 0, 'hutang' => 0])
            <tr><td>{{ $part->tipe }}</td><td>{{ $accountLabels[$part->komponen.'|'.$part->tipe] ?? '-' }}</td><td class="right">Rp {{ number_format($part->subtotal, 2, ',', '.') }}</td><td class="right">Rp {{ number_format($amounts['tunjangan'], 2, ',', '.') }}</td><td class="right">Rp {{ number_format($amounts['biaya'], 2, ',', '.') }}</td><td class="right">Rp {{ number_format($amounts['hutang'], 2, ',', '.') }}</td></tr>
        @endforeach
    </tbody><tfoot><tr class="total"><td colspan="2">Total Rincian</td><td class="right">Rp {{ number_format($parts->sum('subtotal'), 2, ',', '.') }}</td><td class="right">Rp {{ number_format(collect($journalAmounts)->sum('tunjangan'), 2, ',', '.') }}</td><td class="right">Rp {{ number_format(collect($journalAmounts)->sum('biaya'), 2, ',', '.') }}</td><td class="right">Rp {{ number_format(collect($journalAmounts)->sum('hutang'), 2, ',', '.') }}</td></tr></tfoot></table>
    @endif
</body>
</html>
