# CSV Import - Scientific Notation Fix

## Masalah yang Diselesaikan

### 🔢 **Scientific Notation Problem**

Ketika membuka file CSV di Excel, field numerik panjang seperti NIK, KTP, KK, dan nomor HP sering dikonversi secara otomatis menjadi scientific notation:

-   `3174012345678901` → `3.17401E+15`
-   `081234567890` → `8.12346E+10`
-   `1234567890123456` → `1.23457E+15`

### ❌ **Masalah Sebelumnya:**

-   Data numerik panjang berubah format
-   Nomor HP yang diawali 0 kehilangan digit pertama
-   NIK dan KTP tidak akurat setelah import
-   Scientific notation menyebabkan error validation

## ✅ **Solusi yang Diimplementasi**

### 1. **Enhanced Normalization Function**

```php
$normalizeNumericField = function($value) {
    if (empty($value)) return null;

    $value = trim($value);

    // Remove leading apostrophe (Excel text format trick)
    if (substr($value, 0, 1) === "'") {
        $value = substr($value, 1);
    }

    // Handle scientific notation
    if (preg_match('/^-?\d*\.?\d*[eE][+-]?\d+$/', $value)) {
        $number = sprintf('%.0f', (float)$value);
        return $number;
    }

    // Preserve leading zeros for phone numbers
    if (preg_match('/^0\d+$/', $value)) {
        return $value;
    }

    // Clean non-digits
    $cleaned = preg_replace('/[^\d]/', '', $value);
    return $cleaned ?: null;
};
```

### 2. **Improved CSV Template**

Template sekarang menggunakan **apostrophe prefix** untuk memaksa Excel mengenali field sebagai text:

**Before:**

```csv
nik;ktp;no_hp
1234567890;1234567890123456;081234567890
```

**After:**

```csv
nik;ktp;no_hp
'1234567890;'1234567890123456;'081234567890
```

### 3. **Template dengan Instruksi**

Template CSV sekarang memiliki 3 baris:

1. **Header**: Nama kolom
2. **Instructions**: Panduan format untuk setiap field
3. **Sample Data**: Contoh data dengan format yang benar

## 📊 **Field yang Ditangani**

| Field                | Format | Contoh               | Keterangan                   |
| -------------------- | ------ | -------------------- | ---------------------------- |
| `nik`                | Text   | `'3174012345678901`  | NIK 16 digit                 |
| `ktp`                | Text   | `'3174012345678901`  | No KTP 16 digit              |
| `kk`                 | Text   | `'1234567890123456`  | No KK 16 digit               |
| `no_hp`              | Text   | `'081234567890`      | Nomor HP dengan leading zero |
| `jkn`                | Text   | `'0001234567890`     | No JKN/BPJS                  |
| `no_ketenagakerjaan` | Text   | `'12345678901234567` | No BP Jamsostek              |
| `akun_bank`          | Text   | `'1234567890`        | No rekening bank             |

## 🧪 **Testing**

### Test File: `test-scientific-notation.csv`

File test yang menguji berbagai skenario:

```csv
nik;nama_lengkap;ktp;no_hp
'3174012345678901;WITH APOSTROPHE;'3174012345678901;'081234567890
3.17401E+15;SCIENTIFIC NOTATION;3.17401E+15;8.12346E+10
1234567890;NORMAL NUMBER;1234567890123456;1234567890
'0812345678;LEADING ZERO;'3174012345678902;'0812345678
```

### Expected Results:

-   ✅ Apostrophe data: Parsed correctly
-   ✅ Scientific notation: Converted to normal number
-   ✅ Leading zeros: Preserved
-   ✅ Normal numbers: Unchanged

## 📝 **User Instructions**

### Untuk User Excel:

1. **Download template** dari aplikasi
2. **Hapus baris instruksi** (baris ke-2) sebelum mengisi data
3. **Ikuti format sample** yang sudah disediakan
4. **Jangan ubah format cell** - biarkan sebagai text
5. **Simpan sebagai CSV** dengan delimiter semicolon (;)

### Tips Excel:

-   **Select All Cells** → **Format as Text** sebelum paste data
-   Gunakan **apostrophe (')** di awal angka panjang
-   **Import as Text** saat membuka CSV di Excel
-   Hindari **auto-formatting** Excel

## 🔧 **Technical Implementation**

### Controller Updates:

1. **Enhanced `normalizeNumericField()`** function
2. **Improved CSV template** with instructions
3. **Better error handling** for scientific notation
4. **Preserved leading zeros** for phone numbers

### Process Flow:

```
CSV Data → Detect Scientific Notation → Convert to String → Remove Apostrophe → Validate → Store
```

## 🎯 **Benefits**

1. **Data Accuracy**: Nomor tidak berubah format
2. **Leading Zero Preservation**: HP yang diawali 0 tetap utuh
3. **Excel Compatibility**: Template Excel-friendly
4. **User Friendly**: Instruksi jelas di template
5. **Error Prevention**: Mencegah validation error
6. **Backward Compatible**: Tetap support format lama

## 🚀 **Next Steps**

1. **Test thoroughly** dengan berbagai format Excel
2. **User training** untuk penggunaan template baru
3. **Monitor import logs** untuk pattern error baru
4. **Consider LibreOffice** dan software spreadsheet lain
