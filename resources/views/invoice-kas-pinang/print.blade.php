<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->nomor_invoice }} - Invoice Kas Pinang</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 32px auto; max-width: 850px; font-size: 13px; }
        h1 { margin-bottom: 4px; }
        .muted { color: #6b7280; }
        .meta { display: flex; justify-content: space-between; margin: 24px 0; gap: 24px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 9px 8px; border-bottom: 1px solid #d1d5db; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
        .notes { margin-top: 30px; white-space: pre-wrap; }
        .actions { margin-bottom: 24px; }
        @media print { .actions { display: none; } body { margin: 0; max-width: none; } }
    </style>
</head>
<body>
    <div class="actions"><button onclick="window.print()">Cetak</button></div>
    <h1>Invoice Kas Pinang</h1>
    <div class="muted">Dokumen internal cabang Tanjung Pinang</div>
    <div class="meta">
        <div>
            <strong>Nomor:</strong> {{ $invoice->nomor_invoice }}<br>
            <strong>Tanggal:</strong> {{ $invoice->tanggal_transaksi?->format('d/m/Y') }}<br>
            <strong>Status:</strong> {{ ucfirst($invoice->status) }}<br>
            <strong>Arus kas:</strong> Kas {{ ucfirst($invoice->arus_kas) }}
        </div>
        <div>
            <strong>Pihak terkait:</strong> {{ $invoice->pihak_terkait ?: '-' }}<br>
            <strong>Referensi:</strong> {{ $invoice->referensi ?: '-' }}<br>
            <strong>Akun Kas/Bank:</strong> {{ $invoice->akunKas?->nomor_akun }} - {{ $invoice->akunKas?->nama_akun }}<br>
            <strong>Akun Lawan:</strong> {{ $invoice->akunLawan?->nomor_akun }} - {{ $invoice->akunLawan?->nama_akun }}
        </div>
    </div>
    <table>
        <thead><tr><th>No.</th><th>Uraian</th><th class="right">Jumlah</th><th>Satuan</th><th class="right">Harga Satuan</th><th class="right">Subtotal</th></tr></thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td><td>{{ $item->uraian }}</td>
                    <td class="right">{{ number_format((float) $item->jumlah, 2, ',', '.') }}</td><td>{{ $item->satuan ?: '-' }}</td>
                    <td class="right">Rp {{ number_format((float) $item->harga_satuan, 2, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format((float) $item->subtotal, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot><tr><th colspan="5" class="right">Total</th><th class="right">Rp {{ number_format((float) $invoice->total, 2, ',', '.') }}</th></tr></tfoot>
    </table>
    @if($invoice->catatan)
        <div class="notes"><strong>Catatan:</strong><br>{{ $invoice->catatan }}</div>
    @endif
</body>
</html>
