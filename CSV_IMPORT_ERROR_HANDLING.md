# Enhanced CSV Import System - Error Handling

## Overview

Sistem import CSV telah ditingkatkan untuk memberikan feedback yang lebih detail tentang data yang berhasil dan gagal diimpor. Sistem ini tidak akan menolak semua data jika ada beberapa yang bermasalah, melainkan akan memproses data yang valid dan memberikan laporan detail tentang data yang gagal.

## Features

### 1. **Partial Success Import**

-   Data yang valid tetap akan diimpor meskipun ada beberapa baris yang bermasalah
-   Sistem akan mencoba memproses semua baris dalam file CSV
-   Tidak ada "all-or-nothing" approach

### 2. **Detailed Error Reporting**

Sistem akan memberikan informasi spesifik tentang:

-   **Nomor baris** yang bermasalah
-   **Alasan kegagalan** yang spesifik
-   **Nama karyawan** atau NIK yang terkait dengan error

### 3. **Error Categories**

#### A. **Data Validation Errors**

-   NIK kosong atau tidak ditemukan
-   Format tanggal tidak valid
-   Data terlalu panjang untuk field tertentu

#### B. **Duplicate Entry Errors**

-   Email sudah digunakan karyawan lain
-   NIK sudah ada di database
-   Nomor KTP sudah terdaftar

#### C. **Parse Errors**

-   Format CSV tidak sesuai
-   Header dan data tidak cocok
-   Encoding issues

### 4. **Enhanced Flash Messages**

#### Success Messages (Green)

```
✅ 5 data karyawan berhasil diproses
Data berhasil: Baris 2: Data karyawan baru 67890 - JANE SMITH berhasil ditambahkan; Baris 4: Data karyawan baru 22222 - VALID USER berhasil ditambahkan; dan 3 lainnya
```

#### Warning Messages (Yellow) - Partial Success

```
⚠️ Import Selesai dengan Peringatan
✅ 4 data karyawan berhasil diproses
⚠️ 3 data gagal diproses
Data gagal: Baris 3: Email john@example.com sudah digunakan karyawan lain; Baris 5: Format tanggal tidak valid untuk INVALID DATE; Baris 6: NIK kosong atau tidak ditemukan
```

#### Error Messages (Red) - Complete Failure

```
❌ Import Gagal
⚠️ 5 data gagal diproses
Data gagal: Baris 1: NIK kosong atau tidak ditemukan; Baris 2: Email duplicate@example.com sudah digunakan karyawan lain; dan 3 error lainnya
```

## Implementation Details

### Controller Changes (`KaryawanController.php`)

#### 1. **Enhanced Tracking Variables**

```php
$processed = 0;          // Successfully processed rows
$successRows = [];       // Detailed success messages
$failedRows = [];        // Detailed error messages
```

#### 2. **Improved Error Handling**

```php
try {
    $existingKaryawan = Karyawan::where('nik', $nik)->first();
    $karyawan = Karyawan::updateOrCreate(['nik' => $nik], $payload);

    $namaLengkap = $payload['nama_lengkap'] ?? $nik;
    if ($existingKaryawan) {
        $successRows[] = "Baris {$lineNumber}: Data karyawan {$nik} - {$namaLengkap} berhasil diupdate";
    } else {
        $successRows[] = "Baris {$lineNumber}: Data karyawan baru {$nik} - {$namaLengkap} berhasil ditambahkan";
    }
    $processed++;
} catch (\Exception $e) {
    // Detailed error categorization and messaging
}
```

#### 3. **Smart Flash Message Logic**

```php
if ($hasErrors && !$hasSuccess) {
    // All failed - ERROR message
} elseif ($hasErrors && $hasSuccess) {
    // Partial success - WARNING message
} else {
    // All success - SUCCESS message
}
```

### View Changes (`index.blade.php`)

#### Enhanced Flash Message Display

-   Added icons for visual clarity
-   Support for multi-line messages with `nl2br()`
-   Different layouts for success, warning, and error states
-   Better typography and spacing

## Usage Examples

### 1. **Test File with Mixed Results**

Create a CSV file with intentional errors:

```csv
nik;nama_lengkap;nama_panggilan;email;divisi;pekerjaan;tanggal_masuk;no_hp;status_pajak
12345;VALID USER;VALID;valid@example.com;IT;PROGRAMMER;2024-01-15;081234567890;PKP
;EMPTY NIK;EMPTY;;IT;TESTER;2024-02-01;081234567891;PKP
67890;DUPLICATE EMAIL;DUP;valid@example.com;ABK;CREW;2024-03-01;081234567892;PTKP
```

Expected result:

-   Row 1: ✅ Success
-   Row 2: ❌ Failed (empty NIK)
-   Row 3: ❌ Failed (duplicate email)

### 2. **Error Message Examples**

| Error Type      | Example Message                                                      |
| --------------- | -------------------------------------------------------------------- |
| Empty NIK       | `Baris 5: NIK kosong atau tidak ditemukan`                           |
| Duplicate Email | `Baris 8: Email john@example.com sudah digunakan karyawan lain`      |
| Duplicate KTP   | `Baris 12: Nomor KTP 1234567890123456 sudah digunakan karyawan lain` |
| Invalid Date    | `Baris 15: Format tanggal tidak valid untuk JOHN DOE`                |
| Data Too Long   | `Baris 20: Data terlalu panjang untuk JANE SMITH`                    |

## Benefits

1. **User Experience**

    - Clear feedback about what succeeded and what failed
    - Specific guidance on how to fix errors
    - No lost work when partial imports succeed

2. **Data Integrity**

    - Valid data is preserved even when some rows fail
    - Prevents data loss due to minor formatting issues
    - Maintains database consistency

3. **Debugging**

    - Easy identification of problematic rows
    - Specific error messages for quick fixes
    - Row number references for large files

4. **Efficiency**
    - Reduced need for multiple import attempts
    - Faster identification and resolution of data issues
    - Better batch processing for large datasets

## Future Enhancements

1. **Export Error Report**

    - Generate downloadable error report
    - Include suggested fixes for each error
    - CSV format for easy editing

2. **Preview Mode**

    - Validate file before actual import
    - Show potential errors without importing
    - Confirmation dialog for partial imports

3. **Auto-Fix Common Issues**
    - Automatic date format conversion
    - Trim whitespace and standardize data
    - Smart field mapping suggestions
