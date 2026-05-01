<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRINT TANDA TERIMA - {{ $item->no_tanda_terima }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 10mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            color: #000;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .title {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .no-tt {
            font-size: 12pt;
            font-weight: bold;
        }
        .content {
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .label {
            width: 180px;
            font-weight: bold;
        }
        .separator {
            width: 20px;
        }
        .value {
            flex-grow: 1;
            border-bottom: 1px dotted #000;
        }
        .container-box {
            border: 1px solid #000;
            padding: 15px;
            margin: 20px 0;
            background: #f9f9f9;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            font-weight: bold;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(0,0,0,0.05);
            z-index: -1;
            pointer-events: none;
            white-space: nowrap;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="watermark">TARIK KOSONG</div>

    <div class="no-print" style="background: #fdf6b2; padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">🖨️ CETAK SEKARANG</button>
    </div>

    <div class="header">
        <div class="title">Tanda Terima Penarikan Kosong</div>
        <div class="no-tt">{{ $item->no_tanda_terima }}</div>
    </div>

    <div class="content">
        <div class="info-row">
            <div class="label">Tanggal</div>
            <div class="separator">:</div>
            <div class="value">{{ $item->tanggal_tanda_terima->format('d F Y') }}</div>
        </div>
        <div class="info-row">
            <div class="label">No. Surat Jalan</div>
            <div class="separator">:</div>
            <div class="value">{{ $item->no_surat_jalan }}</div>
        </div>
        <div class="info-row">
            <div class="label">Penerima</div>
            <div class="separator">:</div>
            <div class="value">{{ $item->penerima ?: '..........................................................' }}</div>
        </div>

        <div class="container-box">
            <div style="font-size: 10pt; font-weight: bold; margin-bottom: 10px; color: #666; text-transform: uppercase;">Detail Kendaraan & Kontainer</div>
            <div style="display: flex; gap: 40px;">
                <div>
                    <div style="font-size: 9pt; color: #666;">SUPIR / PLAT</div>
                    <div style="font-size: 12pt; font-weight: bold;">{{ $item->supir }} / {{ $item->no_plat }}</div>
                </div>
                <div>
                    <div style="font-size: 9pt; color: #666;">NOMOR KONTAINER</div>
                    <div style="font-size: 14pt; font-weight: bold;">{{ $item->no_kontainer }}</div>
                </div>
                <div>
                    <div style="font-size: 9pt; color: #666;">SIZE</div>
                    <div style="font-size: 12pt; font-weight: bold;">{{ $item->size }}</div>
                </div>
            </div>
        </div>

        <div class="info-row" style="margin-top: 20px;">
            <div class="label">Catatan</div>
            <div class="separator">:</div>
            <div class="value">{{ $item->catatan ?: '-' }}</div>
        </div>
    </div>

    <div class="footer">
        <div class="signature-box">
            <div>Diterima Oleh,</div>
            <div class="signature-line">( {{ $item->penerima ?: '...........................' }} )</div>
        </div>
        <div class="signature-box">
            <div>Supir,</div>
            <div class="signature-line">( {{ $item->supir }} )</div>
        </div>
        <div class="signature-box">
            <div>Petugas Lapangan,</div>
            <div class="signature-line">( ........................... )</div>
        </div>
    </div>

    <script>
        // Auto print after load if needed
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
