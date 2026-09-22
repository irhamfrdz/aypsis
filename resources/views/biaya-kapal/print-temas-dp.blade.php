<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi DP TEMAS - {{ $stage->biayaKapal->nomor_invoice }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { size: 165.1mm 215.9mm; margin: 8mm; }
        html, body { margin: 0; padding: 0; color: #111; font: 12px/1.4 Arial, sans-serif; }
        .page { width: 100%; max-width: 155mm; margin: 0 auto; }
        .toolbar { display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 12px; }
        button, .back { border: 0; border-radius: 4px; padding: 7px 12px; color: #fff; cursor: pointer; text-decoration: none; font-size: 12px; }
        button { background: #2563eb; }
        .back { background: #6b7280; }
        .header { border-bottom: 2px solid #111; padding-bottom: 8px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; letter-spacing: .5px; }
        .header p { margin: 3px 0 0; color: #555; font-size: 11px; }
        .badge { display: inline-block; margin-top: 10px; padding: 4px 14px; border: 1px solid #1d4ed8; border-radius: 999px; color: #1d4ed8; font-weight: bold; }
        .info { width: 100%; margin: 18px 0 12px; border-collapse: collapse; }
        .info td { padding: 4px 0; vertical-align: top; }
        .info td:first-child { width: 35%; color: #555; }
        .info td:nth-child(2) { font-weight: bold; }
        .amount { margin: 18px 0; border: 2px solid #111; padding: 14px; text-align: center; }
        .amount .label { color: #555; font-size: 11px; text-transform: uppercase; }
        .amount .value { margin-top: 4px; font-size: 24px; font-weight: bold; }
        .note { margin-top: 14px; padding: 10px; border: 1px solid #aaa; background: #f8fafc; }
        .signatures { width: 100%; margin-top: 46px; border-collapse: collapse; text-align: center; }
        .signatures td { width: 50%; padding: 4px; }
        .signatures .space { height: 48px; }
        .small { color: #666; font-size: 10px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    @php
        $invoice = $stage->biayaKapal;
        $detail = $stage->details->first();
        $date = $invoice->tanggal ?? $stage->created_at;
        $ship = $stage->kapal ?: $invoice->display_nama_kapal;
        $voyage = $stage->voyage ?: $invoice->display_no_voyage;
        $recipient = $detail?->penerima ?: ($invoice->penerima ?: $invoice->nama_vendor ?: '-');
        $reference = $detail?->nomor_referensi ?: ($invoice->nomor_referensi ?: '-');
    @endphp

    <div class="page">
        <div class="toolbar no-print">
            <button onclick="window.print()">🖨 Cetak</button>
            <a class="back" href="{{ route('biaya-kapal.print-temas', $invoice->id) }}">Kembali</a>
        </div>

        <div class="header">
            <h1>BUKTI PEMBAYARAN</h1>
            <p>Biaya Kapal TEMAS</p>
            <span class="badge">DP / UANG MUKA</span>
        </div>

        <table class="info">
            <tr><td>Nomor invoice</td><td>{{ $invoice->nomor_invoice ?: '-' }}</td></tr>
            <tr><td>Tanggal pembayaran</td><td>{{ $date ? $date->format('d/m/Y') : '-' }}</td></tr>
            <tr><td>Kapal / voyage</td><td>{{ $ship ?: '-' }} / {{ $voyage ?: '-' }}</td></tr>
            <tr><td>Dibayarkan kepada</td><td>{{ $recipient }}</td></tr>
            <tr><td>Nomor referensi</td><td>{{ $reference }}</td></tr>
        </table>

        <div class="amount">
            <div class="label">Nominal DP yang dibayarkan</div>
            <div class="value">Rp {{ number_format((float) $stage->nominal_dibayar, 0, ',', '.') }}</div>
        </div>

        <div class="note">
            <strong>Keterangan</strong><br>
            Pembayaran ini dicatat sebagai uang muka biaya TEMAS. Nilai tagihan akhir dan pelunasan akan dihitung pada transaksi berikutnya.
            @if($invoice->keterangan)
                <br><br>{{ $invoice->keterangan }}
            @endif
        </div>

        <table class="signatures">
            <tr><td>Pembayar</td><td>Penerima</td></tr>
            <tr class="space"><td></td><td></td></tr>
            <tr><td>( __________________ )</td><td>( __________________ )</td></tr>
        </table>
        <p class="small" style="text-align:center; margin-top:18px;">Dokumen ini dicetak dari sistem AYPSIS.</p>
    </div>
</body>
</html>
