<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo DP TEMAS - {{ $stage->biayaKapal->nomor_invoice }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { size: 165.1mm 215.9mm; margin: 8mm; }
        html, body { margin: 0; padding: 0; color: #111; font: 14px/1.7 Arial, sans-serif; }
        .page { width: 100%; max-width: 155mm; margin: 0 auto; }
        .toolbar { display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 12px; }
        button, .back { border: 0; border-radius: 4px; padding: 7px 12px; color: #fff; cursor: pointer; text-decoration: none; font-size: 12px; }
        button { background: #2563eb; }
        .back { background: #6b7280; }
        .memo-date { margin-top: 18px; text-align: right; }
        .memo-title { margin: 18px 0 28px; text-align: center; font-size: 20px; font-weight: bold; text-decoration: underline; }
        .memo-line { margin: 8px 0; }
        .amount-words { margin: 14px 0 24px; font-style: italic; }
        .account { margin: 10px 0 22px 24px; border-collapse: collapse; }
        .account td { padding: 2px 6px 2px 0; vertical-align: top; }
        .account td:first-child { width: 145px; }
        .meta { margin-top: 34px; padding-top: 10px; border-top: 1px solid #bbb; font-size: 10px; color: #555; }
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
        $virtualAccount = $detail?->nomor_rekening ?: ($invoice->nomor_rekening ?: '-');
        $bankName = $invoice->bank?->name ?: '-';
        $amount = (float) $stage->nominal_dibayar;
        $amountWords = ucwords(trim(\App\Helpers\Terbilang::make((int) round($amount))));
    @endphp

    <div class="page">
        <div class="toolbar no-print">
            <button onclick="window.print()">🖨 Cetak</button>
            <a class="back" href="{{ route('biaya-kapal.print-temas', $invoice->id) }}">Kembali</a>
        </div>

        <div class="memo-date">{{ $date ? $date->locale('id')->translatedFormat('d F Y') : '-' }}</div>
        <div class="memo-title">Memo</div>

        <p class="memo-line">
            DP biaya kapal TEMAS {{ $ship ?: '-' }} voyage {{ $voyage ?: '-' }} sebesar
            <strong>Rp {{ number_format($amount, 0, ',', '.') }},-</strong>
        </p>
        <p class="amount-words">{{ $amountWords }} Rupiah</p>

        <p class="memo-line">Dikirim ke rekening sebagai berikut:</p>
        <table class="account">
            <tr><td>Nama bank</td><td>: {{ $bankName }}</td></tr>
            <tr><td>Virtual Account</td><td>: <strong>{{ $virtualAccount }}</strong></td></tr>
            <tr><td>Atas nama</td><td>: {{ $recipient }}</td></tr>
        </table>

        @if($stage->keterangan_dp || $invoice->keterangan)
            <p class="memo-line">{{ $stage->keterangan_dp ?: $invoice->keterangan }}</p>
        @endif

        <div class="meta">
            Nomor invoice: {{ $invoice->nomor_invoice ?: '-' }} &nbsp;|&nbsp;
            Nomor referensi: {{ $reference }}
        </div>
    </div>
</body>
</html>
