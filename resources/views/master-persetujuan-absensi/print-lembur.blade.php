<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perintah Lembur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .page {
            width: 176mm; /* B5 Portrait */
            min-height: 148mm;
            padding: 10mm;
            margin: 0 auto;
            box-sizing: border-box;
            border: 2px solid #000;
            position: relative;
        }
        .page::before {
            content: '';
            position: absolute;
            top: 4px;
            left: 4px;
            right: 4px;
            bottom: 4px;
            border: 1px solid #000;
            pointer-events: none;
        }
        /* Header */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: auto;
            margin-right: 20px;
        }
        .title-container {
            flex-grow: 1;
            text-align: center;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .nomor {
            position: absolute;
            top: 15px;
            right: 25px;
            font-size: 16px;
            font-weight: bold;
            color: #d32f2f;
            border: 1px solid #d32f2f;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-15deg);
        }
        /* Content */
        .content {
            padding: 0 10px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .form-row {
            display: flex;
            margin-bottom: 8px;
            align-items: flex-start;
        }
        .form-label {
            width: 150px;
        }
        .form-label-short {
            width: 100px;
            padding-left: 20px;
        }
        .form-separator {
            width: 15px;
            text-align: center;
        }
        .form-value {
            flex-grow: 1;
            border-bottom: 1px dotted #000;
            min-height: 20px;
        }
        .dotted-line {
            flex-grow: 1;
            border-bottom: 1px dotted #000;
            margin-left: 5px;
            display: inline-block;
        }
        
        ol {
            margin: 5px 0 10px 0;
            padding-left: 35px;
        }
        li {
            margin-bottom: 5px;
        }
        li .form-value {
            display: inline-block;
            width: 90%;
        }

        /* Signatures */
        .signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            position: relative;
        }
        .signature-box .date-text {
            text-align: left;
            margin-bottom: 10px;
            padding-left: 30px;
        }
        .signature-space {
            height: 80px;
            position: relative;
        }
        .signature-image {
            max-height: 70px;
            max-width: 100%;
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
        }
        .signature-name {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 150px;
            padding: 0 10px;
            margin-bottom: 5px;
        }
        /* Footer */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 11px;
            line-height: 1.4;
        }
        .footer p {
            margin: 0;
        }
        @page {
            size: B5 portrait;
            margin: 0;
        }
        
        @media print {
            body { background: none; }
            .page {
                margin: 0;
                border: 2px solid #000;
                box-shadow: none;
            }
            .no-print {
                margin: 0;
                border: none;
                box-shadow: none;
                page-break-after: always;
            }
            .page::before {
                display: none;
            }
        }
    </style>
