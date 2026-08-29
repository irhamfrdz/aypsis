<!DOCTYPE html>
<html>
<head>
    <title>Data Absensi Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            margin-bottom: 10px;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            border: none;
            font-size: 14px;
            font-weight: bold;
        }
        .header .title {
            text-align: left;
        }
        .header .center {
            text-align: center;
            font-weight: normal;
            font-size: 12px;
        }
        .header .date {
            text-align: right;
            font-weight: normal;
            font-size: 12px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: auto;
        }
        table.data-table th, table.data-table td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
            word-wrap: break-word;
            font-size: 10px;
        }
        table.data-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        
        .footer {
            width: 100%;
            margin-top: 30px;
            font-size: 12px;
        }
        .footer table {
            width: 100%;
            border: none;
        }
        .footer td {
            border: none;
        }
        .footer .right {
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td class="title">Data Absensi Harian Karyawan</td>
                <td class="center">({{ $filterTitle }})</td>
                <td class="date">Dari {{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Posisi</th>
                <th>Divisi</th>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Ist. Keluar</th>
                <th>Ist. Masuk</th>
                <th>Pulang</th>
                <th>Lbr. Masuk</th>
                <th>Lbr. Pulang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensis as $index => $absensi)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $absensi->nik }}</td>
                    <td style="text-align: left;">{{ $absensi->karyawan ? $absensi->karyawan->nama_lengkap : 'Karyawan Tidak Terdaftar' }}</td>
                    <td>{{ $absensi->karyawan ? $absensi->karyawan->pekerjaan : '-' }}</td>
                    <td>{{ $absensi->karyawan && $absensi->karyawan->divisi !== '0' ? strtoupper($absensi->karyawan->divisi) : '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $absensi->waktu_masuk ? \Carbon\Carbon::parse($absensi->waktu_masuk)->format('H:i') : '-' }}</td>
                    <td>{{ $absensi->waktu_istirahat_keluar ? \Carbon\Carbon::parse($absensi->waktu_istirahat_keluar)->format('H:i') : '-' }}</td>
                    <td>{{ $absensi->waktu_istirahat_masuk ? \Carbon\Carbon::parse($absensi->waktu_istirahat_masuk)->format('H:i') : '-' }}</td>
                    <td>{{ $absensi->waktu_pulang ? \Carbon\Carbon::parse($absensi->waktu_pulang)->format('H:i') : '-' }}</td>
                    <td>{{ $absensi->waktu_lembur_masuk ? \Carbon\Carbon::parse($absensi->waktu_lembur_masuk)->format('H:i') : '-' }}</td>
                    <td>{{ $absensi->waktu_lembur_pulang ? \Carbon\Carbon::parse($absensi->waktu_lembur_pulang)->format('H:i') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td>Dicetak pada: {{ date('d/m/Y H:i') }}</td>
                <td class="right">Hal. <script type="text/php">if (isset($pdf)) { echo $PAGE_NUM . ' dari ' . $PAGE_COUNT; }</script></td>
            </tr>
        </table>
    </div>

</body>
</html>
