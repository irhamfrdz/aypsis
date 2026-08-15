<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Karyawan</title>
    <style>
        body { font-family: 'Calibri', Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        td, th { padding: 6px 8px; vertical-align: top; border: 1px solid #000000; }
        .title { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 20px; background-color: #4472C4; color: #FFFFFF; }
        .section-title { font-size: 12px; font-weight: bold; margin-top: 15px; margin-bottom: 10px; background-color: #D9E1F2; }
        .label-col { width: 40%; background-color: #D9E1F2; font-weight: bold; }
        .value-col { width: 60%; }
        .no-border td { border: none; padding: 4px; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .footer-text { font-size: 11px; font-style: italic; }
    </style>
</head>
<body>
    @php use Carbon\Carbon; @endphp

    <table class="no-border">
        <tr>
            <td colspan="2" class="title" style="height: 35px; vertical-align: middle;">FORM DATA KARYAWAN</td>
        </tr>
        <tr>
            <td colspan="2"><strong>NIK:</strong> {{ $karyawan->nik ?? '-' }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="label-col">1. NIK Karyawan</td>
            <td class="value-col" style="text-align: left;">{{ $karyawan->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">2. Nama Lengkap</td>
            <td class="value-col">{{ $karyawan->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">3. NIK KTP</td>
            <td class="value-col" style="text-align: left;">{{ $karyawan->ktp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">4. No. Kartu Keluarga</td>
            <td class="value-col" style="text-align: left;">{{ $karyawan->kk ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">5. JKN/BPJS Kesehatan</td>
            <td class="value-col" style="text-align: left;">{{ $karyawan->jkn ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">6. BP Jamsostek</td>
            <td class="value-col" style="text-align: left;">{{ $karyawan->no_ketenagakerjaan ?? $karyawan->no_bpjs_ketenagakerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">7. Jenis Kelamin</td>
            <td class="value-col">
                @if($karyawan->jenis_kelamin == 'L') Laki-Laki @elseif($karyawan->jenis_kelamin == 'P') Perempuan @else {{ $karyawan->jenis_kelamin ?? '-' }} @endif
            </td>
        </tr>
        <tr>
            <td class="label-col">8. Tempat / Tanggal Lahir</td>
            <td class="value-col">{{ ($karyawan->tempat_lahir ?? '-') . ' / ' . ($karyawan->tanggal_lahir ? Carbon::parse($karyawan->tanggal_lahir)->format('d/M/Y') : '-') }}</td>
        </tr>
        <tr>
            <td class="label-col">9. Agama</td>
            <td class="value-col">{{ $karyawan->agama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">10. No. Handphone</td>
            <td class="value-col" style="text-align: left;">{{ $karyawan->no_hp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">11. Status Kawin</td>
            <td class="value-col">{{ $karyawan->status_perkawinan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">12. Tanggal Masuk Kerja</td>
            <td class="value-col">{{ $karyawan->tanggal_masuk ? Carbon::parse($karyawan->tanggal_masuk)->format('d/M/Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">13. Tanggungan (Anak)</td>
            <td class="value-col">{{ $karyawan->tanggungan_anak ?? $karyawan->tanggungan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">14. Alamat Lengkap</td>
            <td class="value-col">{{ $karyawan->alamat_lengkap ?? $karyawan->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">15. Kelurahan</td>
            <td class="value-col">{{ $karyawan->kelurahan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">16. Kecamatan</td>
            <td class="value-col">{{ $karyawan->kecamatan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">17. Kota / Kabupaten</td>
            <td class="value-col">{{ $karyawan->kabupaten ?? $karyawan->kota ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">18. Provinsi</td>
            <td class="value-col">{{ $karyawan->provinsi ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">19. Kode Pos</td>
            <td class="value-col" style="text-align: left;">{{ $karyawan->kode_pos ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">20. Email</td>
            <td class="value-col">{{ $karyawan->email ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">21. Divisi</td>
            <td class="value-col">{{ $karyawan->divisi ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">22. Pekerjaan</td>
            <td class="value-col">{{ $karyawan->pekerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">23. Posisi</td>
            <td class="value-col">{{ $karyawan->posisi ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">24. Supervisor</td>
            <td class="value-col">{{ $karyawan->supervisor ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">25. Kantor Cabang AYP</td>
            <td class="value-col">{{ $karyawan->cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">26. Status Pajak</td>
            <td class="value-col">{{ $karyawan->status_pajak ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">27. Nama Bank</td>
            <td class="value-col">{{ $karyawan->nama_bank ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">28. Cabang Bank</td>
            <td class="value-col">{{ $karyawan->bank_cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">29. Nomor Rekening</td>
            <td class="value-col" style="text-align: left;">{{ $karyawan->akun_bank ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">30. Atas Nama Rekening</td>
            <td class="value-col">{{ $karyawan->atas_nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">31. Catatan</td>
            <td class="value-col">{{ $karyawan->catatan ?? '-' }}</td>
        </tr>
        @php
            $grupList = is_string($karyawan->grup) ? json_decode($karyawan->grup, true) : (is_array($karyawan->grup) ? $karyawan->grup : []);
            if (!is_array($grupList)) $grupList = [];
            
            $grupFormatted = [];
            foreach ($grupList as $g) {
                $parts = explode(':', $g, 2);
                $grupFormatted[] = $parts[0] . (isset($parts[1]) && $parts[1] !== '' ? ' (' . $parts[1] . ')' : '');
            }
            $grupStr = empty($grupFormatted) ? '-' : implode(', ', $grupFormatted);

            $grupBpjsList = is_string($karyawan->grup_bpjs) ? json_decode($karyawan->grup_bpjs, true) : (is_array($karyawan->grup_bpjs) ? $karyawan->grup_bpjs : []);
            if (!is_array($grupBpjsList)) $grupBpjsList = [];
            
            $grupBpjsFormatted = [];
            foreach ($grupBpjsList as $g) {
                $parts = explode(':', $g, 2);
                $grupBpjsFormatted[] = $parts[0] . (isset($parts[1]) && $parts[1] !== '' ? ' (' . $parts[1] . ')' : '');
            }
            $grupBpjsStr = empty($grupBpjsFormatted) ? '-' : implode(', ', $grupBpjsFormatted);
        @endphp
        <tr>
            <td class="label-col">32. Group</td>
            <td class="value-col">{{ $grupStr }}</td>
        </tr>
        <tr>
            <td class="label-col">33. Group BPJS</td>
            <td class="value-col">{{ $grupBpjsStr }}</td>
        </tr>
    </table>

    <br>

    <table class="no-border">
        <tr>
            <td colspan="2" class="footer-text">
                Dengan ini menyatakan bahwa apa yang telah saya beritahukan di atas adalah benar dan dapat dipertanggungjawabkan.
            </td>
        </tr>
        <tr>
            <td colspan="2"><strong>Jakarta, {{ now()->format('d F Y') }}</strong></td>
        </tr>
        <tr>
            <td style="height: 50px;"></td>
            <td style="height: 50px;"></td>
        </tr>
        <tr>
            <td style="width: 50%;"><strong>({{ $karyawan->nama_lengkap ?? '..................................' }})</strong></td>
            <td style="width: 50%;"><strong>( HRD )</strong></td>
        </tr>
    </table>

    <br>

    <table class="no-border">
        <tr>
            <td colspan="7" class="title" style="height: 30px; vertical-align: middle;">SUSUNAN KELUARGA</td>
        </tr>
        <tr>
            <td colspan="7"><strong>NIK:</strong> {{ $karyawan->nik ?? '-' }} &nbsp;&nbsp; <strong>Nama:</strong> {{ $karyawan->nama_lengkap ?? '-' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr style="background-color: #D9E1F2;">
                <th class="text-center" style="width: 50px; font-weight: bold;">NO.</th>
                <th class="text-center" style="width: 120px; font-weight: bold;">HUBUNGAN</th>
                <th class="text-center" style="width: 180px; font-weight: bold;">NAMA</th>
                <th class="text-center" style="width: 120px; font-weight: bold;">TGL. LAHIR</th>
                <th class="text-center" style="width: 200px; font-weight: bold;">ALAMAT</th>
                <th class="text-center" style="width: 120px; font-weight: bold;">NO. TELEPON</th>
                <th class="text-center" style="width: 150px; font-weight: bold;">NO. NIK/KTP</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $fmCount = $karyawan->familyMembers ? $karyawan->familyMembers->count() : 0; 
                $rows = max(6, $fmCount);
            @endphp

            @if($karyawan->familyMembers && $fmCount > 0)
                @foreach($karyawan->familyMembers as $index => $familyMember)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $familyMember->hubungan ?? '-' }}</td>
                        <td>{{ $familyMember->nama ?? '-' }}</td>
                        <td class="text-center">{{ $familyMember->tanggal_lahir ? Carbon::parse($familyMember->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $familyMember->alamat ?? '-' }}</td>
                        <td style="text-align: left;">{{ $familyMember->no_telepon ?? '-' }}</td>
                        <td style="text-align: left;">{{ $familyMember->nik_ktp ?? '-' }}</td>
                    </tr>
                @endforeach
            @endif

            @for($i = $fmCount; $i < 6; $i++)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>
</body>
</html>
