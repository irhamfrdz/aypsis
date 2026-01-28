@extends('layouts.print')

@section('content')
<div class="no-print" style="text-align:right; max-width:800px; margin:0 auto 10px auto;">
    @if(isset($karyawan) && $karyawan->exists)
        <a href="{{ route('master.karyawan.export-single', $karyawan->id) }}" 
           style="display:inline-block; padding:8px 12px; background:#10b981; color:#fff; text-decoration:none; border-radius:4px; font-weight:bold; font-size:12px;">
            Export Excel
        </a>
    @else
        <a href="{{ route('master.karyawan.excel-template') }}" 
           style="display:inline-block; padding:8px 12px; background:#10b981; color:#fff; text-decoration:none; border-radius:4px; font-weight:bold; font-size:12px;">
            Download Template Excel
        </a>
    @endif
</div>
<div style="padding:12px;max-width:800px;margin:0 auto;font-family:Arial,Helvetica,sans-serif;color:#000;font-weight:bold;">
    @php use Carbon\Carbon; @endphp
    <style>
        /* Adjust spacing for readability */
        .form-table td, .form-table th { padding: 4px 6px !important; line-height: 1.2; }
        /* Slightly smaller signature fonts and reduced spacing */
        .signature-block { font-size: 10px; }
        .signature-block .signature-name { font-size: 9px; margin-top:4px; }
        .signature-line { height: 20px; }

        /* Print-specific rules */
        @media print {
            /* Balanced spacing to fit single F4 page with better readability */
            .form-table td, .form-table th { padding: 3px 6px !important; font-size: 11px !important; line-height: 1.15 !important; font-weight: bold !important; }
            /* family table compact */
            .family-table { font-size: 10px !important; font-weight: bold !important; }
            .family-table th, .family-table td { padding: 3px 5px !important; line-height: 1.1 !important; }
            /* headings */
            h2 { margin-bottom:3px !important; font-size:16px !important; }
            .signature-block { margin-top:4px !important; }
            .signature-line { height:16px !important; }
            /* reduce page margins to maximize vertical space */
            @page { margin: 0mm 4mm 4mm 4mm; }
        }
    </style>
    <h2 style="text-align:center;margin-bottom:4px;font-size:18px;">FORM DATA KARYAWAN</h2>
    <div style="display:flex;justify-content:flex-start;margin-bottom:6px;font-size:12px;gap:16px;">
        <div><strong>NIK:</strong> {{ $karyawan->nik ?? '-' }}</div>
    </div>
    @php $fmCount = min($karyawan->familyMembers->count(), 6); @endphp

    <table class="form-table" style="width:100%;border-collapse:collapse;font-size:12px;font-weight:bold;">
        <!-- Requested fields in specific order -->
        <tr>
            <td style="width:40%;padding:1px;border:1px solid #ddd;background:#f7fafc;"><strong>1. NIK Karyawan</strong></td>
            <td style="padding:1px;border:1px solid #ddd;">{{ $karyawan->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>2. Nama Lengkap</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>3. NIK KTP</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->ktp ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>4. No. Kartu Keluarga</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kk ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>5. JKN/BPJS Kesehatan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->jkn ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>6. BP Jamsostek</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->no_ketenagakerjaan ?? $karyawan->no_bpjs_ketenagakerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>7. Jenis Kelamin</strong></td>
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
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>8. Tempat / Tanggal Lahir</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ ($karyawan->tempat_lahir ?? '-') . ' / ' . ($karyawan->tanggal_lahir ? Carbon::parse($karyawan->tanggal_lahir)->format('d/M/Y') : '-') }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>9. Agama</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->agama ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>10. No. Handphone</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->no_hp ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>11. Status Kawin</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->status_perkawinan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>12. Tanggal Masuk Kerja</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->tanggal_masuk ? Carbon::parse($karyawan->tanggal_masuk)->format('d/M/Y') : '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>13. Tanggungan (Anak)</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->tanggungan_anak ?? $karyawan->tanggungan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>14. Alamat Lengkap</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->alamat_lengkap ?? $karyawan->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>15. Kelurahan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kelurahan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>16. Kecamatan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kecamatan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>17. Kota / Kabupaten</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kabupaten ?? $karyawan->kota ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>18. Provinsi</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->provinsi ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>19. Kode Pos</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kode_pos ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>20. Email</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->email ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>21. Divisi</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->divisi ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>22. Pekerjaan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->pekerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>23. Supervisor</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->supervisor ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>24. Kantor Cabang</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>25. Status Pajak</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->status_pajak ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>26. Nama Bank</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->nama_bank ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>27. Cabang Bank</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->bank_cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>28. Nomor Rekening</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->akun_bank ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>29. Atas Nama Rekening</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->atas_nama ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>30. Catatan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->catatan ?? '-' }}</td>
        </tr>
    </table>

    {{-- Halaman 1: Pernyataan dan Tanda Tangan --}}
    <div class="signature-block" style="page-break-inside:avoid; -webkit-column-break-inside: avoid; break-inside: avoid;">
        <div style="margin-top:6px;margin-bottom:4px;font-size:10px;font-style:italic;font-weight:bold;">
            Dengan ini menyatakan bahwa apa yang telah saya beritahukan di atas adalah benar dan dapat dipertanggungjawabkan.
        </div>

        <div style="margin-top:4px;font-size:10px;font-weight:bold;">
            <strong>Jakarta, {{ now()->format('d F Y') }}</strong>
        </div>

        <div style="margin-top:2px;margin-bottom:8px;">&nbsp;</div>

        <div style="margin-top:12px;font-size:12px;display:flex;justify-content:space-between;align-items:flex-end;">
            <div style="width:220px;text-align:center;">
                <div class="signature-line" style="height:28px;border-bottom:1px solid #ddd;width:220px;margin:0 auto;"></div>
                <div style="margin-top:6px;font-size:11px;"><strong>{{ $karyawan->nama_lengkap ?? '____________________________' }}</strong></div>
            </div>
            <div style="width:220px;text-align:center;">
                <div class="signature-name" style="font-size:10px;margin-bottom:6px;">&nbsp;</div>
                <div class="signature-line" style="height:28px;border-bottom:1px solid #ddd;width:220px;margin:0 auto;"></div>
                <div style="margin-top:6px;font-size:11px;"><strong>TTD HR</strong></div>
            </div>
        </div>
    </div>

    {{-- Halaman 2: Susunan Keluarga --}}
    <h2 style="text-align:center;margin-bottom:8px;font-size:18px;">SUSUNAN KELUARGA</h2>
    <div style="display:flex;justify-content:space-between;margin-bottom:16px;font-size:14px;font-weight:bold;">
        <div><strong>NIK:</strong> {{ $karyawan->nik ?? '-' }}</div>
        <div><strong>Nama:</strong> {{ $karyawan->nama_lengkap ?? '-' }}</div>
    </div>

        @if($karyawan->familyMembers && $karyawan->familyMembers->count() > 0)
            <table class="family-table" style="width:100%;border-collapse:collapse;font-size:11px;font-weight:bold;">
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
                    @foreach($karyawan->familyMembers->take(6) as $index => $familyMember)
                        <tr>
                            <td style="padding:3px;border:1px solid #ddd;text-align:center;">{{ $index + 1 }}</td>
                            <td style="padding:3px;border:1px solid #ddd;">{{ $familyMember->hubungan ?? '-' }}</td>
                            <td style="padding:3px;border:1px solid #ddd;">{{ $familyMember->nama ?? '-' }}</td>
                            <td style="padding:3px;border:1px solid #ddd;text-align:center;">{{ $familyMember->tanggal_lahir ? Carbon::parse($familyMember->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
                            <td style="padding:3px;border:1px solid #ddd;">{{ $familyMember->alamat ?? '-' }}</td>
                            <td style="padding:3px;border:1px solid #ddd;">{{ $familyMember->no_telepon ?? '-' }}</td>
                            <td style="padding:3px;border:1px solid #ddd;">{{ $familyMember->nik_ktp ?? '-' }}</td>
                        </tr>
                    @endforeach
                    {{-- Ensure table shows exactly 6 rows for consistent layout --}}
                    @for($i = $fmCount; $i < 6; $i++)
                        <tr>
                            <td style="padding:3px;border:1px solid #ddd;text-align:center;">{{ $i + 1 }}</td>
                            <td style="padding:3px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:3px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:3px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:3px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:3px;border:1px solid #ddd;">&nbsp;</td>
                            <td style="padding:3px;border:1px solid #ddd;">&nbsp;</td>
                        </tr>
                    @endfor
                    @if($karyawan->familyMembers->count() > 6)
                        <tr>
                            <td colspan="7" style="padding:4px;border:1px solid #ddd;font-size:10px;text-align:left;">Menampilkan 6 dari {{ $karyawan->familyMembers->count() }} anggota keluarga. Lihat detail untuk lengkapnya.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        @else
            {{-- Empty table with 10 rows for family members --}}
            <table style="width:100%;border-collapse:collapse;font-size:12px;font-weight:bold;">
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
                    @for($i = 0; $i < 6; $i++)
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
        @endif    {{-- Susunan Keluarga End --}}

</div>
@endsection
