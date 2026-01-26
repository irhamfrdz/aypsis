@extends('layouts.print')

@section('content')
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
            /* headings */
            h2 { margin-bottom:3px !important; font-size:16px !important; }
            .signature-block { margin-top:4px !important; }
            .signature-line { height:16px !important; }
            /* reduce page margins to maximize vertical space */
            @page { margin: 0mm 4mm 4mm 4mm; }
        }
    </style>
    <h2 style="text-align:center;margin-bottom:4px;font-size:18px;">FORM DATA KARYAWAN TIDAK TETAP</h2>
    <div style="display:flex;justify-content:flex-start;margin-bottom:6px;font-size:12px;gap:16px;">
        <div><strong>NIK:</strong> {{ $karyawan->nik ?? '-' }}</div>
    </div>

    <table class="form-table" style="width:100%;border-collapse:collapse;font-size:12px;font-weight:bold;">
        <tr>
            <td style="width:40%;padding:1px;border:1px solid #ddd;background:#f7fafc;"><strong>1. NIK Karyawan</strong></td>
            <td style="padding:1px;border:1px solid #ddd;">{{ $karyawan->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>2. Nama Lengkap</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>3. Nama Panggilan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->nama_panggilan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>4. NIK KTP</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->nik_ktp ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>5. Jenis Kelamin</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->jenis_kelamin ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>6. Agama</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->agama ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>7. Alamat Lengkap</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->alamat_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>8. RT / RW</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->rt_rw ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>9. Kelurahan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kelurahan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>10. Kecamatan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kecamatan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>11. Kabupaten / Kota</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kabupaten ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>12. Provinsi</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->provinsi ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>13. Kode Pos</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->kode_pos ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>14. Email</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->email ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>15. Divisi</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->divisi ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>16. Pekerjaan</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->pekerjaan ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>17. Kantor Cabang</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>18. Tanggal Masuk</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->tanggal_masuk ? Carbon::parse($karyawan->tanggal_masuk)->format('d/M/Y') : '-' }}</td>
        </tr>
        <tr>
            <td style="padding:3px;border:1px solid #ddd;background:#f7fafc;"><strong>19. Status Pajak</strong></td>
            <td style="padding:3px;border:1px solid #ddd;">{{ $karyawan->status_pajak ?? '-' }}</td>
        </tr>
    </table>

    {{-- Halaman 1: Pernyataan dan Tanda Tangan --}}
    <div class="signature-block" style="page-break-inside:avoid; -webkit-column-break-inside: avoid; break-inside: avoid;">
        <div style="margin-top:20px;margin-bottom:4px;font-size:10px;font-style:italic;font-weight:bold;">
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

</div>
@endsection
