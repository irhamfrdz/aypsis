<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Form Permohonan Cuti</title>
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
            width: 297mm; /* Landscape A4 */
            min-height: 170mm;
            height: auto;
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
            margin-bottom: 10px;
            position: relative;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 0;
            right: 0;
            border-bottom: 1px solid #000;
        }
        .logo {
            width: 70px;
            height: auto;
            margin-right: 15px;
        }
        .company-info {
            flex-grow: 1;
            padding-right: 20px;
            border-right: 2px solid #000;
        }
        .company-info p {
            margin: 0;
            font-size: 13px;
            font-style: italic;
        }
        .company-info h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
        }
        .form-title {
            width: 350px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            padding-left: 20px;
        }
        /* Instructions */
        .instructions {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #000;
        }
        .instructions p {
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .instructions ol {
            margin: 0;
            padding-left: 25px;
            font-size: 13px;
        }
        .instructions li {
            margin-bottom: 5px;
        }
        /* Columns */
        .content-columns {
            display: flex;
            position: relative;
            min-height: 150px;
        }
        .hr-col {
            width: 40%;
            padding-right: 20px;
            border-right: 2px solid #000;
        }
        .user-col {
            width: 60%;
            padding-left: 20px;
        }
        
        /* Form rows */
        .form-row {
            display: flex;
            margin-bottom: 15px;
            align-items: flex-end;
        }
        .form-label {
            width: 150px;
        }
        .form-separator {
            width: 20px;
            text-align: center;
        }
        .form-value {
            flex-grow: 1;
            border-bottom: 1px dotted #000;
            min-height: 20px;
        }
        
        .user-col .form-label {
            width: 100px;
        }

        /* Checkbox Box */
        .checkbox-box {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 1px solid #000;
            vertical-align: middle;
            margin-right: 5px;
            text-align: center;
            line-height: 15px;
            font-weight: bold;
            font-size: 14px;
        }
        .checkbox-box.active {
            content: '✓';
        }
        .checkbox-list {
            margin-top: 10px;
            margin-left: 120px;
        }
        .checkbox-item {
            margin-bottom: 8px;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            text-align: center;
        }
        .signature-box {
            width: 30%;
        }
        .signature-title {
            margin-bottom: 70px;
        }
        
        @page {
            size: A4 landscape;
            margin: 0;
        }

        @media print {
            body { background: none; }
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
            <div class="company-info">
                <p>Perusahaan Pelayaran Nasional</p>
                <h1>PT. ALEXINDO YAKINPRIMA</h1>
            </div>
            <div class="form-title">
                FORM PERMOHONAN CUTI KARYAWAN
            </div>
        </div>

        <!-- Instructions -->
        <div class="instructions">
            <p>Petunjuk :</p>
            <ol>
                <li>Karyawan dan Atasan yang berwenang harus mengisi semua butir yang terdapat dalam formulir ini, sebelum diserahkan ke bagian HRD untuk dicatat dan disimpan.</li>
                <li>Formulir ini diajukan selambat-lambatnya 1 (satu) minggu sebelum pengambilan cuti.</li>
                <li>Cuti tahunan harus diambil maksimal 3 (tiga) hari berturut-turut.</li>
                <li>Cuti tahunan harus diambil dan tidak dapat diuangkan.</li>
                <li>Khusus cuti Manager, permohonan ini harus disetujui oleh Direktur.</li>
            </ol>
        </div>

        <!-- Content Columns -->
        <div class="content-columns">
            <div class="hr-col">
                <p style="font-style: italic; text-decoration: underline; font-weight: bold; margin-bottom: 20px;">Kolom ini diisi oleh HRD/Personalia</p>
                
                <div class="form-row">
                    <div class="form-label">Hak Cuti Tahunan</div>
                    <div class="form-separator">:</div>
                    <div class="form-value" style="text-align: center; font-weight: bold;">{{ $data->saldo ? $data->saldo->total_cuti . ' Hari' : '' }}</div>
                </div>
                <div class="form-row">
                    <div class="form-label">Telah diambil</div>
                    <div class="form-separator">:</div>
                    <div class="form-value" style="text-align: center; font-weight: bold;">{{ $data->saldo ? $data->saldo->cuti_terpakai . ' Hari' : '' }}</div>
                </div>
                <div class="form-row">
                    <div class="form-label">Permohonan Cuti</div>
                    <div class="form-separator">:</div>
                    <div class="form-value" style="text-align: center; font-weight: bold;">{{ $data->lama_cuti ? $data->lama_cuti . ' Hari' : '' }}</div>
                </div>
                <div class="form-row">
                    <div class="form-label">Sisa Hak Cuti Tahunan</div>
                    <div class="form-separator">:</div>
                    <div class="form-value" style="text-align: center; font-weight: bold;">{{ $data->saldo ? $data->saldo->sisa_cuti . ' Hari' : '' }}</div>
                </div>
            </div>
            
            <div class="user-col">
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
                <div class="form-row">
                    <div class="form-label">Tanggal Cuti</div>
                    <div class="form-separator">:</div>
                    <div class="form-value" style="border: none; border-bottom: 1px dotted #000; display: flex; justify-content: space-between;">
                        <span>{{ \Carbon\Carbon::parse($data->tanggal_mulai)->format('d F Y') }}</span>
                        <span>S.d</span>
                        <span style="flex-grow: 1; text-align: right; padding-right: 50px;">{{ \Carbon\Carbon::parse($data->tanggal_selesai)->format('d F Y') }}</span>
                    </div>
                </div>
                <div class="form-row" style="margin-bottom: 5px;">
                    <div class="form-label">Alasan Cuti</div>
                    <div class="form-separator">:</div>
                    <div class="form-value" style="border: none;">{{ $data->alasan ?? '' }}</div>
                </div>
                
                <?php $jenis = strtolower($data->jenis_cuti ?? ''); ?>
                <div class="checkbox-list">
                    <div class="checkbox-item">
                        <div class="checkbox-box">{{ $jenis == 'tahunan' ? '✓' : '' }}</div>
                        <span>Tahunan</span>
                    </div>
                    <div class="checkbox-item">
                        <div class="checkbox-box">{{ $jenis == 'menikah' ? '✓' : '' }}</div>
                        <span>Menikah</span>
                    </div>
                    <div class="checkbox-item">
                        <div class="checkbox-box">{{ $jenis == 'hamil' || $jenis == 'melahirkan' ? '✓' : '' }}</div>
                        <span>Hamil</span>
                    </div>
                    <div class="checkbox-item">
                        <div class="checkbox-box">{{ $jenis == 'haji' || $jenis == 'umroh' ? '✓' : '' }}</div>
                        <span>Haji</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px; margin-bottom: 10px;">
            Jakarta, {{ \Carbon\Carbon::parse($data->created_at)->format('d F Y') }}
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">Pemohon</div>
                <div>( {{ $data->nama_lengkap ? str_pad($data->nama_lengkap, 30, '.', STR_PAD_BOTH) : '....................................' }} )</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Atasan Langsung</div>
                <div>( {{ $data->nama_supervisor ? str_pad($data->nama_supervisor, 30, '.', STR_PAD_BOTH) : '....................................' }} )</div>
            </div>
            <div class="signature-box">
                <div class="signature-title">HRD</div>
                <div>( .................................... )</div>
            </div>
        </div>
    </div>
</body>
</html>
