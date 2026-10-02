<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo DP TEMAS - {{ $stage->biayaKapal->nomor_invoice }}</title>
    <style>
        * { box-sizing: border-box; }
        @page { size: 165mm 215mm; margin: 0; }
        html, body { margin: 0; padding: 0; color: #111; font: 14px/1.7 Arial, sans-serif; }
        .page { width: 100%; margin: 0; padding: 10mm 12mm; }
        .toolbar { display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 12px; }
        button, .back { border: 0; border-radius: 4px; padding: 7px 12px; color: #fff; cursor: pointer; text-decoration: none; font-size: 12px; }
        button { background: #2563eb; }
        .back { background: #6b7280; }
        .paper-info { margin-right: auto; color: #475569; font-size: 12px; align-self: center; }
        .memo-date { margin: 0; text-align: right; }
        .memo-title { margin: 12px 0 22px; text-align: center; font-size: 20px; font-weight: bold; text-decoration: underline; }
        .memo-line { margin: 8px 0; }
        .amount-words { margin: 14px 0 24px; font-style: italic; }
        .account { margin: 10px 0 22px 24px; border-collapse: collapse; }
        .account td { padding: 2px 6px 2px 0; vertical-align: top; }
        .account td:first-child { width: 145px; }
        @media screen {
            body { background: #e2e8f0; padding: 20px; }
            .page { width: 165mm; min-height: 215mm; margin: 0 auto; background: #fff; box-shadow: 0 4px 18px rgba(15, 23, 42, .15); }
        }
        @media print {
            html, body { width: 100%; min-height: 0; background: #fff; }
            body { padding: 0; }
            .page { width: 100%; min-height: 0; margin: 0; page-break-after: avoid; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    @php
        $invoice = $stage->biayaKapal;
        $detail = $stage->details->first();
        $date = $stage->tanggal_dp ?? $invoice->tanggal ?? $stage->created_at;
        $ship = $stage->kapal ?: $invoice->display_nama_kapal;
        $voyage = $stage->voyage ?: $invoice->display_no_voyage;
        $recipient = $detail?->penerima ?: ($invoice->penerima ?: $invoice->nama_vendor ?: '-');
        $virtualAccount = $detail?->nomor_rekening ?: ($invoice->nomor_rekening ?: '-');
        $bankName = $stage->nama_bank ?: ($invoice->bank?->name ?: '-');
        $amount = (float) $stage->nominal_dibayar;
        $amountWords = ucwords(trim(\App\Helpers\Terbilang::make((int) round($amount))));
    @endphp

    <div class="page">
        <div class="toolbar no-print">
            <span class="paper-info">Ukuran cetak: Setengah Folio (165 × 215 mm)</span>
            <button onclick="window.print()">Cetak</button>
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

    </div>
</body>
</html>
