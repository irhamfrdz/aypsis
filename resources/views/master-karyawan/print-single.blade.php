@extends('layouts.print')

@section('content')
<div style="padding:12px;max-width:800px;margin:0 auto;font-family:Arial,Helvetica,sans-serif;color:#111;">
    @php use Carbon\Carbon; @endphp
    <h2 style="text-align:center;margin-bottom:6px;font-size:16px;">FORM DATA KARYAWAN</h2>
    <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:11px;">
        <div><strong>NIK:</strong> {{ $karyawan->nik ?? '-' }}</div>
        <div><strong>Tanggal Cetak:</strong> {{ now()->format('Y-m-d') }}</div>
    </div>

    <table style="width:100%;border-collapse:collapse;font-size:10px;">
        <!-- Requested fields in specific order -->
        <tr>
            <td style="width:40%;padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>1. NIK Karyawan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>2. Nama Lengkap</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>3. NPWP</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->npwp ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>4. NIK KTP</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->ktp ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>5. No. Kartu Keluarga</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kk ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>6. JKN/BPJS Kesehatan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->jkn ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>7. BP Jamsostek</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->no_ketenagakerjaan ?? $karyawan->no_bpjs_ketenagakerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>8. Jenis Kelamin</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">
                @if($karyawan->jenis_kelamin == 'L')
                    Laki-Laki
                @elseif($karyawan->jenis_kelamin == 'P')
                    Perempuan
                @else
                    {{ $karyawan->jenis_kelamin ?? '-' }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>9. Tempat / Tanggal Lahir</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ ($karyawan->tempat_lahir ?? '-') . ' / ' . ($karyawan->tanggal_lahir ? Carbon::parse($karyawan->tanggal_lahir)->format('d/M/Y') : '-') }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>10. Agama</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->agama ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>11. No. Handphone</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->no_hp ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>12. Status Kawin</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->status_perkawinan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>13. Tanggal Masuk Kerja</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->tanggal_masuk ? Carbon::parse($karyawan->tanggal_masuk)->format('d/M/Y') : '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>14. Tanggungan (Anak)</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->tanggungan_anak ?? $karyawan->tanggungan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>15. Alamat Lengkap</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->alamat_lengkap ?? $karyawan->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>16. Kelurahan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kelurahan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>17. Kecamatan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kecamatan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>18. Kota / Kabupaten</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kabupaten ?? $karyawan->kota ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>19. Provinsi</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->provinsi ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>20. Kode Pos</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kode_pos ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>21. Email</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->email ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>22. Divisi</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->divisi ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>23. Pekerjaan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->pekerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>24. Supervisor</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->supervisor ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>25. Kantor Cabang</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>26. Status Pajak</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->status_pajak ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>27. Nama Bank</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->nama_bank ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>28. Cabang Bank</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->bank_cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>29. Nomor Rekening</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->akun_bank ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>30. Atas Nama Rekening</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->atas_nama ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>31. Tanggal Berhenti</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->tanggal_berhenti ? Carbon::parse($karyawan->tanggal_berhenti)->format('d/M/Y') : '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>32. Catatan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->catatan ?? '-' }}</td>
        </tr>
    </table>

    {{-- Halaman 1: Pernyataan dan Tanda Tangan --}}
    <div style="margin-top:12px;margin-bottom:6px;font-size:9px;font-style:italic;">
        Dengan ini menyatakan bahwa apa yang telah saya beritahukan di atas adalah benar dan dapat dipertanggungjawabkan.
    </div>

    <div style="margin-top:6px;font-size:9px;">
        <strong>Jakarta, {{ now()->format('d F Y') }}</strong>
    </div>

    <div style="margin-top:8px;margin-bottom:20px;">&nbsp;</div>

    <div style="font-size:9px;">
        <strong>( {{ $karyawan->nama_lengkap ?? '____________________________' }} )</strong>
    </div>

    <div style="margin-top:12px;font-size:11px;display:flex;justify-content:space-between;">
        <div>
            <div style="margin-bottom:12px;"><strong>TTD Karyawan</strong></div>
            <div style="height:40px;border-bottom:1px solid #ddd;width:180px;"></div>
        </div>
        <div style="text-align:center;">
            <div style="margin-bottom:6px;"><strong>TTD HR</strong></div>
            <div style="height:40px;border-bottom:1px solid #ddd;width:180px;margin-left:20px;"></div>
        </div>
    </div>

    {{-- Page Break --}}
    <div style="page-break-before:always;"></div>

    {{-- Halaman 2: Susunan Keluarga --}}
    <h2 style="text-align:center;margin-bottom:8px;">SUSUNAN KELUARGA</h2>
    <div style="display:flex;justify-content:space-between;margin-bottom:16px;font-size:13px;">
        <div><strong>NIK:</strong> {{ $karyawan->nik ?? '-' }}</div>
        <div><strong>Nama:</strong> {{ $karyawan->nama_lengkap ?? '-' }}</div>
        <div><strong>Tanggal Cetak:</strong> {{ now()->format('Y-m-d') }}</div>
    </div>

        @if($karyawan->familyMembers && $karyawan->familyMembers->count() > 0)
            <table style="width:100%;border-collapse:collapse;font-size:11px;">
                <thead>
                    <tr style="background:#f7fafc;">
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:8%;"><strong>NO.</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:15%;"><strong>HUBUNGAN</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:20%;"><strong>NAMA</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:12%;"><strong>TGL. LAHIR</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:20%;"><strong>ALAMAT</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:12%;"><strong>NO. TELEPON</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:13%;"><strong>NO. NIK/KTP</strong></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($karyawan->familyMembers as $index => $familyMember)
                        <tr>
                            <td style="padding:6px;border:1px solid #ddd;text-align:center;">{{ $index + 1 }}</td>
                            <td style="padding:6px;border:1px solid #ddd;">{{ $familyMember->hubungan ?? '-' }}</td>
                            <td style="padding:6px;border:1px solid #ddd;">{{ $familyMember->nama ?? '-' }}</td>
                            <td style="padding:6px;border:1px solid #ddd;text-align:center;">{{ $familyMember->tanggal_lahir ? Carbon::parse($familyMember->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
                            <td style="padding:6px;border:1px solid #ddd;">{{ $familyMember->alamat ?? '-' }}</td>
                            <td style="padding:6px;border:1px solid #ddd;">{{ $familyMember->no_telepon ?? '-' }}</td>
                            <td style="padding:6px;border:1px solid #ddd;">{{ $familyMember->nik_ktp ?? '-' }}</td>
                        </tr>
                    @endforeach
                    {{-- Add empty rows if less than 10 family members for consistent layout --}}
                    @for($i = $karyawan->familyMembers->count(); $i < 10; $i++)
                        <tr>
                            <td style="padding:6px;border:1px solid #ddd;text-align:center;">{{ $i + 1 }}</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        @else
            {{-- Empty table with 10 rows for family members --}}
            <table style="width:100%;border-collapse:collapse;font-size:11px;">
                <thead>
                    <tr style="background:#f7fafc;">
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:8%;"><strong>NO.</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:15%;"><strong>HUBUNGAN</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:20%;"><strong>NAMA</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:12%;"><strong>TGL. LAHIR</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:20%;"><strong>ALAMAT</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:12%;"><strong>NO. TELEPON</strong></th>
                        <th style="padding:6px;border:1px solid #ddd;text-align:center;width:13%;"><strong>NO. NIK/KTP</strong></th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 10; $i++)
                        <tr>
                            <td style="padding:6px;border:1px solid #ddd;text-align:center;">{{ $i + 1 }}</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:6px;border:1px solid #ddd;">&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        @endif    {{-- Catatan untuk Susunan Keluarga --}}
    <div style="margin-top:16px;font-size:10px;color:#666;">
        <p><strong>Catatan:</strong></p>
        <ul style="margin:4px 0;padding-left:16px;">
            <li>Isi data susunan keluarga dengan lengkap dan benar</li>
            <li>Untuk anak yang belum memiliki KTP, kolom NIK/KTP bisa dikosongkan</li>
            <li>Pastikan nomor telepon dapat dihubungi untuk keperluan darurat</li>
        </ul>
    </div>

</div>
@endsection
