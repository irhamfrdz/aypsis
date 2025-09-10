@extends('layouts.print')

@section('content')
<div style="padding:16px;max-width:800px;margin:0 auto;font-family:Arial,Helvetica,sans-serif;color:#111;">
    @php use Carbon\Carbon; @endphp
    <h2 style="text-align:center;margin-bottom:8px;">FORM DATA KARYAWAN</h2>
    <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:13px;">
        <div><strong>NIK:</strong> {{ $karyawan->nik ?? '-' }}</div>
        <div><strong>Tanggal Cetak:</strong> {{ now()->format('Y-m-d') }}</div>
    </div>

    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <!-- Requested fields in specific order -->
        <tr>
            <td style="width:40%;padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>1. NIK Karyawan</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>2. Nama Lengkap</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>3. NPWP</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->npwp ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>4. NIK KTP</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->ktp ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>5. No. Kartu Keluarga</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->kk ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>6. JKN/BPJS Kesehatan</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->jkn ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>7. BP Jamsostek</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->no_ketenagakerjaan ?? $karyawan->no_bpjs_ketenagakerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>8. Jenis Kelamin</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->jenis_kelamin ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>9. Tempat / Tanggal Lahir</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ ($karyawan->tempat_lahir ?? '-') . ' / ' . ($karyawan->tanggal_lahir ? Carbon::parse($karyawan->tanggal_lahir)->format('d/M/Y') : '-') }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>10. Agama</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->agama ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>11. No. Handphone</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->no_hp ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>12. Status Kawin</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->status_perkawinan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>13. Tanggal Masuk Kerja</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->tanggal_masuk ? Carbon::parse($karyawan->tanggal_masuk)->format('d/M/Y') : '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>14. Tanggungan (Anak)</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->tanggungan_anak ?? $karyawan->tanggungan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>15. Alamat Lengkap</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->alamat_lengkap ?? $karyawan->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>16. Kelurahan</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->kelurahan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>17. Kecamatan</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->kecamatan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>18. Kota / Kabupaten</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->kabupaten ?? $karyawan->kota ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>19. Provinsi</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->provinsi ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>20. Kode Pos</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->kode_pos ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>21. Email</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->email ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>22. Divisi</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->divisi ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>23. Pekerjaan</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->pekerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>24. Supervisor</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->supervisor ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>25. Kantor Cabang</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>26. Nomor Plat</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->plat ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>27. Status Pajak</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->status_pajak ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>28. Nama Bank</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->nama_bank ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>29. Cabang Bank</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->bank_cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>30. Nomor Rekening</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->akun_bank ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>31. Atas Nama Rekening</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->atas_nama ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>32. Tanggal Berhenti</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->tanggal_berhenti ? Carbon::parse($karyawan->tanggal_berhenti)->format('d/M/Y') : '-' }}</td>
        </tr>
        <tr>
            <td style="padding:6px;border:1px solid #ddd;background:#f7fafc;"><strong>33. Catatan</strong></td>
            <td style="padding:6px;border:1px solid #ddd;">{{ $karyawan->catatan ?? '-' }}</td>
        </tr>
    </table>

    <div style="margin-top:12px;font-size:13px;display:flex;justify-content:space-between;">
        <div>
            <div style="margin-bottom:24px;"><strong>TTD Karyawan</strong></div>
            <div style="height:60px;border-bottom:1px solid #ddd;width:220px;"></div>
        </div>
        <div style="text-align:center;">
            <div style="margin-bottom:6px;"><strong>TTD HR</strong></div>
            <div style="height:60px;border-bottom:1px solid #ddd;width:220px;margin-left:20px;"></div>
        </div>
    </div>

</div>
@endsection
