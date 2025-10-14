# 🔧 Troubleshooting Guide - Enhanced CSV Import

## ❌ Error: "Format CSV tidak valid"

### Problem

Ketika upload CSV file, muncul error: "Format CSV tidak valid. Pastikan file memiliki salah satu dari format berikut..."

### Solution Steps

#### 1. ✅ Check Your CSV Columns

Buka file CSV Anda dan pastikan ada salah satu dari kombinasi kolom berikut:

**Format A - Standard Grouping:**

-   ✅ `Group` (atau `group`, `GROUP`)
-   ✅ `Periode` (atau `periode`, `PERIODE`)
-   ✅ `Nomor Kontainer` (atau `nomor_kontainer`, `kontainer`, `Kontainer`)

**Format B - Smart Grouping:**

-   ✅ `No.InvoiceVendor` (atau `InvoiceVendor`, `Invoice Vendor`)
-   ✅ `No.Bank` (atau `Bank`, `NoBank`)
-   ✅ `Nomor Kontainer` (atau `nomor_kontainer`, `kontainer`)

#### 2. ✅ Column Name Variations (Case-Insensitive)

Sistem mendukung variasi nama kolom:

| Standard  | Variations Supported                                                        |
| --------- | --------------------------------------------------------------------------- |
| Group     | `group`, `Group`, `GROUP`                                                   |
| Periode   | `periode`, `Periode`, `PERIODE`                                             |
| Kontainer | `nomor_kontainer`, `Nomor Kontainer`, `kontainer`, `Kontainer`, `KONTAINER` |
| Invoice   | `No.InvoiceVendor`, `InvoiceVendor`, `Invoice Vendor`, `invoice_vendor`     |
| Bank      | `No.Bank`, `Bank`, `NoBank`, `no_bank`                                      |

#### 3. ✅ CSV Format Requirements

-   **Delimiter**: Semicolon (`;`) - PENTING!
-   **Encoding**: UTF-8
-   **Header**: Baris pertama harus berisi nama kolom
-   **Data**: Baris kedua dst berisi data

#### 4. ✅ Example Valid Files

**Example 1 - Standard Format:**

```csv
Group;Periode;Nomor Kontainer;keterangan
1;202412;TEMU1234567;Kontainer zona A
1;202412;TEMU2345678;Kontainer zona A
2;202412;TEMU3456789;Kontainer zona B
```

**Example 2 - Smart Grouping Format:**

```csv
No.InvoiceVendor;No.Bank;Nomor Kontainer
INV001;BNK001;TEMU1234567
INV001;BNK001;TEMU2345678
INV002;BNK001;TEMU3456789
```

**Example 3 - Mixed Case (Also Valid):**

```csv
group;PERIODE;Nomor Kontainer
1;202412;TEMU1234567
2;202412;TEMU2345678
```

### Common Issues & Fixes

#### Issue 1: Wrong Delimiter

```csv
❌ WRONG: Group,Periode,Nomor Kontainer  (using comma)
✅ CORRECT: Group;Periode;Nomor Kontainer  (using semicolon)
```

#### Issue 2: Missing Required Columns

```csv
❌ WRONG: Name;Address;Phone  (random columns)
✅ CORRECT: Group;Periode;Nomor Kontainer
```

#### Issue 3: Extra Spaces in Column Names

```csv
❌ WRONG: " Group ";  " Periode "  (with spaces)
✅ CORRECT: Group;Periode  (clean names)
```

#### Issue 4: Wrong File Format

-   Make sure file extension is `.csv`
-   Open in text editor to verify semicolon delimiter
-   Save as UTF-8 encoding

### Debug Your CSV File

#### Step 1: Check Column Detection

1. Open your CSV in notepad
2. Look at first line (header)
3. Verify column names match supported variations

#### Step 2: Test with Simple File

Create a test file with minimal data:

```csv
Group;Periode;Nomor Kontainer
1;202412;TEST001
```

#### Step 3: Check File Encoding

-   Open CSV in Notepad++
-   Check encoding (should be UTF-8)
-   If wrong, convert to UTF-8

### Enhanced Error Messages

Versi terbaru sistem memberikan error message yang lebih detail:

-   ✅ Menampilkan kolom yang ditemukan
-   ✅ Menampilkan kolom yang dicari
-   ✅ Memberikan saran perbaikan

### Quick Fixes

#### Fix 1: Excel to CSV Conversion

1. Open file in Excel
2. File → Save As
3. Choose "CSV (Semicolon delimited) (\*.csv)"
4. Choose UTF-8 encoding

#### Fix 2: Manual Column Rename

```csv
BEFORE: Invoice_Number;Bank_Code;Container_Number
AFTER:  No.InvoiceVendor;No.Bank;Nomor Kontainer
```

#### Fix 3: Google Sheets Export

1. File → Download → Comma Separated Values (.csv)
2. Open in notepad
3. Replace all commas with semicolons
4. Save as UTF-8

### Test Files Available

```
✅ test_standard_format.csv - Standard group/periode format
✅ test_vendor_format.csv - Vendor invoice/bank format
✅ test_enhanced_import.php - Format detection testing script
```

### Still Having Issues?

#### 1. Check System Logs

Error details tersimpan di Laravel logs untuk debugging.

#### 2. Use Template Download

-   Download template dari sistem
-   Copy your data to template format
-   Upload template file

#### 3. Manual Column Mapping

Jika nama kolom tidak standard, rename sesuai format yang didukung.

### Success Indicators

Jika berhasil, Anda akan melihat:

```
✅ Import selesai (Mode: Group + Periode): X pranota berhasil dibuat
✅ Import selesai (Mode: Invoice Vendor + Bank Number): X pranota berhasil dibuat
✅ Efisiensi grouping: X% (untuk mode smart grouping)
```

### Advanced Tips

#### 1. Prefer Smart Grouping

Format Invoice + Bank menghasilkan efisiensi lebih tinggi (40-50% lebih sedikit pranota).

#### 2. Column Order Doesn't Matter

```csv
✅ Group;Periode;Nomor Kontainer
✅ Nomor Kontainer;Group;Periode
✅ Periode;Nomor Kontainer;Group
```

#### 3. Extra Columns OK

```csv
✅ Group;Periode;Nomor Kontainer;extra1;extra2
```

#### 4. Case Mixing OK

```csv
✅ Group;periode;NOMOR_KONTAINER
```

Semua variasi di atas akan otomatis terdeteksi oleh sistem enhanced import! 🚀
