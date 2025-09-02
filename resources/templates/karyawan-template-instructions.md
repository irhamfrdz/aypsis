Template instructions for importing Karyawan via CSV

-   Open `karyawan-template.csv` with Excel or any spreadsheet editor.
-   Keep the first row as header. Column names must match the expected attribute names:
    -   nik, nama_lengkap, nama_panggilan, npwp, ktp, kk, jkn, no_ketenagakerjaan, jenis_kelamin,
        tempat_lahir, tanggal_lahir (YYYY-MM-DD), agama, no_hp, status_perkawinan, tanggal_masuk (YYYY-MM-DD),
        tanggungan_anak, alamat_lengkap, kelurahan, kecamatan, kabupaten, provinsi, kode_pos, email
-   Save as CSV (Comma separated) and upload via the Import CSV page.
-   Notes:
    -   The import uses `nik` as unique identifier and will update existing records with the same `nik`.
    -   Date fields must be in `YYYY-MM-DD` format for proper parsing.
    -   If your Excel uses a different delimiter or locale settings, ensure to export as a comma-separated CSV.
