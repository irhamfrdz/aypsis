<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pranota Stock Amprahan - {{ $pranota->nomor_pranota }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 11pt; margin: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; color: #000; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 10pt; color: #666; }
        
        .info-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .info-table td { padding: 5px 0; vertical-align: top; }
        .info-table .label { width: 140px; font-weight: bold; }
        .info-table .separator { width: 20px; text-align: center; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; table-layout: fixed; }
        .items-table th { background-color: #f2f2f2; border: 1px solid #ccc; padding: 10px; text-align: left; font-size: 10pt; text-transform: uppercase; }
        .items-table td { border: 1px solid #ccc; padding: 8px 10px; font-size: 10pt; word-wrap: break-word; }
        
        .footer { margin-top: 50px; width: 100%; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-table td { width: 33.33%; text-align: center; vertical-align: bottom; height: 120px; }
        .signature-line { border-bottom: 1px solid #000; width: 180px; margin: 0 auto 5px; }
        .signature-label { font-size: 10pt; font-weight: bold; }

        .keterangan-section { margin-top: 20px; padding: 10px; border: 1px dashed #ccc; background-color: #fdfdfd; }
        .keterangan-title { font-weight: bold; font-size: 10pt; margin-bottom: 5px; text-decoration: underline; }
        .keterangan-text { font-size: 10pt; line-height: 1.4; color: #555; }

        @media print {
            body { margin: 10mm; }
            .no-print { display: none; }
            .header { border-bottom-color: #000; }
            .items-table th { background-color: #eee !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pranota Stock Amprahan</h1>
        <p>Laporan Penggunaan Barang Gudang PSA</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nomor Pranota</td>
            <td class="separator">:</td>
            <td><strong>{{ $pranota->nomor_pranota }}</strong></td>
            <td class="label">Nomor Accurate</td>
            <td class="separator">:</td>
            <td>{{ $pranota->nomor_accurate ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="separator">:</td>
            <td>{{ $pranota->tanggal_pranota ? $pranota->tanggal_pranota->format('d F Y') : '-' }}</td>
            <td class="label">Admin</td>
            <td class="separator">:</td>
            <td>{{ $pranota->creator->name ?? '-' }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40px; text-align: center;">No</th>
                <th style="width: 250px;">Nama Barang / Sparepart</th>
                <th style="width: 80px; text-align: center;">Jumlah</th>
                <th style="width: 70px; text-align: center;">Satuan</th>
                <th>Keterangan Penggunaan</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @if(is_array($pranota->items))
                @foreach($pranota->items as $item)
                <tr>
                    <td style="text-align: center;">{{ $i++ }}</td>
                    <td>{{ $item['nama_barang'] ?? '-' }}</td>
                    <td style="text-align: center;">{{ number_format($item['jumlah'] ?? 0, 2, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $item['satuan'] ?? '-' }}</td>
                    <td>{{ $item['keterangan'] ?? '-' }}</td>
                </tr>
                @endforeach
            @else
                <tr><td colspan="5" style="text-align: center; color: #999;">Tidak ada data item</td></tr>
            @endif
        </tbody>
    </table>

    @if($pranota->keterangan)
    <div class="keterangan-section">
        <div class="keterangan-title">Catatan Tambahan:</div>
        <div class="keterangan-text">{{ $pranota->keterangan }}</div>
    </div>
    @endif

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-label">Dibuat Oleh</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-label">Gudang / Logistik</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div class="signature-label">Penerima Barang</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="no-print" style="margin-top: 50px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #444; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
            Cetak Laporan
        </button>
    </div>

    <script>
        // Auto print or other logic
    </script>
</body>
</html>
