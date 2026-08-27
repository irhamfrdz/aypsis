<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Multiple Form</title>
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
            box-sizing: border-box;
            border: 2px solid #000;
            position: relative;
            margin: 0 auto;
            page-break-after: always;
            margin-bottom: 20px;
        }
        .page:last-child {
            page-break-after: auto;
            margin-bottom: 0;
        }
        .page.cuti {
            width: 297mm; /* Landscape A4 */
            min-height: 170mm;
            padding: 10mm;
        }
        .page.izin {
            width: 210mm;
            min-height: 148mm; /* Setengah A4 */
            padding: 10mm;
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
        .page.izin .header::after {
            display: none;
        }
        .page.izin .header {
            margin-bottom: 20px;
        }
        .logo {
            width: 70px;
            height: auto;
            margin-right: 15px;
        }
        .page.izin .logo {
            width: 80px;
            margin-right: 20px;
        }
        .company-info {
            flex-grow: 1;
            padding-right: 20px;
            border-right: 2px solid #000;
        }
        .page.izin .company-info {
            border-right: none;
            padding-right: 0;
        }
        .company-info p {
            margin: 0;
            font-size: 13px;
            font-style: italic;
        }
        .page.izin .company-info p {
            font-size: 14px;
        }
        .company-info h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
        }
        .page.izin .company-info h1 {
            font-size: 20px;
            color: #000;
        }
        .form-title {
            width: 350px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            padding-left: 20px;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0 20px;
            text-transform: uppercase;
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
        .page.izin .form-row {
            margin-bottom: 8px;
            align-items: flex-start;
        }
        .form-label {
            width: 150px;
        }
        .page.izin .form-label {
            width: 100px;
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
        .page.izin .form-value {
            border-bottom: none;
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
        .checkbox-list {
            margin-top: 10px;
            margin-left: 120px;
        }
        .checkbox-item {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

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

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            text-align: center;
        }
        .page.izin .signature-section {
            margin-top: 30px;
        }
        .signature-box {
            width: 30%;
        }
        .signature-title {
            margin-bottom: 70px;
        }
        .page.izin .signature-title {
            margin-bottom: 50px;
        }
        
        .note {
            margin-top: 20px;
            font-size: 11px;
        }

        /* Stamp */
        .stamp {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 70px;
            font-weight: bold;
            text-transform: uppercase;
            border: 6px solid;
            padding: 15px 40px;
            border-radius: 12px;
            opacity: 0.25;
            pointer-events: none;
            z-index: 50;
            letter-spacing: 5px;
        }
        .stamp.approved {
            color: #10B981;
            border-color: #10B981;
        }
        .stamp.rejected {
            color: #EF4444;
            border-color: #EF4444;
        }

        @media print {
            body { background: none; }
            .page {
                margin: 0;
                border: 2px solid #000;
                box-shadow: none;
            }
            .page.cuti {
                size: A4 landscape;
            }
            .page.izin {
                size: A4 portrait;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="no-print" style="background: #fdf6b2; padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">🖨️ CETAK SEMUA</button>
    </div>

    @foreach($dataToPrint as $item)
        @if($item['type'] === 'cuti')
            <?php 
                $data = $item['data']; 
                $statusText = strtoupper($data->status ?? '');
                $isApproved = in_array($statusText, ['APPROVED', 'DISETUJUI']);
                $isRejected = in_array($statusText, ['REJECTED', 'DITOLAK']);
            ?>
            <div class="page cuti">
                @if($isApproved || $isRejected)
                    <div class="stamp {{ $isApproved ? 'approved' : 'rejected' }}">
                        {{ $isApproved ? 'DISETUJUI' : 'DITOLAK' }}
                    </div>
                @endif
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
                        <div>( {{ !empty($data->nama_hrd) ? str_pad($data->nama_hrd, 30, '.', STR_PAD_BOTH) : '....................................' }} )</div>
                    </div>
                </div>
            </div>
        @elseif($item['type'] === 'izin')
            <?php 
                $data = $item['data']; 
                $statusText = strtoupper($data->status ?? '');
                $isApproved = in_array($statusText, ['APPROVED', 'DISETUJUI']);
                $isRejected = in_array($statusText, ['REJECTED', 'DITOLAK']);
            ?>
            <div class="page izin">
                @if($isApproved || $isRejected)
                    <div class="stamp {{ $isApproved ? 'approved' : 'rejected' }}">
                        {{ $isApproved ? 'DISETUJUI' : 'DITOLAK' }}
                    </div>
                @endif
                <!-- Header -->
                <div class="header">
                    <div class="logo">
                        <img src="/images/logo.png" alt="Logo" style="width: 100%;" onerror="this.style.display='none'">
                    </div>
                    <div class="company-info">
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
                    <div class="form-value"><span class="dotted-line" style="border-bottom-width: 0;">{{ $data->nama_lengkap ?? '' }}</span></div>
                </div>
                <div class="form-row">
                    <div class="form-label">Divisi</div>
                    <div class="form-separator">:</div>
                    <div class="form-value"><span class="dotted-line" style="border-bottom-width: 0;">{{ $data->divisi ?? '' }}</span></div>
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
        @endif
    @endforeach
</body>
</html>
