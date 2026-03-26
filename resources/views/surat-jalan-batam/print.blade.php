<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Surat Jalan Batam - {{ $suratJalan->no_surat_jalan }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 14px; margin: 0; padding: 20px; line-height: 1.5; color: #333; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .company-info h1 { margin: 0; font-size: 24px; color: #000; text-transform: uppercase; }
        .company-info p { margin: 2px 0; font-size: 12px; color: #666; }
        .document-title { font-size: 20px; font-weight: bold; text-align: center; margin-bottom: 30px; text-transform: uppercase; border: 1px solid #333; padding: 5px; background: #f9f9f9; }
        .info-grid { display: grid; grid-template-cols: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .info-section { border: 1px solid #eee; padding: 15px; border-radius: 5px; }
        .info-section h3 { margin: 0 0 10px 0; font-size: 14px; color: #888; text-transform: uppercase; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-row { display: flex; margin-bottom: 5px; }
        .info-label { width: 140px; font-weight: bold; font-size: 13px; color: #555; }
        .info-value { flex: 1; font-size: 13px; border-bottom: 1px dotted #ccc; }
        .unit-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .unit-table th, .unit-table td { border: 1px solid #333; padding: 12px; text-align: left; }
        .unit-table th { background-color: #f2f2f2; font-weight: bold; }
        .signatures { display: grid; grid-template-cols: 1fr 1fr 1fr; gap: 20px; text-align: center; margin-top: 50px; }
        .signature-box { height: 100px; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #eee; padding: 10px; border-radius: 5px; }
        .signature-box .name { font-weight: bold; text-decoration: underline; margin-top: 50px; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #4f46e5; color: white; border: none; border-radius: 5px; cursor: pointer;">Print Document</button>
    </div>

    <div class="header">
        <div class="company-info">
            <h1>PT. ANUGRAH YAKIN PACIFIC SURYA</h1>
            <p>Jalan Gajah Mada, Komp Ruko Gajah Mada Blok B No 12, Batam</p>
            <p>Telepon: (0778) 480XXXX | Email: info@aypsis.co.id</p>
        </div>
        <div style="text-align: right;">
            <p style="font-weight: bold; font-size: 16px; margin: 0;">SJ BATAM</p>
            <p style="font-size: 12px; color: #666;">Salinan Dokumen</p>
        </div>
    </div>

    <div class="document-title">Surat Jalan Batam</div>

    <div class="info-grid">
        <div class="info-section">
            <h3>Informasi Surat Jalan</h3>
            <div class="info-row">
                <span class="info-label">No. Surat Jalan:</span>
                <span class="info-value">{{ $suratJalan->no_surat_jalan }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal SJ:</span>
                <span class="info-value">{{ $suratJalan->tanggal_surat_jalan ? $suratJalan->tanggal_surat_jalan->format('d/m/Y') : '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Pengirim:</span>
                <span class="info-value">{{ $suratJalan->pengirim ?: '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tujuan Kirim:</span>
                <span class="info-value">{{ $suratJalan->tujuan_pengiriman ?: '-' }}</span>
            </div>
        </div>

        <div class="info-section">
            <h3>Transportasi</h3>
            <div class="info-row">
                <span class="info-label">No. Plat:</span>
                <span class="info-value">{{ $suratJalan->no_plat ?: '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nama Supir:</span>
                <span class="info-value">{{ $suratJalan->supir ?: '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kenek:</span>
                <span class="info-value">{{ $suratJalan->kenek ?: '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Keterangan:</span>
                <span class="info-value">{{ $suratJalan->status }}</span>
            </div>
        </div>
    </div>

    <table class="unit-table">
        <thead>
            <tr>
                <th style="width: 10%;">No.</th>
                <th style="width: 40%;">Jenis Barang</th>
                <th style="width: 25%;">Tipe Kontainer</th>
                <th style="width: 25%;">No. Kontainer / Seal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>{{ $suratJalan->jenis_barang ?: 'Barang Campuran' }}</td>
                <td>{{ $suratJalan->tipe_kontainer ?: '-' }}</td>
                <td>{{ $suratJalan->no_kontainer ?: '-' }} <br><small>Seal: {{ $suratJalan->no_seal ?: '-' }}</small></td>
            </tr>
        </tbody>
    </table>

    <div class="signatures">
        <div class="signature-box">
            <span>Penerima,</span>
            <span class="name">( ............................ )</span>
        </div>
        <div class="signature-box">
            <span>Supir,</span>
            <span class="name">( {{ $suratJalan->supir ?: '............................' }} )</span>
        </div>
        <div class="signature-box">
            <span>Hormat Kami,</span>
            <span class="name">( ............................ )</span>
        </div>
    </div>

    <div style="margin-top: 40px; font-size: 10px; color: #999; text-align: center;">
        Dicetak otomatis oleh AYPSIS System pada {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
