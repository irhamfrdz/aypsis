Master Karyawan CSV template

Location: resources/csv_templates/master_karyawan_template.csv

How to use:

-   Download or open one of the template files and edit rows for each karyawan.
-   Template variants included:
    -   `master_karyawan_template.csv` (comma-separated). Fields containing commas are quoted.
    -   `master_karyawan_template_semi.csv` (semicolon-separated) — useful when address fields contain commas.
    -   `master_karyawan_template_tsv.txt` (tab-separated) — comfortable for Excel/LibreOffice and avoids comma issues.
-   The import expects a header row that matches column names.
-   Date fields must be in YYYY-MM-DD format: `tanggal_lahir`, `tanggal_masuk`, `tanggal_berhenti`, `tanggal_masuk_sebelumnya`, `tanggal_berhenti_sebelumnya`.
-   Unique key: `nik` is used as the unique identifier when importing; rows with the same `nik` will update the existing record.
-   Optional/nullable fields: most fields are nullable; ensure required ones like `nik`, `nama_lengkap`, `nama_panggilan` are provided.

Important fields included in template header:

-   nik, nama_lengkap, nama_panggilan, email, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, no_hp, ktp, kk,
-   divisi, pekerjaan, tanggal_masuk, tanggal_berhenti, tanggal_masuk_sebelumnya, tanggal_berhenti_sebelumnya, nik_supervisor, supervisor,
-   cabang, plat, alamat, rt_rw, kelurahan, kecamatan, kabupaten, provinsi, kode_pos, alamat_lengkap,
-   nama_bank, akun_bank, atas_nama, status_pajak, jkn, no_ketenagakerjaan, tanggungan_anak
-   catatan (opsional) — catatan bebas, akan disimpan ke field `catatan`.

Notes:

-   Ensure CSV encoding is UTF-8 without BOM for proper import.
-   If your CSV tool inserts quotes or escapes, ensure fields are properly exported as standard CSV.
-   If you need assistance mapping an existing HR export to this template, I can add a small mapping script.
-   If you need assistance mapping an existing HR export to this template, I can add a small mapping script.

Delimiter auto-detection: the importer will try to detect comma, semicolon, or tab delimiters so you can use any of the included template variants.

Templates updated: `npwp` was removed from the templates to match the current database schema.
