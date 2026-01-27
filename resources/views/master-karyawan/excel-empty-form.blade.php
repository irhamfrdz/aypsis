<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Data Karyawan</title>
</head>
<body>
    <h2 style="text-align:center;font-size:18px;font-weight:bold;">FORM DATA KARYAWAN</h2>
    
    <table>
        <tr>
            <td colspan="2" style="font-weight:bold;">NIK: </td>
        </tr>
    </table>

    <table border="1" style="width:100%;border-collapse:collapse;">
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;width:250px;">1. NIK Karyawan</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">2. Nama Lengkap</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">3. NIK KTP</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">4. No. Kartu Keluarga</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">5. JKN/BPJS Kesehatan</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">6. Jenis Kelamin</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">7. Tempat / Tanggal Lahir</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">8. Agama</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">9. No. Handphone</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">10. Status Kawin</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">11. Tanggal Masuk Kerja</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">12. Tanggungan (Anak)</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">13. Alamat Lengkap</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">14. Kelurahan</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">15. Kecamatan</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">16. Kota / Kabupaten</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">17. Provinsi</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">18. Kode Pos</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">19. Email</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">20. Divisi</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">21. Pekerjaan</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">22. Supervisor</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">23. Kantor Cabang</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">24. Status Pajak</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">25. Nama Bank</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">26. Cabang Bank</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">27. Nomor Rekening</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">28. Atas Nama Rekening</td>
            <td></td>
        </tr>
        <tr>
            <td style="background-color:#f7fafc;font-weight:bold;">29. Catatan</td>
            <td></td>
        </tr>
    </table>

    <br>

    <h3 style="text-align:center;">SUSUNAN KELUARGA</h3>
    <table border="1" style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background-color:#f7fafc;">
                <th style="font-weight:bold;">ID</th>
                <th style="font-weight:bold;">HUBUNGAN</th>
                <th style="font-weight:bold;">NAMA</th>
                <th style="font-weight:bold;">TGL. LAHIR</th>
                <th style="font-weight:bold;">ALAMAT</th>
                <th style="font-weight:bold;">NO. TELEPON</th>
                <th style="font-weight:bold;">NO. NIK/KTP</th>
            </tr>
        </thead>
        <tbody>
            @for($i=1; $i<=6; $i++)
            <tr>
                <td style="text-align:center;">{{ $i }}</td>
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

    <br><br>

    <table style="width:100%;">
        <tr>
            <td style="width:50%;"></td>
            <td>Jakarta, ...............................</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td style="height:50px;"></td>
        </tr>
        <tr>
            <td style="text-align:center;">(_______________________)</td>
            <td style="text-align:center;">(           TTD HR          )</td>
        </tr>
    </table>

</body>
</html>
