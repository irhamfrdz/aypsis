<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak {{ $purchaseOrder->nomor_po }}</title>
    <style>
        @page { size: A4 portrait; margin: 10mm; }
        body { max-width: 190mm; margin: 24px auto; color: #111; font: 12pt 'Times New Roman', serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 7px; vertical-align: top; }
        thead { display: table-header-group; }
        tr { break-inside: avoid; }
        .letterhead { display: flex; align-items: center; gap: 18px; padding: 6px 0 18px; border-bottom: 4px double #343434; text-align: center; }
        .letterhead-logo { width: 90px; height: 100px; object-fit: contain; flex: none; }
        .letterhead-text { flex: 1; }
        .letterhead-company { color: #a55c62; font-size: 21pt; font-weight: bold; white-space: nowrap; }
        .letterhead-contact { font-size: 9pt; line-height: 1.4; }
        .toolbar { margin-bottom: 24px; display: flex; gap: 10px; font: 14px Arial, sans-serif; }
        .toolbar button, .toolbar a { border: 1px solid #777; border-radius: 6px; padding: 8px 14px; background: white; color: #111; text-decoration: none; cursor: pointer; }
        @media print { body { width: 190mm; max-width: none; margin: 0; } .toolbar { display: none; } .print-sheet { zoom: var(--print-scale, 1); margin-inline: auto; } }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="printPurchaseOrder()">Cetak</button><a href="{{ route('purchase-orders.show', $purchaseOrder) }}">Kembali ke PO</a></div>
    <main class="print-sheet" id="po-print-sheet">
    <header class="letterhead">
        <img class="letterhead-logo" src="{{ asset('images/logo_transparent.png') }}" alt="Logo PT. Alexindo Yakinprima">
        <div class="letterhead-text">
            <div style="font-size:13pt;font-style:italic">Perusahaan Pelayaran Nasional</div>
            <div class="letterhead-company">PT. ALEXINDO YAKINPRIMA</div>
            <div class="letterhead-contact">Jl. Pluit Raya No. 8 Blok B No. 12 Jakarta 14440<br>
                Telp. (021) 6606231-6614175-76 &nbsp; Fax. (021) 6619907<br>
                Email: alexindo_yakinprima@yahoo.co.id</div>
        </div>
    </header>
    <h2 style="text-align:center;font-size:17pt;margin:22px 0 2px">PURCHASE ORDER</h2>
    <div style="text-align:center;font-weight:bold">{{ $purchaseOrder->nomor_referensi ?: $purchaseOrder->nomor_po }}</div>
    <div style="margin-top:30px">
        <div>Jakarta, {{ $purchaseOrder->tanggal_po->translatedFormat('d F Y') }}</div>
        <div>Nomor : {{ $purchaseOrder->nomor_po }}</div>
    </div>
    <div style="margin-top:28px">
        <div>Kepada Yth,</div>
        <div style="font-weight:bold">{{ $purchaseOrder->vendor }}</div>
        @if($purchaseOrder->alamat_vendor)<div style="white-space:pre-line">{{ $purchaseOrder->alamat_vendor }}</div>@endif
    </div>
    <p style="margin-top:28px">Dengan Hormat,</p>
    <p>Mohon dapat disediakan dan dikirim kepada kami sebagai berikut:</p>
    <table>
        <thead><tr><th style="width:35px">NO</th><th>KETERANGAN</th><th style="width:100px">KUANTITAS</th><th style="width:120px">HARGA</th><th style="width:120px">JUMLAH</th></tr></thead>
        <tbody>
            @foreach($purchaseOrder->items as $item)
                <tr><td style="text-align:center">{{ $item->urutan }}</td><td>{{ $item->deskripsi }}</td><td>{{ rtrim(rtrim(number_format((float) $item->kuantitas, 3, ',', '.'), '0'), ',') }} {{ $item->satuan }}</td><td style="text-align:right">{{ number_format((float) $item->harga_satuan, 2, ',', '.') }}</td><td style="text-align:right">{{ number_format((float) $item->jumlah, 2, ',', '.') }}</td></tr>
            @endforeach
            <tr><td colspan="4" style="text-align:right">SUBTOTAL</td><td style="text-align:right">{{ number_format((float) $purchaseOrder->subtotal, 2, ',', '.') }}</td></tr>
            @if((float) $purchaseOrder->diskon > 0)<tr><td colspan="4" style="text-align:right">DISKON</td><td style="text-align:right">{{ number_format((float) $purchaseOrder->diskon, 2, ',', '.') }}</td></tr>@endif
            <tr><td colspan="4" style="text-align:right">PPN</td><td style="text-align:right">{{ number_format((float) $purchaseOrder->pajak, 2, ',', '.') }}</td></tr>
            <tr><td colspan="4" style="text-align:right;font-weight:bold">TOTAL</td><td style="text-align:right;font-weight:bold">{{ number_format((float) $purchaseOrder->total, 2, ',', '.') }}</td></tr>
        </tbody>
    </table>
    <p style="margin-top:12px;font-style:italic">{{ ucfirst(trim(\App\Helpers\Terbilang::make((int) round((float) $purchaseOrder->total)))) }} rupiah</p>
    @if($purchaseOrder->catatan)<p style="white-space:pre-line;margin-top:24px">Note:<br>{{ $purchaseOrder->catatan }}</p>@endif
    <p style="margin-top:30px">Demikianlah purchase order ini kami sampaikan sekian dan terima kasih.</p>
    <div style="margin:45px 10px 0 auto;width:180px;text-align:center">Hormat kami,<br><br><br><br><br>(____________________)</div>
    </main>
    <script>
        function fitPurchaseOrderToPage() {
            const sheet = document.getElementById('po-print-sheet');
            sheet.style.width = '190mm';
            const availableHeight = 270 * 96 / 25.4;
            const scale = Math.min(1, availableHeight / (sheet.scrollHeight + 8));
            sheet.style.setProperty('--print-scale', scale.toFixed(4));
        }
        function printPurchaseOrder() {
            fitPurchaseOrderToPage();
            window.print();
        }
        window.addEventListener('beforeprint', fitPurchaseOrderToPage);
        window.addEventListener('load', fitPurchaseOrderToPage);
    </script>
</body>
</html>
