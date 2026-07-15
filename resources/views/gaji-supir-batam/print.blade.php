<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Gaji Supir Batam - {{ $gaji->karyawan->nama_lengkap }}</title>
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
            text-align: center;
        }
        .sub-title {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .label {
            width: 150px;
        }
        .rincian-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .rincian-table th, .rincian-table td {
            border: 1px solid #000;
            padding: 5px 8px;
        }
        .rincian-table th {
            background-color: #f0f0f0;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .signatures {
            margin-top: 40px;
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

    <div class="title">SLIP GAJI SUPIR BATAM</div>
    <div class="sub-title">Periode: {{ $gaji->periode_text }}</div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Supir</td>
            <td style="width: 250px;">: {{ $gaji->karyawan->nama_lengkap }}</td>
            <td class="label">No. Plat</td>
            <td>: {{ $gaji->karyawan->plat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td>: {{ $gaji->karyawan->nik ?? '-' }}</td>
            <td class="label">Status Bayar</td>
            <td>: {{ $gaji->status_pembayaran === 'PAID' ? 'SUDAH DIBAYAR' : ($gaji->status_pembayaran === 'PENDING' ? 'PENDING' : 'BATAL') }}</td>
        </tr>
    </table>

    <div style="font-weight: bold; margin-bottom: 5px;">Rincian Surat Jalan :</div>
    <table class="rincian-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">No.</th>
                <th>Tipe</th>
                <th>No. Surat Jalan</th><th>No. Kontainer</th><th>Tujuan Pengiriman</th><th>Ring</th>
                <th>Tanggal</th>
                <th class="text-right">Uang Jalan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($waybills as $index => $wb)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $wb['type'] }}</td>
                    <td>{{ $wb['no_surat_jalan'] }}</td><td>{{ $wb['no_kontainer'] }}</td><td>{{ $wb['tujuan'] }}</td><td class="text-center">{{ $wb['ring'] }}</td>
                    <td>{{ $wb['tanggal'] }}</td>
                    <td class="text-right">Rp {{ number_format($wb['rit'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada surat jalan yang ditemukan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="font-weight: bold; margin-bottom: 5px;">Rincian Gaji :</div>
    <table class="rincian-table">
        <tr>
            <td>Total Gaji Pokok (Berdasarkan Surat Jalan)</td>
            <td class="text-right font-bold" style="width: 200px;">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</td>
        </tr>
        @if($gaji->uang_malam_libur > 0)
        <tr>
            <td>Uang Berangkat Malam/Libur</td>
            <td class="text-right font-bold">Rp {{ number_format($gaji->uang_malam_libur, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr>
            <td>Potongan Biaya Bensin</td>
            <td class="text-right font-bold">Rp {{ number_format($gaji->biaya_bensin ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="font-bold">Total Gaji Bersih</td>
            <td class="text-right font-bold">Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($gaji->keterangan)
        <div style="margin-top: 10px; margin-bottom: 20px;">
            <strong>Catatan:</strong> {{ $gaji->keterangan }}
        </div>
    @endif

    <div class="signatures" style="margin-top: 30px;">
        <div class="signature-box" style="width: 40%;">
            <p>Penerima (Supir),</p>
            <div class="signature-line">{{ $gaji->karyawan->nama_lengkap }}</div>
        </div>
        <div class="signature-box" style="width: 40%; float: right;">
            <p>Batam, {{ $gaji->tanggal_dibayar ? $gaji->tanggal_dibayar->format('d F Y') : now()->format('d F Y') }}</p>
            <div class="signature-line">Manajemen Operasional</div>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
