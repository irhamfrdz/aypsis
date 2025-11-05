<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Master Mobil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .info {
            margin-bottom: 15px;
            font-size: 9px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            font-size: 8px;
            word-wrap: break-word;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DAFTAR MASTER MOBIL</h1>
    </div>

    <div class="info">
        <div class="info-row">
            <span><strong>Total Data:</strong> {{ $total }} mobil</span>
            <span><strong>Tanggal Export:</strong> {{ $exported_at }}</span>
        </div>
        <div class="info-row">
            @if($search)
                <span><strong>Filter Pencarian:</strong> "{{ $search }}"</span>
            @else
                <span><strong>Filter:</strong> Semua Data</span>
            @endif
            <span><strong>Diekspor oleh:</strong> {{ $exported_by }}</span>
        </div>
    </div>

    @if($mobils->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 8%;">Kode Aktiva</th>
                    <th style="width: 8%;">No. Polisi</th>
                    <th style="width: 10%;">Karyawan</th>
                    <th style="width: 6%;">Lokasi</th>
                    <th style="width: 8%;">Merek</th>
                    <th style="width: 8%;">Jenis</th>
                    <th style="width: 5%;">Tahun</th>
                    <th style="width: 7%;">BPKB</th>
                    <th style="width: 8%;">No. Mesin</th>
                    <th style="width: 8%;">No. Rangka</th>
                    <th style="width: 6%;">Pajak STNK</th>
                    <th style="width: 6%;">No. KIR</th>
                    <th style="width: 9%;">Atas Nama</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mobils as $index => $mobil)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $mobil->kode_no ?? '-' }}</td>
                        <td>{{ $mobil->nomor_polisi ?? '-' }}</td>
                        <td>
                            @if($mobil->karyawan)
                                {{ $mobil->karyawan->nama_lengkap }}
                                @if($mobil->karyawan->nik)
                                    <br><small>({{ $mobil->karyawan->nik }})</small>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $mobil->lokasi ?? '-' }}</td>
                        <td>{{ $mobil->merek ?? '-' }}</td>
                        <td>{{ $mobil->jenis ?? '-' }}</td>
                        <td class="text-center">{{ $mobil->tahun_pembuatan ?? '-' }}</td>
                        <td>{{ $mobil->bpkb ?? '-' }}</td>
                        <td>{{ $mobil->no_mesin ?? '-' }}</td>
                        <td>{{ $mobil->nomor_rangka ?? '-' }}</td>
                        <td class="text-center">
                            {{ $mobil->pajak_stnk ? date('d/m/Y', strtotime($mobil->pajak_stnk)) : '-' }}
                        </td>
                        <td>{{ $mobil->no_kir ?? '-' }}</td>
                        <td>{{ $mobil->atas_nama ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Tidak ada data mobil yang ditemukan.</p>
            @if($search)
                <p>Coba ubah kata kunci pencarian: "{{ $search }}"</p>
            @endif
        </div>
    @endif

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis dari sistem pada {{ $exported_at }}</p>
        <p>Total {{ $total }} data mobil {{ $search ? 'dengan filter pencarian' : '' }}</p>
    </div>
</body>
</html>