<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tanda Terima Asset - {{ $asset->kode_asset }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 24px;
        }

        .header {
            text-align: center;
            border-bottom: 2.5px solid #000;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }

        .company-name {
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .company-sub {
            font-size: 9pt;
            color: #333;
            margin: 3px 0 0 0;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .doc-title h2 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }

        .doc-title p {
            font-size: 9.5pt;
            color: #444;
            margin: 4px 0 0 0;
            font-family: monospace;
        }

        .statement {
            font-size: 10pt;
            margin-bottom: 14px;
            text-align: justify;
        }

        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            background-color: #f1f1f1;
            padding: 4px 8px;
            border: 1px solid #ccc;
            margin-top: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        table.detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-bottom: 12px;
        }

        table.detail-table td {
            padding: 5px 8px;
            vertical-align: top;
            border: 1px solid #ddd;
        }

        table.detail-table td.label-cell {
            width: 25%;
            font-weight: bold;
            background-color: #fafafa;
        }

        table.detail-table td.value-cell {
            width: 75%;
        }

        .notes-box {
            border: 1px solid #ddd;
            padding: 8px 10px;
            min-height: 40px;
            font-size: 9.5pt;
            margin-bottom: 14px;
            background-color: #fafafa;
        }

        .terms {
            font-size: 8.5pt;
            color: #333;
            border: 1px dashed #aaa;
            padding: 8px 12px;
            margin-bottom: 25px;
            background-color: #fdfdfd;
        }

        .terms ol {
            margin: 4px 0 0 16px;
            padding: 0;
        }

        .terms li {
            margin-bottom: 3px;
        }

        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-role {
            font-weight: bold;
            margin-bottom: 65px;
        }

        .signature-name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 140px;
            padding-bottom: 2px;
        }

        .signature-sub {
            font-size: 8.5pt;
            color: #555;
            margin-top: 3px;
        }

        .action-bar {
            position: fixed;
            top: 12px;
            right: 12px;
            background: white;
            padding: 8px 12px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            gap: 8px;
            z-index: 100;
        }

        .btn {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-print {
            background-color: #2563eb;
            color: white;
        }

        .btn-close {
            background-color: #e5e7eb;
            color: #374151;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }
            body {
                padding: 0;
            }
            .action-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="action-bar no-print">
        <button onclick="window.print()" class="btn btn-print">Cetak Dokumen</button>
        <button onclick="window.close()" class="btn btn-close">Tutup</button>
    </div>

    <!-- Kop Surat -->
    <div class="header">
        <h1 class="company-name">PT. ALEXINDO YAKINPRIMA</h1>
        <p class="company-sub">Shipping & Logistics Management System</p>
    </div>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h2>FORM TANDA TERIMA / BERITA ACARA SERAH TERIMA ASSET</h2>
        <p>No: TT-AST/{{ $asset->kode_asset }}/{{ $asset->tanggal_tanda_terima ? $asset->tanggal_tanda_terima->format('Ymd') : date('Ymd') }}</p>
    </div>

    <div class="statement">
        Pada hari ini, tanggal <strong>{{ $asset->tanggal_tanda_terima ? $asset->tanggal_tanda_terima->format('d F Y') : date('d F Y') }}</strong>, telah dilakukan serah terima asset inventaris perusahaan dengan rincian sebagai berikut:
    </div>

    <!-- Section 1: Informasi Asset -->
    <div class="section-title">A. Spesifikasi & Data Asset</div>
    <table class="detail-table">
        <tr>
            <td class="label-cell">Kode Asset</td>
            <td class="value-cell"><strong>{{ $asset->kode_asset }}</strong></td>
        </tr>
        <tr>
            <td class="label-cell">Nama Asset</td>
            <td class="value-cell"><strong>{{ $asset->nama_asset }}</strong></td>
        </tr>
        <tr>
            <td class="label-cell">Kategori Asset</td>
            <td class="value-cell">{{ $asset->kategori }}</td>
        </tr>
        <tr>
            <td class="label-cell">Kondisi Fisik</td>
            <td class="value-cell"><strong>{{ $asset->kondisi }}</strong></td>
        </tr>
        <tr>
            <td class="label-cell">Status Operasional</td>
            <td class="value-cell">{{ $asset->status }}</td>
        </tr>
    </table>

    <!-- Section 2: Pihak Pemegang / Penerima -->
    <div class="section-title">B. Data Penerima (Pemegang Asset)</div>
    <table class="detail-table">
        <tr>
            <td class="label-cell">Nama Lengkap</td>
            <td class="value-cell"><strong>{{ $asset->karyawan ? $asset->karyawan->nama_lengkap : 'Belum Ditentukan' }}</strong></td>
        </tr>
        <tr>
            <td class="label-cell">Nomor Induk Karyawan (NIK)</td>
            <td class="value-cell">{{ $asset->karyawan && $asset->karyawan->nik ? $asset->karyawan->nik : '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Divisi / Penempatan</td>
            <td class="value-cell">{{ $asset->karyawan && $asset->karyawan->divisi ? $asset->karyawan->divisi : '-' }} {{ $asset->karyawan && $asset->karyawan->cabang ? '(' . $asset->karyawan->cabang . ')' : '' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Jabatan / Posisi</td>
            <td class="value-cell">{{ $asset->karyawan && $asset->karyawan->posisi ? $asset->karyawan->posisi : '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tanggal Tanda Terima</td>
            <td class="value-cell"><strong>{{ $asset->tanggal_tanda_terima ? $asset->tanggal_tanda_terima->format('d F Y') : '-' }}</strong></td>
        </tr>
    </table>

    <!-- Section 3: Catatan & Kelengkapan -->
    <div class="section-title">C. Catatan & Kelengkapan Asset Saat Serah Terima</div>
    <div class="notes-box">
        {{ $asset->keterangan ?: 'Asset diserahterimakan dalam kondisi baik, berfungsi normal, dan lengkap untuk keperluan operasional perusahaan.' }}
    </div>

    <!-- Ketentuan -->
    <div class="terms">
        <strong>Ketentuan & Tanggung Jawab Penerima:</strong>
        <ol>
            <li>Penerima wajib memelihara, menjaga kebersihan, dan mempergunakan asset perusahaan untuk kepentingan kedinasan/pekerjaan.</li>
            <li>Kerusakan atau kehilangan yang diakibatkan oleh kelalaian penerima menjadi tanggung jawab karyawan sesuai regulasi perusahaan.</li>
            <li>Asset wajib dikembalikan dalam kondisi baik apabila penerima mutasi, selesai masa kontrak, mengundurkan diri, atau diminta sewaktu-waktu oleh perusahaan.</li>
        </ol>
    </div>

    <!-- Lembar Tanda Tangan -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-role">Yang Menyerahkan,</div>
                <div class="signature-name">( {{ Auth::user() ? Auth::user()->name : 'Bagian GA / Asset' }} )</div>
                <div class="signature-sub">GA / Pengelola Asset</div>
            </td>
            <td>
                <div class="signature-role">Yang Menerima,</div>
                <div class="signature-name">( {{ $asset->karyawan ? $asset->karyawan->nama_lengkap : '..................................' }} )</div>
                <div class="signature-sub">Karyawan Pemegang</div>
            </td>
            <td>
                <div class="signature-role">Mengetahui,</div>
                <div class="signature-name">( ........................................ )</div>
                <div class="signature-sub">Atasan / HRD Manager</div>
            </td>
        </tr>
    </table>

</body>
</html>
