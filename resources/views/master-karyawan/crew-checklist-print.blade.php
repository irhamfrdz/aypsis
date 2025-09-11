<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Kelengkapan Crew - {{ $karyawan->nama_lengkap }}</title>
    <style>
        @media print {
            @page {
                margin: 1cm;
                size: A4;
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.4;
                color: #000;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .info-item {
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
            text-transform: uppercase;
        }

        .no-col { width: 30px; text-align: center; }
        .kelengkapan-col { width: 180px; }
        .ada-col, .tidak-col { width: 40px; text-align: center; }
        .sertifikat-col { width: 120px; }
        .date-col { width: 70px; text-align: center; }
        .catatan-col { width: 100px; }

        .checkbox {
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            display: inline-block;
            text-align: center;
            line-height: 10px;
            font-weight: bold;
        }

        .checked {
            background-color: #000;
            color: #fff;
        }

        .description {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
            font-style: italic;
        }

        .expired {
            background-color: #ffebee;
        }

        .expiring-soon {
            background-color: #fff3e0;
        }

        .notes-section {
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .notes-content {
            font-size: 9px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Checklist Kelengkapan Crew</h1>
    </div>

    <div class="info-section">
        <div>
            <div class="info-item">
                <span class="info-label">NAMA</span>: {{ $karyawan->nama_lengkap }}
            </div>
            <div class="info-item">
                <span class="info-label">TEMPAT/ TGL LAHIR</span>: {{ $karyawan->tempat_lahir }} / {{ $karyawan->tanggal_lahir ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('d-m-Y') : '-' }}
            </div>
            <div class="info-item">
                <span class="info-label">No. HP</span>: {{ $karyawan->no_hp }}
            </div>
            <div class="info-item">
                <span class="info-label">JABATAN</span>: {{ $karyawan->pekerjaan }}
            </div>
        </div>
        <div>
            <div class="info-item">
                <span class="info-label">NIK</span>: {{ $karyawan->nik }}
            </div>
            <div class="info-item">
                <span class="info-label">DIVISI</span>: {{ $karyawan->divisi }}
            </div>
            <div class="info-item">
                <span class="info-label">TANGGAL PRINT</span>: {{ now()->format('d-m-Y') }}
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="no-col">No</th>
                <th class="kelengkapan-col">Kelengkapan</th>
                <th class="ada-col">ADA</th>
                <th class="tidak-col">TIDAK</th>
                <th class="sertifikat-col">NOMOR SERTIFIKAT</th>
                <th class="date-col">ISSUED</th>
                <th class="date-col">EXPIRED</th>
            </tr>
        </thead>
        <tbody>
            @foreach($checklistItems as $index => $item)
                <tr class="{{ $item->is_expired ? 'expired' : ($item->is_expiring_soon ? 'expiring-soon' : '') }}">
                    <td class="no-col">{{ $index + 1 }}</td>
                    <td class="kelengkapan-col">
                        <strong>{{ $item->item_name }}</strong>
                        @if(in_array($item->item_name, ['BST (Basic Safety Training)', 'SCRB (Survival Craft and Rescue Boat)', 'AFF (Advanced Fire Fighting)', 'MFA (Medical First Aid)', 'SAT (Security Awareness Training)', 'SDSD (Seafarer with Designated Security Duties)', 'ERM (Engine Room Resource Management)', 'BRM (Bridge Resource Management)', 'MC (Medical Care)']))
                            <div class="description">
                                @if($item->item_name == 'BST (Basic Safety Training)')
                                    BST - Sertifikat dasar keselamatan pelaut untuk menghadapi bahaya di atas kapal, wajib dimiliki semua tingkat jabatan.
                                @elseif($item->item_name == 'SCRB (Survival Craft and Rescue Boat)')
                                    SCRB - Pelatihan penggunaan sekoci dan perahu penyelamat dalam keadaan darurat di laut.
                                @elseif($item->item_name == 'AFF (Advanced Fire Fighting)')
                                    AFF - Pelatihan pemadaman kebakaran tingkat lanjut.
                                @elseif($item->item_name == 'MFA (Medical First Aid)')
                                    MFA - Pelatihan pertolongan pertama medis di kapal.
                                @elseif($item->item_name == 'SAT (Security Awareness Training)')
                                    SAT - Pelatihan kesadaran keamanan kapal sesuai ISPS Code dan STCW Amandemen 2010.
                                @elseif($item->item_name == 'SDSD (Seafarer with Designated Security Duties)')
                                    SDSD - Pelatihan untuk pelaut yang ditunjuk menjalankan tugas keamanan kapal sesuai ISPS Code dan STCW Amandemen 2010.
                                @elseif($item->item_name == 'ERM (Engine Room Resource Management)')
                                    ERM - Manajemen sumber daya di ruang mesin.
                                @elseif($item->item_name == 'BRM (Bridge Resource Management)')
                                    BRM - Pelatihan manajemen sumber daya di anjungan kapal.
                                @elseif($item->item_name == 'MC (Medical Care)')
                                    MC - Pelatihan lanjutan untuk penanganan medis di kapal.
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="ada-col">
                        <span class="checkbox {{ $item->status == 'ada' ? 'checked' : '' }}">
                            {{ $item->status == 'ada' ? '✓' : '' }}
                        </span>
                    </td>
                    <td class="tidak-col">
                        <span class="checkbox {{ $item->status == 'tidak' ? 'checked' : '' }}">
                            {{ $item->status == 'tidak' ? '✓' : '' }}
                        </span>
                    </td>
                    <td class="sertifikat-col">{{ $item->nomor_sertifikat ?: '-' }}</td>
                    <td class="date-col">{{ (isset($item->issued_date) && is_object($item->issued_date)) ? $item->issued_date->format('d-m-Y') : (is_string($item->issued_date) && trim($item->issued_date) !== '' ? \Carbon\Carbon::parse($item->issued_date)->format('d-m-Y') : '-') }}</td>
                    <td class="date-col">
                        {{ (isset($item->expired_date) && is_object($item->expired_date)) ? $item->expired_date->format('d-m-Y') : (is_string($item->expired_date) && trim($item->expired_date) !== '' ? \Carbon\Carbon::parse($item->expired_date)->format('d-m-Y') : '-') }}
                        @if($item->is_expired)
                            <br><small style="color: red;">EXPIRED</small>
                        @elseif($item->is_expiring_soon)
                            <br><small style="color: orange;">AKAN EXPIRED</small>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="notes-section">
        <div class="notes-title">Catatan Khusus:</div>
        <div class="notes-content">
            <p><strong>BST</strong> - Sertifikat dasar keselamatan pelaut untuk menghadapi bahaya di atas kapal, wajib dimiliki semua tingkat jabatan.</p>
            <p><strong>SCRB</strong> - Pelatihan penggunaan sekoci dan perahu penyelamat dalam keadaan darurat di laut.</p>
            <p><strong>AFF</strong> - Pelatihan pemadaman kebakaran tingkat lanjut</p>
            <p><strong>MFA</strong> - Pelatihan pertolongan pertama medis di kapal</p>
            <p><strong>SAT</strong> - Pelatihan kesadaran keamanan kapal sesuai ISPS Code dan STCW Amandemen 2010</p>
            <p><strong>SDSD</strong> - Pelatihan untuk pelaut yang ditunjuk menjalankan tugas keamanan kapal sesuai ISPS Code dan STCW Amandemen 2010</p>
            <p><strong>ERM</strong> - Manajemen sumber daya di ruang mesin</p>
            <p><strong>BRM</strong> - Pelatihan manajemen sumber daya di anjungan kapal</p>
            <p><strong>MC</strong> - Pelatihan lanjutan untuk penanganan medis di kapal</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
