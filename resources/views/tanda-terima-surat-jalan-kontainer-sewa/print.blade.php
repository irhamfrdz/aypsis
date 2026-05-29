<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Tanda Terima Kontainer Sewa - {{ $tandaTerima->nomor_tanda_terima }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .title {
            font-size: 16pt;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .label {
            width: 180px;
            font-weight: bold;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            width: 30%;
            float: left;
            text-align: center;
            margin-right: 3%;
        }
        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        @media print {
            @page {
                size: A4;
                margin: 2cm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2 style="margin:0">PT. AYPSIS</h2>
        <p style="margin:5px 0">Jasa Transportasi & Logistik</p>
    </div>

    <div style="text-align: center; margin-bottom: 30px;">
        <h3 class="title">TANDA TERIMA KONTAINER SEWA</h3>
        <p>No: {{ $tandaTerima->nomor_tanda_terima }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Tanggal Terima</td>
            <td>: {{ \Carbon\Carbon::parse($tandaTerima->tanggal_tanda_terima)->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">No. Surat Jalan</td>
            <td>: {{ $tandaTerima->nomor_surat_jalan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kegiatan</td>
            <td>: {{ strtoupper($tandaTerima->kegiatan) }}</td>
        </tr>
        <tr>
            <td class="label">No. Kontainer</td>
            <td>: {{ $tandaTerima->nomor_kontainer ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tipe / Ukuran</td>
            <td>: {{ $tandaTerima->tipe_kontainer ?? '-' }} / {{ $tandaTerima->ukuran ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Mulai Sewa</td>
            <td>: {{ $tandaTerima->tanggal_mulai_sewa ? \Carbon\Carbon::parse($tandaTerima->tanggal_mulai_sewa)->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Supir</td>
            <td>: {{ $tandaTerima->supir ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No. Plat Kendaraan</td>
            <td>: {{ $tandaTerima->no_plat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Keterangan</td>
            <td>: {{ $tandaTerima->keterangan ?? '-' }}</td>
        </tr>
    </table>

    <div class="signatures">
        <div class="signature-box">
            <p>Diserahkan Oleh,</p>
            <div class="signature-line">( Supir )</div>
        </div>
        <div class="signature-box">
            <p>Diterima Oleh,</p>
            <div class="signature-line">( Penerima )</div>
        </div>
        <div class="signature-box">
            <p>Mengetahui,</p>
            <div class="signature-line">( Admin )</div>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
