# Master Kapal - Import CSV Documentation

## Tanggal: 16 Oktober 2025

## Fitur: Import & Export Template CSV

---

## 📋 Overview

Fitur import CSV memungkinkan user untuk menambahkan atau mengupdate data Master Kapal secara massal melalui file CSV.

---

## 🔧 Fitur yang Tersedia

### 1. **Download Template CSV**

-   **Route:** `GET /master-kapal/download-template`
-   **Permission:** `master-kapal.view`
-   **File:** `template_master_kapal.csv`
-   **Format:** Semicolon-delimited (;)
-   **Encoding:** UTF-8 with BOM

### 2. **Import CSV**

-   **Route:** `POST /master-kapal/import`
-   **Permission:** `master-kapal.create`
-   **Max File Size:** 10MB
-   **Supported Format:** .csv, .txt

---

## 📝 Format CSV

### Header (Wajib)

```csv
kode;kode_kapal;nama_kapal;lokasi;catatan;status
```

### Contoh Data

```csv
kode;kode_kapal;nama_kapal;lokasi;catatan;status
KPL001;KP-001;MV. Sinar Jaya;Pelabuhan Tanjung Priok;Kapal kontainer besar;aktif
KPL002;KP-002;MV. Nusantara;Pelabuhan Tanjung Perak;Kapal general cargo;nonaktif
KPL003;;MV. Indonesia Raya;Pelabuhan Makassar;;aktif
```

---

## 📊 Field Specifications

| Field        | Type   | Required | Max Length | Description      | Valid Values                              |
| ------------ | ------ | -------- | ---------- | ---------------- | ----------------------------------------- |
| `kode`       | String | ✅ Yes   | 50         | Kode unik kapal  | Unique, alphanumeric                      |
| `kode_kapal` | String | ❌ No    | 100        | Kode alternatif  | Alphanumeric                              |
| `nama_kapal` | String | ✅ Yes   | 255        | Nama kapal       | Any text                                  |
| `lokasi`     | String | ❌ No    | 255        | Lokasi kapal     | Any text                                  |
| `catatan`    | Text   | ❌ No    | -          | Catatan tambahan | Any text                                  |
| `status`     | Enum   | ✅ Yes   | -          | Status kapal     | `aktif`, `nonaktif`, `active`, `inactive` |

---

## ⚙️ Import Behavior

### Update vs Insert

-   **Jika `kode` sudah ada:** Data akan di-**UPDATE**
-   **Jika `kode` belum ada:** Data akan di-**INSERT** baru

### Validation Rules

1. **Kode wajib diisi** - Baris dengan kode kosong akan diabaikan
2. **Nama kapal wajib diisi** - Error jika kosong
3. **Status harus valid** - Hanya menerima: aktif/nonaktif/active/inactive
4. **Kode harus unique** - Saat insert baru

### Status Normalization

-   `active` → `aktif`
-   `inactive` → `nonaktif`
-   Case insensitive

---

## 🎯 Use Cases

### Use Case 1: Bulk Insert

**Skenario:** Import 100 kapal baru sekaligus

```csv
kode;kode_kapal;nama_kapal;lokasi;catatan;status
KPL001;KP-001;MV. Sinar Jaya;Pelabuhan Tanjung Priok;;aktif
KPL002;KP-002;MV. Nusantara;Pelabuhan Tanjung Perak;;aktif
... (98 baris lainnya)
```

**Result:** 100 data baru ditambahkan

---

### Use Case 2: Bulk Update

**Skenario:** Update lokasi untuk semua kapal yang sudah ada

```csv
kode;kode_kapal;nama_kapal;lokasi;catatan;status
KPL001;KP-001;MV. Sinar Jaya;Pelabuhan Baru;;aktif
KPL002;KP-002;MV. Nusantara;Pelabuhan Baru;;aktif
```

**Result:** 2 data diperbarui (lokasi berubah)

---

### Use Case 3: Mixed Insert & Update

**Skenario:** Import dengan kombinasi data baru dan update

```csv
kode;kode_kapal;nama_kapal;lokasi;catatan;status
KPL001;KP-001;MV. Sinar Jaya;Pelabuhan Baru;;aktif  ← Update (kode sudah ada)
KPL999;KP-999;MV. Baru;Pelabuhan Jakarta;;aktif      ← Insert (kode baru)
```

**Result:** 1 data baru ditambahkan, 1 data diperbarui

---

## 🚨 Error Handling

### Common Errors

#### 1. Format Header Salah

**Error:** `Format header CSV tidak sesuai. Gunakan template yang disediakan.`

**Penyebab:** Header tidak sesuai format yang ditentukan

**Solusi:** Download template dan gunakan header yang benar

---

#### 2. Kode Kosong

**Warning:** `Baris 5: Kode tidak boleh kosong`

**Penyebab:** Kolom kode tidak diisi

**Solusi:** Isi kolom kode atau hapus baris tersebut

---

#### 3. Nama Kapal Kosong

**Error:** `Baris 10: Nama kapal tidak boleh kosong`

**Penyebab:** Kolom nama_kapal tidak diisi

**Solusi:** Isi kolom nama_kapal (wajib)

---

