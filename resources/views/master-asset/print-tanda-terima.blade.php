<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pernyataan Serah Terima dan Pengembalian Aset - {{ $asset->kode_asset }}</title>
    <style>
        @page { size: letter portrait; margin: 25.4mm; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; color: #000; background: #eee; font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.08; }
        .document { width: 215.9mm; max-width: 100%; margin: 0 auto; padding: 25.4mm; background: #fff; }
        .letterhead { display: block; width: 100%; height: auto; margin: 0 0 8pt; }
        h1 { margin: 0 0 20pt; text-align: center; font-size: 12pt; line-height: 1.08; text-decoration: underline; }
        p { margin: 0 0 8pt; }
        .identity { margin: 0 0 8pt; padding-left: 36pt; }
        .identity li { padding-left: 0; margin: 0; overflow-wrap: anywhere; }
        .assets { width: 100%; border-collapse: collapse; margin: 16pt 0; table-layout: fixed; font-size: 12pt; }
        .assets th, .assets td { border: 0.5pt solid #000; padding: 0 5.4pt; vertical-align: top; overflow-wrap: anywhere; line-height: 1.08; }
        .assets th { text-align: center; }
        .assets td { height: 20pt; }
        .assets .number { width: 6.1%; }
        .assets th.number { white-space: nowrap; }
        .assets .name { width: 22.7%; }
        .assets .model { width: 31.2%; }
        .assets .serial { width: 20%; }
        .assets .condition { width: 20%; }
        .terms { margin: 0 0 16pt; padding-left: 36pt; text-align: justify; }
        .terms > li { margin-bottom: 8pt; break-inside: avoid; page-break-inside: avoid; }
        .terms ul { margin: 8pt 0 0; padding-left: 18pt; list-style-type: disc; }
        .terms ul li { margin-bottom: 8pt; }
        .closing { text-align: justify; }
        .signature { margin-top: 8pt; break-inside: avoid; page-break-inside: avoid; }
        .signature p { margin: 0 0 8pt; }
        .signature-space { height: 36pt; }
        .signature-name { overflow-wrap: anywhere; }
        .action-bar { position: fixed; top: 12px; right: 12px; display: flex; gap: 8px; padding: 8px; background: #fff; box-shadow: 0 2px 10px #0002; border-radius: 6px; }
        .action-bar button, .action-bar a { padding: 8px 12px; border: 0; border-radius: 4px; cursor: pointer; background: #e5e7eb; font: 13px Arial, sans-serif; text-decoration: none; }
        .action-bar .print { background: #2563eb; color: #fff; }
        .action-bar .download { background: #047857; color: #fff; }
        @media print {
            body { padding: 0; background: #fff; }
            .document { width: auto; max-width: none; padding: 0; }
            .action-bar { display: none !important; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; page-break-inside: avoid; }
        }
    </style>
</head>
<body @unless($isPdf ?? false) onload="window.print()" @endunless>
    @php
        $karyawan = $asset->karyawan;
        $namaPemegang = $karyawan?->nama_lengkap ?: '........................................';
        $tanggalSurat = $asset->tanggal_tanda_terima ?? now();
        $kota = in_array($asset->lokasi, ['Jakarta', 'Batam'], true)
            ? $asset->lokasi
            : ($karyawan?->cabang ?: '........................');
    @endphp

    @unless($isPdf ?? false)
    <div class="action-bar">
        <button type="button" class="print" onclick="window.print()">Cetak Dokumen</button>
        <a class="download" href="{{ route('asset.download-tanda-terima', $asset) }}">Download PDF</a>
        <button type="button" onclick="window.close()">Tutup</button>
    </div>
    @endunless

    <main class="document">
        <img class="letterhead" src="{{ ($isPdf ?? false) ? public_path('images/asset-handover-letterhead.png') : asset('images/asset-handover-letterhead.png') }}" alt="Kop surat PT. Alexindo Yakinprima">
        <h1>SURAT PERNYATAAN SERAH TERIMA DAN PENGEMBALIAN ASET</h1>

        <p>Yang bertanda tangan di bawah ini:</p>
        <ul class="identity">
            <li>Nama Lengkap : {{ $namaPemegang }}</li>
            <li>NIK / NIP : {{ $karyawan?->nik ?: '........................................' }}</li>
            <li>Departemen : {{ $karyawan?->departemen ?: ($karyawan?->divisi ?: '........................................') }}</li>
        </ul>

        <p>Dengan ini menyatakan bahwa saya telah menerima aset milik <strong>PT. ALEXINDO YAKINPRIMA</strong> berupa fasilitas kerja dengan rincian sebagai berikut:</p>

        <table class="assets">
            <thead>
                <tr>
                    <th class="number">No.</th>
                    <th class="name">Nama/Jenis Aset</th>
                    <th class="model">Merk &amp; Tipe</th>
                    <th class="serial">Nomor Serial / Pelat Nomor</th>
                    <th class="condition">Kondisi Aset</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="number">1.</td>
                    <td>{{ $asset->nama_asset }}</td>
                    <td>
                        <div>Merk: {{ $asset->nama_merk ?: '........................' }}</div>
                        <div>Tipe: {{ $asset->kategori ?: '........................' }}</div>
                    </td>
                    <td>{{ $asset->nomor_seri ?: '........................' }}</td>
                    <td>{{ $asset->kondisi ?: '........................' }}</td>
                </tr>
            </tbody>
        </table>

        <p>Sehubungan dengan penerimaan aset tersebut, saya menyatakan tunduk dan menyetujui ketentuan di bawah ini:</p>
        <ol class="terms">
            <li><strong>Tanggung Jawab Pemeliharaan:</strong> Saya akan menjaga, merawat, dan mempergunakan aset tersebut hanya untuk kepentingan operasional dan kelancaran pekerjaan perusahaan.</li>
            <li><strong>Pengembalian Aset:</strong> Saya bersedia dan wajib mengembalikan seluruh aset tersebut dalam kondisi baik dan lengkap kepada perusahaan apabila:
                <ul>
                    <li>Hubungan kerja saya berakhir (baik karena mengundurkan diri, PHK, habis masa kontrak, atau pensiun), paling lambat pada hari terakhir saya bekerja (last day).</li>
                    <li>Diminta kembali sewaktu-waktu oleh manajemen perusahaan untuk kebutuhan rotasi, audit, pembaruan (upgrade), atau alasan operasional lainnya.</li>
                </ul>
            </li>
            <li><strong>Sanksi dan Ganti Rugi:</strong> Apabila terjadi kerusakan yang disebabkan oleh kelalaian (bukan karena aus pemakaian wajar) atau kehilangan selama aset berada di bawah pengawasan saya, saya bersedia bertanggung jawab mengganti biaya perbaikan atau penggantian aset sesuai dengan kebijakan perusahaan.</li>
            <li><strong>Tindakan Hukum:</strong> Jika saya lalai atau sengaja tidak mengembalikan aset tersebut setelah hubungan kerja berakhir atau setelah diminta kembali, perusahaan berhak melakukan tindakan hukum dan menahan hak-hak finansial saya (seperti gaji terakhir atau pesangon) sebagai jaminan.</li>
        </ol>

        <p class="closing">Demikian surat pernyataan ini saya buat dengan sadar, sukarela, dan tanpa paksaan dari pihak mana pun untuk dipergunakan sebagaimana mestinya.</p>

        <div class="signature">
            <p>{{ $kota }}, {{ $tanggalSurat->copy()->locale('id')->translatedFormat('d F Y') }}</p>
            <p>Yang Menyatakan,</p>
            <p>Materai 10.000</p>
            <div class="signature-space" aria-label="Ruang tanda tangan"></div>
            <p>(Tanda Tangan)</p>
            <p class="signature-name">({{ $namaPemegang }})</p>
        </div>
    </main>
</body>
</html>
