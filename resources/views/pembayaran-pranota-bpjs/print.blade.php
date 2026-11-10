<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak {{ $payment->nomor_pembayaran }}</title>
    <style>
        *{box-sizing:border-box}body{font-family:Arial,Helvetica,sans-serif;color:#1e293b;background:#eef1f5;margin:0;font-size:11px;line-height:1.45}.sheet{max-width:1120px;margin:24px auto 40px;padding:32px 36px;background:#fff;box-shadow:0 4px 24px #172b4d12}.toolbar{max-width:1120px;margin:22px auto 0;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:0 2px;color:#64748b;font-size:12px}.toolbar button{border:0;border-radius:7px;background:#17365a;color:#fff;padding:11px 22px;font-weight:700;cursor:pointer}.toolbar button:hover{background:#244b79}.header{display:flex;justify-content:space-between;align-items:flex-start;gap:24px;border-bottom:3px solid #17365a;padding-bottom:18px}.brand{font-size:22px;font-weight:800;letter-spacing:2px;color:#17365a}.company{font-size:11px;font-weight:700;margin-top:3px}.subtitle{font-size:10px;color:#64748b;margin-top:3px}.document{text-align:right}.eyebrow{font-size:9px;text-transform:uppercase;letter-spacing:1.5px;color:#64748b;margin-bottom:5px}h1{font-size:20px;margin:0;color:#17365a;line-height:1.25}.reference{font-size:12px;font-weight:700;margin-top:7px}.paid{display:inline-block;margin-top:8px;padding:3px 10px;border:1px solid #99bba9;color:#246346;border-radius:4px;font-size:9px;font-weight:700;letter-spacing:1px}.meta{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px 24px;padding:18px 0;border-bottom:1px solid #dce2ea}.meta strong,.note strong{display:block;color:#64748b;font-size:9px;text-transform:uppercase;letter-spacing:.6px;margin-bottom:3px}.meta br{display:none}.meta .accounts{grid-column:span 2}.allocation{display:flex;justify-content:space-between;gap:14px;padding:4px 0;border-bottom:1px dotted #e2e8f0}.allocation:last-child{border:0}.allocation span:last-child{white-space:nowrap;font-weight:600}.note{padding:12px 0;color:#475569;white-space:pre-line;overflow-wrap:anywhere}h2{font-size:11px;margin:19px 0 8px;color:#17365a;text-transform:uppercase;letter-spacing:.8px}table{width:100%;border-collapse:collapse;font-size:10px}th,td{padding:9px 10px;border-bottom:1px solid #dce2ea;text-align:left;vertical-align:top;overflow-wrap:anywhere}th{background:#edf1f6;color:#34465e;font-size:9px;text-transform:uppercase;letter-spacing:.3px;border-top:1px solid #dce2ea}tbody tr:nth-child(even){background:#fafbfd}.right{text-align:right;white-space:nowrap;font-variant-numeric:tabular-nums}.total{font-weight:700;background:#edf3f8;border-top:1px solid #b9c8d8}.total td{padding-top:11px;padding-bottom:11px}.signatures{display:flex;justify-content:flex-end;gap:38px;margin-top:28px;break-inside:avoid}.signature{width:150px;text-align:center;font-size:10px;color:#64748b}.signature .name{margin-top:48px;border-bottom:1px solid #94a3b8;min-height:20px;color:#1e293b;font-weight:600}.footer{display:flex;justify-content:space-between;gap:20px;border-top:1px solid #dce2ea;margin-top:22px;padding-top:9px;font-size:9px;color:#64748b}thead{display:table-header-group}tfoot{display:table-row-group}tr{break-inside:avoid}h2{break-after:avoid}@page{size:A4 landscape;margin:12mm}@media print{body{background:#fff;font-size:10px}.toolbar{display:none}.sheet{max-width:none;margin:0;padding:0;box-shadow:none}th,.total{-webkit-print-color-adjust:exact;print-color-adjust:exact}.header{padding-bottom:12px}.meta{padding:12px 0;gap:10px 20px}.signatures{margin-top:22px}table{font-size:9px}th,td{padding:7px 8px}}@media screen and (max-width:760px){.sheet{margin:12px;padding:20px;overflow-x:auto}.toolbar{margin:14px 12px}.header{min-width:650px}.meta{grid-template-columns:1fr 1fr}.meta .accounts{grid-column:span 2}table{min-width:720px}}

        /* Compact print layout: keep ordinary payment receipts on one sheet. */
        @page{size:A4 landscape;margin:8mm}
        @media print{
            body{font-size:9px;line-height:1.2}
            .sheet{width:100%;padding:0;margin:0}
            .header{padding-bottom:7px;border-bottom-width:2px;gap:14px}
            .brand{font-size:17px;letter-spacing:1.5px}
            .company{font-size:9px;margin-top:2px}
            .subtitle{font-size:8px;margin-top:2px}
            .eyebrow{font-size:8px;margin-bottom:2px}
            h1{font-size:15px}
            .reference{font-size:10px;margin-top:3px}
            .paid{font-size:8px;margin-top:4px;padding:2px 7px}
            .meta{grid-template-columns:1fr 1fr 1fr;gap:6px 18px;padding:8px 0}
            .meta strong,.note strong{font-size:8px;margin-bottom:2px}
            .allocation{padding:2px 0;gap:8px}
            .note{padding:6px 0;font-size:9px}
            h2{font-size:9px;margin:9px 0 5px;letter-spacing:.5px}
            table{font-size:8px;line-height:1.2}
            th{font-size:8px;letter-spacing:0}
            th,td{padding:4px 6px}
            .total td{padding-top:5px;padding-bottom:5px}
            .signatures{margin-top:12px;gap:24px}
            .signature{width:125px;font-size:8px}
            .signature .name{margin-top:22px;min-height:14px}
            .footer{margin-top:10px;padding-top:5px;font-size:8px}
        }
    </style>
</head>
<body>
    <div class="toolbar"><span>Pratinjau bukti pembayaran ? A4 landscape</span><button type="button" onclick="window.print()">Cetak / Simpan PDF</button></div>
    <main class="sheet">
    <header class="header">
        <div><div class="brand">AYPSIS</div><div class="company">PT. Alexindo YakinPrima Shipping</div><div class="subtitle">Administrasi Pembayaran BPJS</div></div>
        <div class="document"><div class="eyebrow">Bukti Pembayaran</div><h1>Pembayaran Pranota BPJS</h1><div class="reference">{{ $payment->nomor_pembayaran }}</div><span class="paid">LUNAS</span></div>
    </header>
    <div class="meta">
        <div><strong>Tanggal Pembayaran</strong><br>{{ $payment->tanggal_pembayaran?->format('d/m/Y') ?? '-' }}</div>

        <div class="accounts"><strong>Akun Bank</strong>
            @forelse($payment->bank_allocations ?? [] as $allocation)
                <div class="allocation"><span>{{ $allocation['nomor_akun'] }} - {{ $allocation['nama_akun'] }}</span><span>Rp {{ number_format((float) $allocation['subtotal'], 2, ',', '.') }}</span></div>
            @empty
                <div>{{ $payment->bank?->nama_akun ?? '-' }}</div>
            @endforelse
        </div>
        <div><strong>Bank yang Digunakan</strong><br>{{ $payment->akunCoa?->nama_akun ?? '-' }}</div>
        <div><strong>Nomor Accurate</strong><br>{{ $payment->nomor_accurate ?: '-' }}</div>
        <div><strong>Dibuat Oleh</strong><br>{{ $payment->createdBy?->name ?? '-' }}</div>
    </div>
    <div class="note"><strong>Keterangan</strong>{{ $payment->keterangan ?: '-' }}</div>
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
    <div class="signatures">
        <div class="signature">Dibuat oleh<div class="name">{{ $payment->createdBy?->name ?? ' ' }}</div></div>
        <div class="signature">Diperiksa oleh<div class="name">&nbsp;</div></div>
        <div class="signature">Disetujui oleh<div class="name">&nbsp;</div></div>
    </div>
    <footer class="footer"><span>AYPSIS ? Bukti Pembayaran Pranota BPJS</span><span>{{ $payment->nomor_pembayaran }}</span></footer>
    </main>
</body>
</html>
