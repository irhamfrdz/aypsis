<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Surat Jalan - {{ $item->no_surat_jalan }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 10mm;
            font-size: 10pt;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 14pt;
            font-weight: bold;
        }
        .document-title {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-group {
            margin-bottom: 8px;
        }
        .label {
            display: inline-block;
            width: 100px;
            font-weight: normal;
        }
        .value {
            font-weight: bold;
        }
        .container-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 10px 0;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
        .footer {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            text-align: center;
        }
        .signature-box {
            height: 50px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60pt;
            color: rgba(200, 200, 200, 0.2);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="watermark">TARIK KOSONG</div>
    
    <div class="header">
        <div>
            <div class="company-name">PT. AYP LOGISTICS</div>
            <div>Batam, Indonesia</div>
        </div>
        <div style="text-align: right;">
            <div class="document-title">SURAT JALAN TARIK KOSONG</div>
            <div class="value">No: {{ $item->no_surat_jalan }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div>
            <div class="info-group">
                <span class="label">Tanggal:</span>
                <span class="value">{{ $item->tanggal_surat_jalan->format('d/m/Y') }}</span>
            </div>
            <div class="info-group">
                <span class="label">Pengirim:</span>
                <span class="value">{{ $item->pengirim ?? '-' }}</span>
            </div>
            <div class="info-group">
                <span class="label">Penerima:</span>
                <span class="value">{{ $item->penerima ?? '-' }}</span>
            </div>
            <div class="info-group">
                <span class="label">Alamat:</span>
                <span class="value">{{ $item->alamat ?? '-' }}</span>
            </div>
        </div>
        <div>
            <div class="info-group">
                <span class="label">No. Plat:</span>
                <span class="value">{{ $item->no_plat ?? '-' }}</span>
            </div>
            <div class="info-group">
                <span class="label">Supir:</span>
                <span class="value">{{ $item->supir ?? '-' }}</span>
            </div>
            <div class="info-group">
                <span class="label">Tujuan Ambil:</span>
                <span class="value">{{ $item->tujuan_pengambilan ?? '-' }}</span>
            </div>
            <div class="info-group">
                <span class="label">Tujuan Kirim:</span>
                <span class="value">{{ $item->tujuan_pengiriman ?? '-' }}</span>
            </div>
        </div>
    </div>

    <div class="container-box">
        <div>
            <span class="label">No. Kontainer:</span>
            <span class="value">{{ $item->no_kontainer ?? '-' }}</span>
        </div>
        <div>
            <span class="label">Size / Type:</span>
            <span class="value">{{ $item->size ?? '-' }} FT / {{ $item->tipe_kontainer ?? '-' }}</span>
        </div>
        <div>
            <span class="label">Status:</span>
            <span class="value">{{ $item->f_e == 'E' ? 'EMPTY (E)' : 'FULL (F)' }}</span>
        </div>
    </div>

    <div class="info-group">
        <span class="label">Catatan:</span>
        <span class="value">{{ $item->catatan ?? '-' }}</span>
    </div>

    <div class="footer">
        <div>
            <div>Pengirim</div>
            <div class="signature-box"></div>
            <div>( .................... )</div>
        </div>
        <div>
            <div>Supir</div>
            <div class="signature-box"></div>
            <div>( {{ $item->supir ?? '....................' }} )</div>
        </div>
        <div>
            <div>Penerima</div>
            <div class="signature-box"></div>
            <div>( .................... )</div>
        </div>
        <div>
            <div>Administrasi</div>
            <div class="signature-box"></div>
            <div>( {{ Auth::user()->name }} )</div>
        </div>
    </div>
</body>
</html>
