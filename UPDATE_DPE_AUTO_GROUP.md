# ✅ UPDATE: File CSV DPE dengan Group Kosong

## 🔄 Perubahan yang Dilakukan

Sesuai permintaan Anda, saya telah membuat file CSV baru dengan **kolom group dikosongkan** untuk vendor DPE:

### 📄 File Baru: `template_import_dpe_auto_group.csv`

**Format Header:**

```csv
vendor;nomor_kontainer;size;group;tanggal_awal;tanggal_akhir;periode;tarif;status
```

**Contoh Data:**

```csv
DPE;CCLU3836629;20;;2025-01-21;2025-02-20;1;Bulanan;Tersedia
DPE;CBHU4077764;20;;2025-01-21;2025-02-20;1;Bulanan;Tersedia
DPE;STXU2015218;20;;2025-01-22;2025-02-21;1;Bulanan;Tersedia
```

## 🎯 Keunggulan Auto-Grouping

### Dengan kolom group kosong, sistem akan:

-   ✅ **Auto-generate group ID** berdasarkan vendor + tanggal awal
-   ✅ **Kelompokkan kontainer** dengan vendor dan tanggal awal yang sama
-   ✅ **Format group**: `TK1YYMMXXXXXXX` (contoh: `TK125010000001`)
-   ✅ **Hitung finansial** otomatis berdasarkan pricelist

### Logic Grouping yang Akan Terjadi:

1. **Grup 1**: DPE + 2025-01-21 → Kontainer: CCLU3836629, CBHU4077764
2. **Grup 2**: DPE + 2025-01-22 → Kontainer: STXU2015218, CCLU3806500, DPEU4869769, CBHU5876322
3. **Grup 3**: DPE + 2025-01-23 → Kontainer: CSLU1004045, CBHU5914130
4. Dan seterusnya...

## 🚀 Cara Import

### Langkah-langkah:

1. **Login** ke sistem Aypsis
2. **Navigasi** ke Daftar Tagihan Kontainer Sewa
3. **Klik** tombol **"Upload CSV dengan Grouping"** (orange)
4. **Pilih** file `template_import_dpe_auto_group.csv`
5. **Klik** tombol **"Import & Group"**
6. **Konfirmasi** di modal yang muncul
7. **Tunggu** proses selesai dan cek hasilnya

## 📊 Hasil yang Diharapkan

Setelah import berhasil:

-   ✅ **58 tagihan** DPE akan terimport
-   ✅ **Auto-grouping** berdasarkan tanggal awal yang sama
-   ✅ **Group ID otomatis** dengan format TK1YYMMXXXXXXX
-   ✅ **Perhitungan finansial** otomatis dari pricelist
-   ✅ **Kemudahan management** grup untuk pranota

## 💡 Catatan Penting

-   **Kolom group kosong** (`;;`) memungkinkan sistem melakukan full auto-grouping
-   **Sistem akan smart grouping** berdasarkan kombinasi vendor + tanggal awal
-   **Nilai finansial** akan dihitung otomatis berdasarkan pricelist yang ada
-   **Format tanggal** sudah sesuai: YYYY-MM-DD

**File siap untuk digunakan dengan fitur auto-grouping penuh!** 🎉
