<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Rit - {{ $startDate->format('d/m/Y') }} s/d {{ $endDate->format('d/m/Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            color: #666;
        }
        .info {
            margin-bottom: 15px;
        }
        .info table {
            width: 100%;
        }
        .info td {
            padding: 3px 0;
        }
        .info td:first-child {
            width: 150px;
            font-weight: bold;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        table.data th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .signature {
            margin-top: 60px;
        }
        @media print {
            body {
                padding: 10px;
            }
            .no-print {
                display: none;
            }
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-muat {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-bongkar {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            <i class="fas fa-print"></i> Print
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    <div class="header">
        <h1>LAPORAN RIT</h1>
        <p>Periode: {{ $startDate->format('d/m/Y') }} s/d {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td>Total Surat Jalan</td>
                <td>: {{ $suratJalans->count() }}</td>
            </tr>
            <tr>
                <td>Total Muat</td>
                <td>: {{ $suratJalans->where('kegiatan', 'muat')->count() }}</td>
            </tr>
            <tr>
                <td>Total Bongkar</td>
                <td>: {{ $suratJalans->where('kegiatan', 'bongkar')->count() }}</td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ now()->format('d/m/Y H:i:s') }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">Tanggal</th>
                <th width="12%">No. Surat Jalan</th>
                <th width="7%">Kegiatan</th>
                <th width="12%">Supir</th>
                <th width="8%">No. Plat</th>
                <th width="15%">Pengirim</th>
                <th width="15%">Penerima</th>
                <th width="15%">Jenis Barang</th>
                <th width="5%">Jml</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suratJalans as $key => $sj)
            <tr>
                <td style="text-align: center;">{{ $key + 1 }}</td>
                <td style="text-align: center;">{{ $sj->tanggal ? $sj->tanggal->format('d/m/Y') : '-' }}</td>
                <td>{{ $sj->nomor_surat_jalan ?: '-' }}</td>
                <td style="text-align: center;">
                    <span class="badge {{ $sj->kegiatan == 'muat' ? 'badge-muat' : 'badge-bongkar' }}">
                        {{ strtoupper($sj->kegiatan) }}
                    </span>
                </td>
                <td>{{ $sj->nama_supir ?: '-' }}</td>
                <td>{{ $sj->no_plat ?: '-' }}</td>
                <td>{{ $sj->pengirim ?: '-' }}</td>
                <td>{{ $sj->penerima ?: '-' }}</td>
                <td>{{ $sj->jenis_barang ?: '-' }}</td>
                <td style="text-align: center;">{{ $sj->jumlah_kontainer ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align: center; padding: 20px;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>{{ now()->format('d F Y') }}</p>
        <div class="signature">
            <p>_____________________</p>
            <p>Penanggung Jawab</p>
        </div>
    </div>
</body>
</html>
