# 📊 Master Tujuan Kirim - Fitur Download Template & Import CSV

## 🎯 Overview

Fitur ini memungkinkan pengguna untuk:

-   **Download Template CSV** - Mendapatkan format yang benar untuk import data
-   **Import CSV** - Upload data tujuan kirim secara massal melalui file CSV

## 🚀 Fitur yang Ditambahkan

### 1. **Controller Methods**

Di `MasterTujuanKirimController.php`:

-   `downloadTemplate()` - Generate dan download template CSV kosong
-   `showImport()` - Tampilkan halaman import
-   `import()` - Proses upload dan import data CSV

### 2. **Routes**

```php
// Download Template CSV
GET: master/tujuan-kirim-download-template
Name: tujuan-kirim.download-template
Permission: can:master-tujuan-kirim-view

// Show Import Form
GET: master/tujuan-kirim-import
Name: tujuan-kirim.import
Permission: can:master-tujuan-kirim-create

// Process Import
POST: master/tujuan-kirim-import
Name: tujuan-kirim.import.process
Permission: can:master-tujuan-kirim-create
```

### 3. **Views**

-   `resources/views/master/tujuan-kirim/import.blade.php` - Halaman upload CSV
-   Updated `resources/views/master/tujuan-kirim/index.blade.php` - Tambah tombol dan notifikasi

### 4. **UI Elements**

Di halaman index:

-   ✅ Tombol "Download Template" (hijau)
-   ✅ Tombol "Import CSV" (kuning)
-   ✅ Notifikasi hasil import (sukses, error, duplikat)

## 📄 Format CSV Template

### Header Columns:

```csv
kode;nama_tujuan;catatan;status
```

### Data Requirements:

-   **kode**: Required, max 10 chars, must be unique
-   **nama_tujuan**: Required, max 100 chars
-   **catatan**: Optional, max 500 chars
-   **status**: Required, values: 'active' or 'inactive'

### File Requirements:

-   **Format**: CSV dengan delimiter titik koma (;)
-   **Encoding**: UTF-8 dengan BOM
-   **Size**: Maximum 2MB
-   **Extensions**: .csv, .txt

## 🔧 Import Features

### ✅ Validasi Data:

-   Format header CSV
-   Required fields validation
-   Data length validation
-   Status value validation
-   Duplicate kode detection

### ✅ Error Handling:

-   File format validation
-   File size validation
-   Row-by-row error reporting
-   Duplicate data reporting
-   Transaction safety

### ✅ User Experience:

-   Drag & drop file upload
-   Real-time file validation
-   Progress indication
-   Detailed success/error messages
-   Template download integration

## 🎨 UI Components

### Halaman Import (`import.blade.php`):

-   📁 Drag & drop file upload area
-   📋 Import instructions and guidelines
-   📊 CSV format examples
-   🔄 Real-time file validation
-   📱 Responsive design

### Notifikasi Import (di `index.blade.php`):

-   ✅ **Success**: Jumlah data berhasil diimport
-   ❌ **Errors**: Daftar error per baris dengan detail
-   ⚠️ **Duplicates**: Daftar data duplikat yang dilewati

## 🚦 Testing

### Manual Testing Steps:

1. **Download Template**:

    ```
    Akses: /master/tujuan-kirim
    Klik: "Download Template"
    Verify: File CSV terdownload dengan header yang benar
    ```

2. **Import Valid CSV**:

    ```
    Akses: /master/tujuan-kirim-import
    Upload: File CSV dengan data valid
    Verify: Data tersimpan di database
    ```

3. **Import Invalid CSV**:
    ```
    Upload: File dengan format salah
    Verify: Error messages ditampilkan
    ```

### Sample Valid CSV:

```csv
kode;nama_tujuan;catatan;status
JKT001;Jakarta Pusat;Pelabuhan Tanjung Priok;active
SBY002;Surabaya Timur;;inactive
BDG003;Bandung Kota;Terminal Bandung;active
```

## 🔐 Permissions

### Required Permissions:

-   **View/Download**: `master-tujuan-kirim-view`
-   **Import**: `master-tujuan-kirim-create`

### Permission Checking:

```php
@can('master-tujuan-kirim-view')
    // Download template button
@endcan

@can('master-tujuan-kirim-create')
    // Import CSV button & form
@endcan
```

## 🎯 Benefits

### ✅ **Efficiency**:

-   Bulk data import
-   Template standardization
-   Reduced manual entry

### ✅ **User Experience**:

-   Intuitive UI/UX
-   Clear instructions
-   Real-time feedback

### ✅ **Data Quality**:

-   Comprehensive validation
-   Duplicate prevention
-   Error reporting

### ✅ **Maintainability**:

-   Clean code structure
-   Proper error handling
-   Consistent UI patterns

## 🔄 Usage Workflow

```
1. User clicks "Download Template"
   ↓
2. System generates CSV template
   ↓
3. User fills template with data
   ↓
4. User clicks "Import CSV"
   ↓
5. User uploads filled CSV
   ↓
6. System validates data
   ↓
7. System imports valid records
   ↓
8. System shows results (success/errors/duplicates)
```

## 📝 Implementation Notes

### Code Quality:

-   ✅ Proper input validation
-   ✅ Error handling with try-catch
-   ✅ Transaction safety
-   ✅ Memory efficient processing
-   ✅ UTF-8 BOM support

### Security:

-   ✅ File type validation
-   ✅ File size limits
-   ✅ Permission-based access
-   ✅ CSRF protection
-   ✅ Input sanitization

### Performance:

-   ✅ Streaming CSV generation
-   ✅ Row-by-row processing
-   ✅ Memory efficient file handling
-   ✅ Pagination for large datasets

---

**🎉 Status: ✅ COMPLETED**
**📅 Created: October 13, 2025**
**👨‍💻 Feature: Master Tujuan Kirim CSV Import/Export**
