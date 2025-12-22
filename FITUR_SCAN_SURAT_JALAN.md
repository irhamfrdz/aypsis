# Fitur Scan Surat Jalan dari Excel

## Deskripsi
Fitur ini memungkinkan untuk melakukan update massal status BL menjadi "Sudah Muat" berdasarkan daftar nomor surat jalan yang diupload melalui file Excel.

## Cara Penggunaan

### 1. Persiapan File Excel
- Buat file Excel dengan format berikut:
  - Kolom pertama (A): Nomor Surat Jalan
  - Baris pertama bisa berisi header (opsional, akan otomatis di-skip jika terdeteksi)
  - Contoh:
    ```
    Nomor Surat Jalan
    SJ-001/2024
    SJ-002/2024
    SJ-003/2024
    ```

### 2. Upload File
1. Klik tombol **"Scan Surat Jalan"** di halaman Prospek
2. Modal upload akan terbuka
3. Pilih file Excel (.xlsx, .xls, atau .csv)
4. Maksimal ukuran file: 5 MB
5. Klik tombol **"Scan & Update"**

### 3. Proses
Sistem akan:
1. Membaca semua nomor surat jalan dari kolom pertama
2. Mencari data surat jalan yang sesuai di database
3. Menemukan prospek yang terkait dengan surat jalan tersebut
4. Update status prospek menjadi "Sudah Muat"
5. Update semua BL yang terkait dengan prospek tersebut menjadi status "Sudah Muat"

### 4. Hasil
Setelah proses selesai, sistem akan menampilkan:
- Total nomor surat jalan yang dipindai
- Jumlah yang ditemukan di database
- Jumlah yang tidak ditemukan
- Jumlah prospek yang diupdate
- Jumlah BL yang diupdate
- Daftar nomor surat jalan yang tidak ditemukan (jika ada)

## File Template
Download template Excel: `/public/templates/template_scan_surat_jalan.csv`

## Technical Details

### Route
- URL: `/prospek/scan-surat-jalan`
- Method: POST
- Middleware: `can:prospek-edit`

### Controller Method
- Controller: `ProspekController`
- Method: `scanSuratJalan(Request $request)`
- Location: `app/Http/Controllers/ProspekController.php`

### Request Validation
```php
'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120' // max 5MB
```

### Response Format
```json
{
    "success": true,
    "message": "Berhasil memproses X dari Y surat jalan. Z BL telah diupdate menjadi 'Sudah Muat'.",
    "data": {
        "total_scanned": 100,
        "found": 95,
        "not_found": 5,
        "not_found_numbers": ["SJ-001", "SJ-002", ...],
        "bl_updated": 120,
        "prospek_updated": 95
    }
}
```

### Database Updates
1. **Tabel: prospeks**
   - Field: `status` → 'sudah_muat'
   - Field: `updated_by` → user ID

2. **Tabel: bls**
   - Field: `status_bongkar` → 'Sudah Muat'
   - Field: `updated_by` → user ID

## Permissions Required
User harus memiliki permission: `prospek-edit`

## Logging
Semua error akan dicatat dalam Laravel log dengan format:
```
Error scanning surat jalan: [error message]
```

## Notes
- Jika nomor surat jalan tidak ditemukan, sistem akan melanjutkan ke nomor berikutnya
- Sistem akan otomatis mendeteksi dan skip baris header jika mengandung kata "nomor", "no", atau "surat"
- Satu surat jalan bisa terkait dengan beberapa prospek dan BL
- Semua perubahan akan tercatat dengan user_id yang melakukan update
