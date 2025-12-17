# SYNC TAGIHAN KONTAINER SEWA FROM MASTER KONTAINERS

Script untuk memperbaiki dan menyinkronkan tagihan kontainer sewa berdasarkan data `tanggal_mulai_sewa` dan `tanggal_selesai_sewa` dari table `kontainers`.

## File
`sync_tagihan_kontainer_from_master.php`

## Fungsi Utama

Script ini akan:
1. ✅ Membaca data kontainer yang memiliki `tanggal_mulai_sewa`
2. ✅ Generate tagihan per periode bulanan dari tanggal mulai sampai tanggal selesai
3. ✅ Jika `tanggal_selesai_sewa` NULL, generate sampai hari ini
4. ✅ Sinkronkan dengan data existing di `daftar_tagihan_kontainer_sewa`
5. ✅ Create tagihan baru jika belum ada
6. ✅ Update tagihan existing jika ada perbedaan tanggal/harga
7. ✅ Skip tagihan yang sudah masuk pranota (status_pranota IS NOT NULL)

## Cara Penggunaan

### 1. Test Mode (Dry Run)
Lihat apa yang akan dilakukan tanpa menyimpan perubahan:

```bash
# Test untuk semua kontainer
php sync_tagihan_kontainer_from_master.php --dry-run

# Test untuk kontainer tertentu
php sync_tagihan_kontainer_from_master.php --container=CBHU3952697 --dry-run

# Test untuk vendor tertentu
php sync_tagihan_kontainer_from_master.php --vendor=DPE --dry-run --verbose
```

### 2. Eksekusi Sebenarnya

```bash
# Sync semua kontainer
php sync_tagihan_kontainer_from_master.php

# Sync kontainer tertentu
php sync_tagihan_kontainer_from_master.php --container=CBHU3952697 --verbose

# Sync semua kontainer vendor DPE
php sync_tagihan_kontainer_from_master.php --vendor=DPE

# Sync semua kontainer vendor ZONA
php sync_tagihan_kontainer_from_master.php --vendor=ZONA
```

### 3. Options

| Option | Deskripsi |
|--------|-----------|
| `--container=NOMOR` | Filter kontainer tertentu |
| `--vendor=VENDOR` | Filter vendor tertentu (DPE/ZONA) |
| `--dry-run` | Test mode, tidak menyimpan perubahan |
| `--verbose` | Tampilkan detail output untuk setiap periode |
| `--force` | Force update (reserved untuk future use) |

## Contoh Output

```
╔════════════════════════════════════════════════════════════════╗
║  SYNC TAGIHAN KONTAINER SEWA FROM MASTER KONTAINERS           ║
╚════════════════════════════════════════════════════════════════╝

📦 Ditemukan 13 kontainer dengan tanggal_mulai_sewa

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📦 Kontainer: CBHU3952697
   Vendor: DPE
   Ukuran: 20
   Tanggal Mulai: 2025-01-24
   Tanggal Selesai: 2025-04-09
   💰 Harga Sewa: Rp 775.000

   ➕ Periode 1 CREATED: 2025-01-24 s/d 2025-02-23
   🔄 Periode 2 UPDATED: 2025-02-24 s/d 2025-03-23
   🔄 Periode 3 UPDATED: 2025-03-24 s/d 2025-04-09

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📊 SUMMARY:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Total Kontainer Diproses: 13
Total Periode Created:    103
Total Periode Updated:    51
Total Periode Skipped:    0
Total Errors:             0
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

✅ SELESAI
```

## Cara Kerja Detail

### 1. Pembacaan Data Kontainer
- Ambil semua kontainer yang memiliki `tanggal_mulai_sewa` NOT NULL
- Filter berdasarkan `--container` atau `--vendor` jika disediakan

### 2. Penentuan Harga Sewa
Script akan mencari harga sewa dengan urutan:
1. Dari tagihan existing di `daftar_tagihan_kontainer_sewa` (ambil yang terakhir)
2. Dari `master_pricelist_sewa_kontainers` berdasarkan vendor dan ukuran

### 3. Generate Periode
- Mulai dari `tanggal_mulai_sewa`
- Setiap periode = 1 bulan (30 hari)
- Jika ada `tanggal_selesai_sewa`, generate sampai tanggal tersebut
- Jika NULL, generate sampai hari ini
- Periode terakhir akan dipotong jika melewati tanggal_selesai_sewa

### 4. Sync dengan Data Existing
Untuk setiap periode:
- **Jika sudah ada** (berdasarkan nomor_kontainer + periode + status_pranota IS NULL):
  - Update jika ada perbedaan: tanggal_awal, tanggal_akhir, dpp, vendor, size
  - Skip jika sudah sama
- **Jika belum ada**:
  - Create record baru dengan data lengkap

### 5. Safety Features
- ✅ Tidak akan mengubah tagihan yang sudah masuk pranota
- ✅ Tidak akan mengubah tagihan yang status_pranota = 'paid'
- ✅ Safety limit 100 periode per kontainer
- ✅ Error handling untuk tanggal invalid

## Rekomendasi Penggunaan

### Step 1: Test Dulu
```bash
php sync_tagihan_kontainer_from_master.php --dry-run --verbose
```

### Step 2: Sync Per Vendor
```bash
# Sync vendor DPE dulu
php sync_tagihan_kontainer_from_master.php --vendor=DPE

# Lalu sync vendor ZONA
php sync_tagihan_kontainer_from_master.php --vendor=ZONA
```

### Step 3: Verifikasi
Cek hasil dengan:
```bash
php temp_check_detail_tagihan.php
```

## Catatan Penting

⚠️ **PENTING**: 
- Script hanya akan update tagihan yang `status_pranota IS NULL`
- Tagihan yang sudah paid atau sudah masuk pranota tidak akan diubah
- Pastikan data `tanggal_mulai_sewa` dan `tanggal_selesai_sewa` di table kontainers sudah benar
- Gunakan `--dry-run` untuk preview sebelum eksekusi

## Troubleshooting

### Jika periode tidak sesuai:
1. Cek `tanggal_mulai_sewa` dan `tanggal_selesai_sewa` di table kontainers
2. Jalankan dengan `--verbose` untuk melihat detail

### Jika harga tidak sesuai:
1. Cek `master_pricelist_sewa_kontainers`
2. Atau update manual harga di `daftar_tagihan_kontainer_sewa` lalu run ulang

### Jika ada error:
1. Cek format tanggal di table kontainers
2. Pastikan vendor dan ukuran sudah benar
