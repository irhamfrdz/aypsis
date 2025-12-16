@extends('layouts.print')

@section('content')
<div style="padding:12px;max-width:800px;margin:0 auto;font-family:Arial,Helvetica,sans-serif;color:#111;">
    @php use Carbon\Carbon; @endphp
    <style>
        /* Adjust spacing for readability */
        .form-table td, .form-table th { padding: 4px 6px !important; line-height: 1.2; }
        /* Slightly smaller signature fonts and reduced spacing */
        .signature-block { font-size: 9px; }
        .signature-block .signature-name { font-size: 8px; margin-top:4px; }
        .signature-line { height: 20px; }

        /* Print-specific rules */
        @media print {
            /* make table rows more readable in print */
            .form-table td, .form-table th { padding: 3px 6px !important; font-size: 9px !important; line-height: 1.2 !important; }
            /* family table comfortable spacing */
            .family-table { font-size: 9px !important; }
            .family-table th, .family-table td { padding: 6px 6px !important; line-height: 1.2 !important; }
            /* headings */
            h2 { margin-bottom:6px !important; font-size:16px !important; }
            .signature-block { margin-top:8px !important; }
            /* top margin set to 0, keep others small */
            @page { margin: 0mm 6mm 6mm 6mm; }
        }
    </style>
    <h2 style="text-align:center;margin-bottom:4px;font-size:15px;">FORM DATA KARYAWAN</h2>
    <div style="display:flex;justify-content:flex-start;margin-bottom:6px;font-size:11px;gap:16px;">
        <div><strong>NIK:</strong> {{ $karyawan->nik ?? '-' }}</div>
    </div>

    <table class="form-table" style="width:100%;border-collapse:collapse;font-size:9px;">
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
        <div style="margin-top:6px;margin-bottom:4px;font-size:8.5px;font-style:italic;">
            Dengan ini menyatakan bahwa apa yang telah saya beritahukan di atas adalah benar dan dapat dipertanggungjawabkan.
        </div>

        <div style="margin-top:4px;font-size:8.5px;">
            <strong>Jakarta, {{ now()->format('d F Y') }}</strong>
        </div>

        <div style="margin-top:2px;margin-bottom:8px;">&nbsp;</div>

        <div style="margin-top:12px;font-size:11px;display:flex;justify-content:space-between;align-items:flex-end;">
            <div style="width:220px;text-align:center;">
                <div class="signature-line" style="height:28px;border-bottom:1px solid #ddd;width:220px;margin:0 auto;"></div>
                <div style="margin-top:6px;font-size:9px;"><strong>{{ $karyawan->nama_lengkap ?? '____________________________' }}</strong></div>
            </div>
            <div style="width:220px;text-align:center;">
                <div class="signature-name" style="font-size:8.5px;margin-bottom:6px;">&nbsp;</div>
                <div class="signature-line" style="height:28px;border-bottom:1px solid #ddd;width:220px;margin:0 auto;"></div>
                <div style="margin-top:6px;font-size:9px;"><strong>TTD HR</strong></div>
            </div>
        </div>
    </div>

    {{-- Halaman 2: Susunan Keluarga --}}
    <h2 style="text-align:center;margin-bottom:8px;">SUSUNAN KELUARGA</h2>
    <div style="display:flex;justify-content:space-between;margin-bottom:16px;font-size:13px;">
        <div><strong>NIK:</strong> {{ $karyawan->nik ?? '-' }}</div>
        <div><strong>Nama:</strong> {{ $karyawan->nama_lengkap ?? '-' }}</div>
    </div>

        @if($karyawan->familyMembers && $karyawan->familyMembers->count() > 0)
            <table class="family-table" style="width:100%;border-collapse:collapse;font-size:9px;">
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
