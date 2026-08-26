<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Form Permohonan Izin</title>
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
            width: 210mm;
            min-height: 148mm; /* Setengah A4 */
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
        .header-text {
            flex-grow: 1;
        }
        .header-text p {
            margin: 0;
            font-size: 14px;
            font-style: italic;
        }
        .header-text h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }
        /* Title */
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0 20px;
            text-transform: uppercase;
        }
        /* Form Content */
        .form-row {
            display: flex;
            margin-bottom: 8px;
            align-items: flex-start;
        }
        .form-label {
            width: 100px;
        }
        .form-separator {
            width: 20px;
            text-align: center;
        }
        .form-value {
            flex-grow: 1;
        }
        /* Checkboxes section */
        .checkbox-section {
            margin-top: 15px;
            margin-bottom: 20px;
            display: flex;
        }
        .checkbox-label {
            width: 280px;
        }
        .checkbox-grid {
            flex-grow: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            row-gap: 10px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
        }
        .checkbox-circle {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            margin-right: 10px;
            font-weight: bold;
        }
        .checkbox-circle.active {
            border: 1.5px solid #000;
        }
        
        .dotted-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            width: 100%;
            min-height: 20px;
        }

        /* Signatures */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            text-align: center;
        }
        .signature-box {
            width: 30%;
        }
        .signature-title {
            margin-bottom: 50px;
        }
        
        /* Note */
        .note {
            margin-top: 20px;
            font-size: 11px;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }
        
        @media print {
            body {
                background: none;
            }
            .page {
                margin: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="no-print" style="background: #fdf6b2; padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">🖨️ CETAK SEKARANG</button>
    </div>

    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="/images/logo.png" alt="Logo" style="width: 100%;" onerror="this.style.display='none'">
            </div>
            <div class="header-text">
                <p>Perusahaan Pelayaran Nasional</p>
                <h1>PT. ALEXINDO YAKINPRIMA</h1>
            </div>
        </div>

        <!-- Title -->
        <div class="title">
            FORM PERMOHONAN IZIN
        </div>

        <!-- Form Info -->
        <div class="form-row">
            <div class="form-label">Nama</div>
            <div class="form-separator">:</div>
            <div class="form-value">{{ $data->nama_lengkap ?? '' }}</div>
        </div>
        <div class="form-row">
            <div class="form-label">Divisi</div>
            <div class="form-separator">:</div>
            <div class="form-value">{{ $data->divisi ?? '' }}</div>
        </div>

        <?php 
            $jenis = strtolower($data->jenis_izin ?? ''); 
        ?>
        <div class="checkbox-section">
            <div class="checkbox-label">Dengan ini mengajukan Permohonan :</div>
            <div class="checkbox-grid">
                <div class="checkbox-item">
                    <span class="checkbox-circle {{ $jenis == 'tidak_masuk' || $jenis == 'tidak masuk' ? 'active' : '' }}">a.</span>
                    <span>Tidak Masuk</span>
                </div>
                <div class="checkbox-item">
                    <span class="checkbox-circle {{ $jenis == 'datang_terlambat' || $jenis == 'datang terlambat' ? 'active' : '' }}">b.</span>
                    <span>Datang Terlambat</span>
                </div>
                <div class="checkbox-item">
                    <span class="checkbox-circle {{ $jenis == 'pulang_cepat' || $jenis == 'pulang cepat' ? 'active' : '' }}">c.</span>
                    <span>Pulang Cepat</span>
                </div>
                <div class="checkbox-item">
                    <span class="checkbox-circle {{ $jenis == 'dinas_luar' || $jenis == 'dinas luar' ? 'active' : '' }}">d.</span>
                    <span>Dinas Luar</span>
                </div>
                <div class="checkbox-item">
                    <span class="checkbox-circle {{ $jenis == 'sakit    ' || $jenis == 'sakit' ? 'active' : '' }}">e.</span>
                    <span>Sakit</span>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-label">Tanggal</div>
            <div class="form-separator">:</div>
            <div class="form-value">
                <span class="dotted-line">
                    {{ \Carbon\Carbon::parse($data->tanggal_mulai)->format('d F Y') }} 
                    s.d 
                    {{ \Carbon\Carbon::parse($data->tanggal_selesai)->format('d F Y') }}
                </span>
            </div>
        </div>
        <div class="form-row">
            <div class="form-label">Waktu</div>
            <div class="form-separator">:</div>
            <div class="form-value">
                <span class="dotted-line">{{ $data->waktu ?? '......................................................' }}</span>
            </div>
        </div>
        <div class="form-row">
            <div class="form-label">Alasan</div>
            <div class="form-separator">:</div>
            <div class="form-value">
                <span class="dotted-line">{{ $data->alasan ?? '......................................................' }}</span>
                <span class="dotted-line mt-2"></span>
            </div>
        </div>

        <div style="margin-top: 40px; margin-bottom: 20px;">
            Jakarta, {{ \Carbon\Carbon::parse($data->created_at)->format('d F Y') }}
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Pemohon</div>
                <div>( {{ $data->nama_lengkap ? str_pad($data->nama_lengkap, 30, '.', STR_PAD_BOTH) : '....................................' }} )</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Atasan Yang Bersangkutan</div>
                <div>( {{ $data->nama_supervisor ? str_pad($data->nama_supervisor, 30, '.', STR_PAD_BOTH) : '....................................' }} )</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">HRD</div>
                <div>( .................................... )</div>
            </div>
        </div>

        <!-- Note -->
        <div class="note">
            Note:<br>
            Setelah form diisi dan ditanda tangani, serahkan form ke HRD
        </div>
    </div>
</body>
</html>