</head>
<body>
    @php
        // Format dates
        $tanggalObj = \Carbon\Carbon::parse($data->tanggal);
        $hariTanggal = $tanggalObj->isoFormat('dddd, D MMMM Y');
        
        // Extract hour/minute from time if they exist
        $mulaiDari = $data->jam_mulai ? \Carbon\Carbon::parse($data->jam_mulai)->format('H:i') . ' WIB' : '...... WIB';
        $sampaiDengan = $data->jam_selesai ? \Carbon\Carbon::parse($data->jam_selesai)->format('H:i') . ' WIB' : '...... WIB';
        
        $keteranganLines = [];
        if (!empty($data->keterangan)) {
            $lines = explode("\n", $data->keterangan);
            foreach ($lines as $line) {
                if (trim($line) !== '') {
                    $keteranganLines[] = trim($line);
                }
            }
        }
        // Ensure at least 2 lines for the form
        while (count($keteranganLines) < 2) {
            $keteranganLines[] = '';
        }
        
        $submitDate = \Carbon\Carbon::parse($data->created_at)->isoFormat('D MMMM Y');
    @endphp

    <div class="page">
        <!-- Optional Nomor Badge (Simulating red pen from image) -->
        <!-- <div class="nomor">{{ $data->id }}</div> -->

        <div class="header">
            <img src="{{ asset('img/ayp-logo.png') }}" alt="Logo AYP" class="logo" onerror="this.style.display='none'">
            <div class="title-container">
                <h1 class="title">SURAT PERINTAH LEMBUR</h1>
            </div>
        </div>

        <div class="content">
            <div class="form-group" style="display: flex;">
                <span>Pimpinan unit Divisi</span>
                <span class="dotted-line" style="flex-grow: 0; width: 150px; margin-right: 5px;">
                    {{ $data->divisi ?? '..................' }}
                </span>
                <span>memberi perintah kepada :</span>
            </div>

            <div class="form-group" style="margin-top: 15px;">
                <div class="form-row">
                    <div class="form-label">Nama</div>
                    <div class="form-separator">:</div>
                    <div class="form-value" style="font-weight: bold;">{{ $data->nama_lengkap ?? '....................................' }}</div>
                </div>
                <div class="form-row">
                    <div class="form-label">NPP</div>
                    <div class="form-separator">:</div>
                    <div class="form-value">{{ $data->nik ?? '....................................' }}</div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <p style="margin: 0 0 10px 0;">Untuk melaksanakan tugas di luar jam kerja rutin pada :</p>
                
                <div class="form-row">
                    <div class="form-label-short">Hari, Tanggal</div>
                    <div class="form-separator">:</div>
                    <div class="form-value">{{ $hariTanggal }}</div>
                </div>
                <div class="form-row">
                    <div class="form-label-short">Mulai dari</div>
                    <div class="form-separator">:</div>
                    <div class="form-value">{{ $mulaiDari }}</div>
                </div>
                <div class="form-row">
                    <div class="form-label-short">Sampai dengan</div>
                    <div class="form-separator">:</div>
                    <div class="form-value">{{ $sampaiDengan }}</div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <p style="margin: 0;">Hal-hal yang dikerjakan :</p>
                <ol>
                    @foreach(array_slice($keteranganLines, 0, 2) as $index => $line)
                    <li>
                        <div class="form-value">{{ $line }}</div>
                    </li>
                    @endforeach
                </ol>
            </div>

            <div class="form-group">
                <p style="margin: 0;">Alasan memberi perintah lembur :</p>
                <ol>
                    <li><div class="form-value"></div></li>
                    <li><div class="form-value"></div></li>
                </ol>
            </div>
            
            <p style="margin-top: 20px; font-weight: bold;">Harap dilaksanakan dengan penuh tanggung jawab.</p>

            <div class="signatures">
                <div class="signature-box" style="margin-top: 25px;">
                    <div class="signature-space">
                        <!-- Tanda tangan karyawan bisa ditambahkan di sini jika ada -->
                    </div>
                    <div class="signature-name">{{ $data->nama_lengkap ?? '....................................' }}</div>
                    <div>Yang menerima tugas</div>
                </div>
                <div class="signature-box">
                    <div class="date-text">Jakarta, {{ $submitDate }}</div>
                    <div class="signature-space">
                        <!-- Tanda tangan supervisor/atasan -->
                    </div>
                    <div class="signature-name">{{ $data->nama_supervisor ?? '....................................' }}</div>
                    <div>Yang memberi tugas</div>
                </div>
            </div>

            <div class="footer">
                <p style="margin-bottom: 2px;">Catatan :</p>
                <p>- Harap Cantumkan nomor NIK dan Nama lengkap agar pencatatan berjalan baik.</p>
                <p>- Ketentuan pembayaran lembur berdasarkan Data Finger Scan yang tercatat di Mesin Absen.</p>
                <p>- Pemberi tugas lembur wajib untuk mengisi/penjelasan pekerjaan lembur yang dilakukan.</p>
            </div>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
            // Optional: Close window after print dialog is closed
            // setTimeout(function() { window.close(); }, 500);
        };
    </script>
</body>
</html>
