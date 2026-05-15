<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $suratJalan->nomor_surat_jalan }} - Surat Jalan {{ $suratJalan->tipe_label }} Kontainer Sewa</title>
    <style>
        @page { size: A4; margin: 15mm; }
        body { font-family: 'Arial', sans-serif; font-size: 12px; line-height: 1.5; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; font-weight: bold; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .header h2 { font-size: 14px; font-weight: bold; margin: 5px 0 0; text-transform: uppercase; }
        .header p { margin: 3px 0 0; font-size: 11px; color: #666; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 3px 5px; font-size: 12px; vertical-align: top; }
        .info-table td:first-child { width: 140px; font-weight: bold; }
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .main-table th, .main-table td { border: 1px solid #333; padding: 6px 8px; text-align: left; font-size: 11px; }
        .main-table th { background-color: #f0f0f0; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 10px; }
        .main-table td.center { text-align: center; }
        .tipe-badge { display: inline-block; padding: 2px 10px; border-radius: 3px; font-weight: bold; font-size: 11px; color: white; }
        .tipe-pengambilan { background-color: #059669; }
        .tipe-pengembalian { background-color: #ea580c; }
        .signatures { margin-top: 40px; width: 100%; }
        .signatures td { width: 33.33%; text-align: center; padding-top: 10px; vertical-align: top; font-size: 12px; }
        .signatures .sign-space { height: 60px; }
        .signatures .name { border-top: 1px solid #333; display: inline-block; padding-top: 5px; min-width: 120px; }
        .keterangan { margin-bottom: 15px; padding: 8px 10px; border: 1px solid #ccc; border-radius: 3px; background-color: #fafafa; }
        .keterangan-label { font-weight: bold; font-size: 11px; margin-bottom: 3px; }
        .nomor-sj { font-size: 13px; font-weight: bold; text-align: right; margin-bottom: 15px; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin: 10px 0;">
        <button onclick="window.print()" style="padding: 8px 20px; background: #0891b2; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
            🖨️ Cetak Surat Jalan
        </button>
        <a href="{{ route('surat-jalan-kontainer-sewa.show', $suratJalan->id) }}" style="padding: 8px 20px; background: #6b7280; color: white; border: none; border-radius: 4px; text-decoration: none; font-size: 14px; margin-left: 5px;">
            ← Kembali
        </a>
    </div>

    {{-- Header --}}
    <div class="header">
        <h1>PT. ANUGERAH YAKIN PERKASA</h1>
        <h2>Surat Jalan {{ $suratJalan->tipe_label }} Kontainer Sewa</h2>
        <p>Jl. Raya Pelabuhan Kav. 1, Tanjung Priok, Jakarta Utara</p>
    </div>

    {{-- Nomor SJ --}}
    <div class="nomor-sj">
        No: {{ $suratJalan->nomor_surat_jalan }}
    </div>

    {{-- Info --}}
    <table class="info-table">
        <tr>
            <td>Tanggal</td>
            <td>: {{ $suratJalan->tanggal->format('d / m / Y') }}</td>
            <td style="width: 100px; font-weight: bold;">Tipe</td>
            <td>: <span class="tipe-badge {{ $suratJalan->tipe === 'pengambilan' ? 'tipe-pengambilan' : 'tipe-pengembalian' }}">{{ strtoupper($suratJalan->tipe_label) }}</span></td>
        </tr>
        <tr>
            <td>Vendor</td>
            <td>: {{ $suratJalan->vendor ?? '-' }}</td>
            <td style="font-weight: bold;">Jumlah</td>
            <td>: {{ $suratJalan->items->count() }} unit</td>
        </tr>
        <tr>
            <td>Supir</td>
            <td>: {{ $suratJalan->supir ?? '-' }}</td>
            <td style="font-weight: bold;">No. Plat</td>
            <td>: {{ $suratJalan->no_plat ?? '-' }}</td>
        </tr>
        @if($suratJalan->lokasi_pengambilan)
        <tr>
            <td>Lokasi Pengambilan</td>
            <td colspan="3">: {{ $suratJalan->lokasi_pengambilan }}</td>
        </tr>
        @endif
        @if($suratJalan->lokasi_pengembalian)
        <tr>
            <td>Lokasi Pengembalian</td>
            <td colspan="3">: {{ $suratJalan->lokasi_pengembalian }}</td>
        </tr>
        @endif
    </table>

    @if(!empty($suratJalan->keterangan))
    <div class="keterangan">
        <div class="keterangan-label">Keterangan:</div>
        <div>{{ $suratJalan->keterangan }}</div>
    </div>
    @endif

    {{-- Table --}}
    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th>Nomor Kontainer</th>
                <th>Vendor</th>
                <th>Ukuran</th>
                <th>Tipe</th>
                <th>Kondisi</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suratJalan->items as $i => $item)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $item->nomor_kontainer }}</td>
                <td>{{ $item->vendor ?? '-' }}</td>
                <td class="center">{{ $item->ukuran ?? '-' }}</td>
                <td class="center">{{ $item->tipe_kontainer ?? '-' }}</td>
                <td class="center">{{ $item->kondisi_label }}</td>
                <td>{{ $item->catatan_kondisi ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Signatures --}}
    <table class="signatures">
        <tr>
            <td>Dibuat Oleh,</td>
            <td>Diketahui Oleh,</td>
            <td>Diterima Oleh,</td>
        </tr>
        <tr>
            <td><div class="sign-space"></div></td>
            <td><div class="sign-space"></div></td>
            <td><div class="sign-space"></div></td>
        </tr>
        <tr>
            <td><span class="name">(........................)</span></td>
            <td><span class="name">(........................)</span></td>
            <td><span class="name">(........................)</span></td>
        </tr>
    </table>
</body>
</html>
