# 📋 PANDUAN IMPORT DATA MASTER

## 📦 Yang Sudah Dilakukan

Saya telah berhasil mengimpor **semua data master** dari file backup SQL ke database Anda.

### ✅ Status Import
- **Total Tabel Berhasil**: 11 tabel
- **Total Record**: 559 data
- **File Backup**: `c:\folder_kerjaan\backup\aypsis_backup.sql`

## 📊 Data yang Diimpor

| Tabel | Jumlah | Deskripsi |
|-------|--------|-----------|
| `master_kegiatans` | 10 | Kegiatan operasional |
| `master_pricelist_sewa_kontainers` | 7 | Harga sewa kontainer |
| `divisis` | 9 | Divisi perusahaan |
| `pekerjaans` | 56 | Jabatan/pekerjaan |
| `pajaks` | 12 | Data pajak |
| `banks` | 6 | Data bank |
| `akun_coa` | 417 | Chart of Accounts |
| `cabangs` | 3 | Cabang perusahaan |
| `tipe_akuns` | 15 | Tipe akun |
| `kode_nomor` | 21 | Kode nomor |
| `nomor_terakhir` | 3 | Nomor terakhir |

## 🛠️ Script yang Tersedia

### 1. Quick Check (Tercepat)
Cek cepat apakah semua data master tersedia:
```bash
php quick_check_master.php
```

### 2. Ringkasan Lengkap
Lihat ringkasan detail semua data:
```bash
php summary_import.php
```

### 3. Verifikasi Detail
Verifikasi data dengan sample:
```bash
php verify_master_data.php
```

### 4. Re-Import (Jika Diperlukan)
Jalankan ulang import jika diperlukan:
```bash
php import_all_master_tables.php
```

## 📝 Detail Data Penting

### Master Kegiatan (10 data)
```
KGT001 - ANTAR ISI
KGT002 - ANTAR KOSONG
KGT003 - TARIK ISI
KGT004 - TARIK KOSONG
KGT005 - ANTAR KONTAINER PERBAIKAN
KGT006 - TARIK KONTAINER PERBAIKAN
KGT007 - ANTAR KONTAINER SEWA
KGT008 - TARIK KONTAINER SEWA
KGT009 - PENJUALAN KONTAINER
KGT010 - PEMBELIAN KONTAINER
```

### Divisi (9 data)
```
ABK - ABK
ADM - ADMINISTRASI
DIR - DIREKSI
KRN - KRANI
LAP - LAPANGAN
PRT - PORT
SPR - SUPIR
NKR - NON KARYAWAN
STP - SATPAM
```

### Cabang (3 data)
```
- JAKARTA
- BATAM
- TANJUNG PINANG
```

## ⚠️ Catatan

### Tabel yang Tidak Ditemukan
Tabel berikut tidak ditemukan di file backup:
- `vendor_bengkels`
- `pricelist_cats`
- `stock_kontainers`

Jika tabel ini diperlukan, Anda perlu menambahkan datanya secara manual atau dari sumber lain.

## 🔍 Cara Menggunakan Data

### Via Eloquent Model
```php
use App\Models\MasterKegiatan;
use App\Models\Divisi;

// Ambil semua kegiatan
$kegiatans = MasterKegiatan::all();

// Ambil divisi tertentu
$divisi = Divisi::where('kode_divisi', 'ADM')->first();
```

### Via Query Builder
```php
use Illuminate\Support\Facades\DB;

// Ambil data bank
$banks = DB::table('banks')->get();

// Ambil cabang tertentu
$cabang = DB::table('cabangs')
    ->where('nama_cabang', 'JAKARTA')
    ->first();
```

## 📞 Troubleshooting

### Jika Ada Masalah
1. **Cek koneksi database**
   ```bash
   php artisan tinker --execute="DB::connection()->getPdo()"
   ```

2. **Verifikasi data**
   ```bash
   php quick_check_master.php
   ```

3. **Re-import jika diperlukan**
   ```bash
   php import_all_master_tables.php
   ```

## ✅ Kesimpulan

Semua data master dari file backup sudah berhasil diimpor ke database Anda. Total **559 record** dari **11 tabel master** siap digunakan!

---
*Dibuat pada: 3 Oktober 2025*