#### 4. Status Tidak Valid

**Error:** `Baris 15: Status harus 'aktif' atau 'nonaktif'`

**Penyebab:** Nilai status selain aktif/nonaktif/active/inactive

**Solusi:** Gunakan nilai yang valid

---

#### 5. File Terlalu Besar

**Error:** `The csv file field must not be greater than 10240 kilobytes.`

**Penyebab:** File lebih dari 10MB

**Solusi:** Pecah file menjadi beberapa bagian

---

## 📱 User Interface

### Import Page

1. **Navigate:** Master Kapal → klik tombol "Import CSV"
2. **Upload:** Drag & drop atau browse file CSV
3. **Submit:** Klik "Import Data"
4. **Result:** Lihat summary dan error (jika ada)

### Buttons Location

-   **Index Page:** Tombol "Import CSV" (hijau) di sebelah "Tambah Kapal"
-   **Import Page:** Tombol "Import Data" (biru) dan "Kembali" (abu-abu)

---

## 🔄 Import Process Flow

```
1. User upload CSV file
         ↓
2. Validate file (size, format)
         ↓
3. Parse CSV dengan delimiter ";"
         ↓
4. Validate header
         ↓
5. Loop setiap baris:
   - Skip jika kosong
   - Validate required fields
   - Check kode exists
   - Insert or Update
         ↓
6. Return summary:
   - X data baru ditambahkan
   - Y data diperbarui
   - Z error (jika ada)
```

---

## 💻 Technical Implementation

### Controller Methods

#### `downloadTemplate()`

```php
- Generate CSV header only
- No example data
- UTF-8 BOM encoding
- Semicolon delimiter
- Stream response
```

#### `importForm()`

```php
- Show import form view
- Permission check: master-kapal.create
```

#### `import(Request $request)`

```php
- Validate file upload
- Parse CSV
- Validate header
- Loop & process data
- Transaction handling
- Return summary
```

---

## 🧪 Testing

### Manual Test Steps

1. **Download Template:**

    ```
    1. Login sebagai admin
    2. Go to Master Kapal
    3. Click "Import CSV"
    4. Click "Download Template"
    5. Verify file downloaded: template_master_kapal.csv
    ```

2. **Import Valid Data:**

    ```
    1. Edit template, tambah data valid
    2. Upload file
    3. Click "Import Data"
    4. Verify success message
    5. Check data in table
    ```

3. **Import with Errors:**

    ```
    1. Edit template, tambah data invalid (kode kosong, status salah)
    2. Upload file
    3. Verify error messages muncul
    4. Data valid tetap masuk, invalid diabaikan
    ```

4. **Update Existing Data:**
    ```
    1. Import data yang kodenya sudah ada
    2. Verify data terupdate, bukan duplicate
    ```

---

## 📦 Files Modified/Added

### Added Files:

-   `resources/views/master-kapal/import.blade.php` - Import form
-   `public/template_master_kapal_sample.csv` - Sample template

### Modified Files:

-   `app/Http/Controllers/MasterKapalController.php` - Added import methods
-   `routes/web.php` - Added import routes
-   `resources/views/master-kapal/index.blade.php` - Added import button

---

## 🔐 Security

-   **File Upload:** Max 10MB, only .csv and .txt
-   **Permission Check:** Requires `master-kapal.create`
-   **SQL Injection:** Protected by Eloquent ORM
-   **Transaction:** Rollback on error
-   **Validation:** All inputs validated before insert/update

---

## 📈 Performance

-   **Batch Processing:** Uses transaction for better performance
-   **Memory Efficient:** Stream reading for large files
-   **Error Tolerance:** Continues processing despite individual row errors

---

## 🎓 Best Practices

1. **Download template** sebelum membuat CSV sendiri
2. **Backup data** sebelum import besar
3. **Test dengan data kecil** terlebih dahulu
4. **Check error messages** setelah import
5. **Gunakan encoding UTF-8** untuk karakter Indonesia
6. **Gunakan Excel/LibreOffice** untuk edit CSV (set delimiter ;)

---

## 📝 Notes

-   Template **tidak berisi contoh data**, hanya header
-   Delimiter **wajib titik koma** (;)
-   Baris kosong otomatis **diabaikan**
-   Data duplicate (kode sama) akan **diupdate**, bukan error
-   File CSV dapat dibuat dengan Excel (save as CSV, delimiter ;)

---

## 🐛 Troubleshooting

### Problem: Import tidak berhasil

**Check:**

1. Format header benar?
2. Delimiter menggunakan ; ?
3. File encoding UTF-8?
4. Required fields terisi?

### Problem: Data tidak muncul setelah import

**Check:**

1. Lihat error messages
2. Check permission
3. Refresh halaman
4. Check database directly

---

## ✅ Changelog

### Version 1.0.0 - 16 Oktober 2025

-   ✅ Initial release
-   ✅ Download template (header only)
-   ✅ Import CSV with validation
-   ✅ Update existing data
-   ✅ Error handling & reporting
-   ✅ Transaction support

---

## 👥 Credits

**Developer:** GitHub Copilot & Development Team  
**Date:** 16 Oktober 2025  
**System:** AYPSIS
