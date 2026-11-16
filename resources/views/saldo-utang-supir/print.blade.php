<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pranota Utang UTG-{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef1f5; color: #1e293b; font: 12px/1.6 Arial, sans-serif; }
        .toolbar { max-width: 794px; margin: 24px auto 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #64748b; padding: 0 12px; }
        button { border: 0; border-radius: 6px; padding: 10px 18px; background: #17365a; color: white; font-weight: bold; cursor: pointer; }
        .sheet { max-width: 794px; margin: 0 auto 32px; padding: 36px; background: white; }
        header { display: flex; justify-content: space-between; gap: 20px; border-bottom: 2px solid #17365a; padding-bottom: 20px; }
        .brand { color: #17365a; font-size: 24px; font-weight: bold; letter-spacing: 2px; }
        h1 { margin: 0; font-size: 20px; color: #17365a; }
        .document { text-align: right; }
        .muted { color: #64748b; font-size: 11px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin: 24px 0; }
        .label { color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: .6px; display: block; margin-bottom: 4px; }
        .value { font-weight: bold; overflow-wrap: anywhere; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 11px; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; overflow-wrap: anywhere; }
        th { background: #f1f5f9; font-size: 10px; color: #475569; }
        .number { text-align: right; font-variant-numeric: tabular-nums; }
        .total { font-weight: bold; background: #f1f5f9; }
        .note { margin-top: 22px; white-space: pre-wrap; overflow-wrap: anywhere; }
        .signatures { display: flex; justify-content: space-between; gap: 24px; margin-top: 40px; break-inside: avoid; }
        .signature { flex: 1; text-align: center; }
        .signature-name { margin-top: 65px; border-bottom: 1px solid #94a3b8; min-height: 20px; overflow-wrap: anywhere; }
        footer { border-top: 1px solid #e2e8f0; margin-top: 28px; padding-top: 10px; font-size: 10px; color: #64748b; }
        tr { break-inside: avoid; } thead { display: table-header-group; }
        @page { size: A4 portrait; margin: 15mm; }
        @media print { body { background: white; } .toolbar { display: none; } .sheet { max-width: none; margin: 0; padding: 0; } th, .total { print-color-adjust: exact; -webkit-print-color-adjust: exact; } }
        @media screen and (max-width: 600px) { .sheet { padding: 20px; } header { flex-direction: column; } .document { text-align: left; } }
    </style>
</head>
<body>
    <div class="toolbar"><span>Pratinjau pranota · A4 potret</span><button type="button" onclick="window.print()">Cetak / Simpan PDF</button></div>
    <main class="sheet">
        <header>
            <div><div class="brand">AYPSIS</div><strong>PT. Alexindo YakinPrima Shipping</strong><div class="muted">Administrasi Utang Karyawan</div></div>
            <div class="document"><h1>Pranota Utang Karyawan</h1><strong>UTG-{{ str_pad($item->id, 6, '0', STR_PAD_LEFT) }}</strong><div class="muted">{{ $item->tipe === 'penambahan' ? 'Pinjaman / Kasbon' : 'Pembayaran / Potongan' }}</div></div>
        </header>
        <div class="meta">
            <div><span class="label">Nama Karyawan</span><div class="value">{{ $karyawan->nama_lengkap }}</div></div>
            <div><span class="label">NIK</span><div class="value">{{ $karyawan->nik ?: '—' }}</div></div>
            <div><span class="label">Tanggal Transaksi</span><div class="value">{{ $item->tanggal->format('d/m/Y') }}</div></div>
            <div><span class="label">Referensi / Judul</span><div class="value">{{ $item->referensi ?: '—' }}</div></div>
            <div style="grid-column: span 2"><span class="label">Akun Bank / Kas</span><div class="value">{{ $item->bankCoa ? $item->bankCoa->nomor_akun.' — '.$item->bankCoa->nama_akun : '—' }}</div></div>
            <div><span class="label">Nama Bank Karyawan</span><div class="value">{{ $item->nama_bank_karyawan ?: '—' }}</div></div>
            <div><span class="label">No. Rekening Karyawan</span><div class="value">{{ $item->no_rekening_karyawan ?: '—' }}</div></div>
        </div>
        <table aria-label="Rincian akun pranota">
            <thead><tr><th style="width: 8%">No.</th><th style="width: 40%">Akun COA</th><th style="width: 27%">Memo</th><th class="number" style="width: 25%">Nominal (Rp)</th></tr></thead>
            <tbody>
                @forelse($item->rincian_coa ?? [] as $account)
                    <tr><td>{{ $loop->iteration }}</td><td>{{ $account['nomor_akun'] ?? '—' }}<br>{{ $account['nama_akun'] ?? '—' }}</td><td style="white-space: pre-wrap">{{ $account['memo'] ?? '—' }}</td><td class="number">{{ isset($account['nominal']) ? number_format($account['nominal'], 2, ',', '.') : '—' }}</td></tr>
                @empty
                    <tr><td colspan="3">{{ $item->referensi ?: 'Transaksi utang karyawan' }}<div class="muted">Rincian akun COA tidak tercatat pada transaksi ini.</div></td><td class="number">{{ number_format($item->nominal, 2, ',', '.') }}</td></tr>
                @endforelse
                <tr class="total"><td colspan="3">Total Transaksi</td><td class="number">Rp {{ number_format($item->nominal, 2, ',', '.') }}</td></tr>
            </tbody>
        </table>
        <div class="note"><span class="label">Keterangan</span>{{ $item->keterangan ?: '—' }}</div>
        <div class="signatures">
            <div class="signature">Dibuat oleh<div class="signature-name"></div></div>
            <div class="signature">Disetujui oleh<div class="signature-name"></div></div>
            <div class="signature">Karyawan<div class="signature-name">{{ $karyawan->nama_lengkap }}</div></div>
        </div>
        <footer>Pranota ini merupakan rincian satu transaksi utang karyawan. Nominal yang tercantum adalah nominal transaksi, bukan saldo utang berjalan.</footer>
    </main>
</body>
</html>
