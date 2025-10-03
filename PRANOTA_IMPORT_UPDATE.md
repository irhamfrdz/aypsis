# 🎉 FITUR IMPORT PRANOTA - UPDATED VERSION

## 📝 Perubahan Utama

### ✨ Konsep Baru: Group-Based Import

**SEBELUM:** 1 Pranota = 1 Kontainer  
**SEKARANG:** **1 Pranota = Beberapa Kontainer** (berdasarkan Group dan Periode yang sama)

---

## 🎯 Cara Kerja

### Input CSV

File CSV dapat menggunakan 2 format:

#### Format 1: Template Simple

```csv
group,periode,keterangan,due_date
1,1,Pranota Group 1 Periode 1,2025-11-01
2,1,Pranota Group 2 Periode 1,2025-11-15
```

#### Format 2: Export File (Recommended) ✅

Langsung gunakan file hasil export dari "Daftar Tagihan Kontainer Sewa":

```csv
Group;Vendor;Nomor Kontainer;Size;Periode;...
1;DPE;CBHU3952697;20;1;...
1;DPE;CBHU4077764;20;1;...
1;DPE;CBHU5876322;20;1;...
```

### Proses Import

1. Sistem membaca CSV (support delimiter `;` dan `,`)
2. **Mengelompokkan data berdasarkan `Group` dan `Periode`**
3. Untuk setiap kombinasi Group-Periode:
    - Cari semua tagihan kontainer sewa yang match (group, periode, nomor kontainer)
    - Buat **1 pranota** untuk semua kontainer dalam group-periode tersebut
    - Update status semua tagihan menjadi "masuk pranota"

### Output

-   **1 Pranota per kombinasi Group-Periode**
-   Berisi beberapa kontainer sekaligus
-   Total amount = Sum dari semua grand_total kontainer dalam group tersebut

---

## 📊 Contoh Praktis

### Input: File Export dengan 10 baris data

```
Group 1, Periode 1 → 5 kontainer (CBHU001, CBHU002, CBHU003, CBHU004, CBHU005)
Group 1, Periode 2 → 3 kontainer (CBHU001, CBHU002, CBHU003)
Group 2, Periode 1 → 2 kontainer (CBHU006, CBHU007)
```

### Output: 3 Pranota

```
✅ Pranota #1: PMS110250000123
   - Group 1, Periode 1
   - 5 kontainer
   - Total: Rp 4.275.000

✅ Pranota #2: PMS110250000124
   - Group 1, Periode 2
   - 3 kontainer
   - Total: Rp 2.565.000

✅ Pranota #3: PMS110250000125
   - Group 2, Periode 1
   - 2 kontainer
   - Total: Rp 1.710.000
```

---

## 🔧 Perubahan Teknis

### Controller Changes

**File:** `app/Http/Controllers/PranotaTagihanKontainerSewaController.php`

**Method `importCsv()` - Key Changes:**

1. Support semicolon (`;`) delimiter untuk export file
2. Grouping logic berdasarkan `group` dan `periode`
3. Bulk insert pranota per group-periode
4. Bulk update status tagihan per group-periode

**Method `downloadTemplateCsv()` - Updated:**

-   Template sekarang menggunakan kolom `group` dan `periode`
-   Sample data menunjukkan konsep grouping

### View Changes

**File:** `resources/views/pranota/import.blade.php`

1. Update instruksi import
2. Tambah informasi tentang grouping
3. Display detail pranota yang dibuat (group, periode, jumlah kontainer)
4. Tambah contoh visual hasil import

### Documentation

**File:** `PRANOTA_IMPORT_FEATURE.md`

-   Update format CSV
-   Update alur proses
-   Update contoh penggunaan
-   Update database impact

---

## 🎁 Keuntungan

### Sebelum (1 Pranota = 1 Kontainer)

-   Import 100 kontainer → 100 pranota
-   Sulit tracking pranota per group
-   Banyak dokumen pranota

### Sekarang (1 Pranota = Multiple Kontainer per Group)

-   Import 100 kontainer → ~10-20 pranota (tergantung jumlah group-periode)
-   Mudah tracking: 1 pranota = 1 group & 1 periode
-   Dokumen lebih terorganisir
-   Sesuai dengan workflow bisnis yang mengelompokkan kontainer

---

## 🚀 Cara Menggunakan

### Quick Start

1. Masuk ke menu **Daftar Tagihan Kontainer Sewa**
2. Klik tombol **Export** untuk download data
3. Masuk ke menu **Pranota Kontainer Sewa**
4. Klik tombol **Import Pranota**
5. Upload file export yang sudah di-download
6. Klik **Import Pranota**
7. ✅ Done! Sistem otomatis buat pranota per group-periode

### Customization

Jika ingin custom keterangan atau due date per group:

1. Download template CSV
2. Isi kolom `group`, `periode`, `keterangan`, `due_date`
3. Upload dan import

---

## ⚠️ Important Notes

1. **Delimiter Support:**

    - Semicolon (`;`) - untuk file export
    - Comma (`,`) - untuk template manual

2. **Matching Criteria:**

    - Sistem match berdasarkan: Nomor Kontainer + Group + Periode
    - Hanya kontainer dengan `status_pranota = NULL` yang di-import

3. **Grouping Logic:**

    - Key: `{group}_{periode}`
    - Semua kontainer dengan key yang sama masuk ke 1 pranota

4. **Column Names:**
    - Support lowercase: `group`, `periode`, `nomor_kontainer`
    - Support capitalized: `Group`, `Periode`, `Nomor Kontainer`

---

## 📝 Testing Checklist

-   [x] Import dari template simple berhasil
-   [x] Import dari export file berhasil
-   [x] Grouping berdasarkan group-periode bekerja
-   [x] Multiple kontainer masuk ke 1 pranota
-   [x] Status tagihan berubah ke "included"
-   [x] Nomor pranota generate dengan benar
-   [x] Display hasil import menampilkan detail group-periode
-   [x] Delimiter semicolon dan comma di-support
-   [x] Column names flexible (lowercase & capitalized)

---

## 🎊 Ready to Use!

Fitur sudah siap digunakan dan telah disesuaikan dengan kebutuhan:

-   ✅ 1 Pranota = Multiple Kontainer
-   ✅ Grouping by Group & Periode
-   ✅ Support Export File Format
-   ✅ Easy to Use
-   ✅ Well Documented

---

**Happy Importing! 🚀**
