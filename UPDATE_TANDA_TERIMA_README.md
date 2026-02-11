# Script Update Tanda Terima Penerima

Script ini digunakan untuk mengupdate data penerima dan alamat pada table `tanda_terima` berdasarkan data order yang sudah diisi di approval order.

## Cara Menggunakan

### 1. Preview (Dry Run) - Lihat data yang akan diupdate tanpa melakukan perubahan
```bash
php artisan tanda-terima:update-penerima --dry-run
```

### 2. Update Semua Tanda Terima
```bash
php artisan tanda-terima:update-penerima
```

### 3. Update Tanda Terima untuk Order Tertentu (dengan preview)
```bash
php artisan tanda-terima:update-penerima --order-id=123 --dry-run
```

### 4. Update Tanda Terima untuk Order Tertentu (langsung update)
```bash
php artisan tanda-terima:update-penerima --order-id=123
```

## Opsi Command

| Opsi | Deskripsi |
|------|-----------|
| `--order-id=ID` | Update hanya untuk order dengan ID tertentu |
| `--all` | Update semua tanda terima yang terkait dengan order (default) |
| `--dry-run` | Tampilkan preview tanpa melakukan update |

## Proses yang Dilakukan

1. Script akan mencari semua order yang memiliki data penerima (penerima, penerima_id, alamat_penerima, kontak_penerima)
2. Untuk setiap order, script akan:
   - Mengambil data penerima dan alamat dari order
   - Mencari semua surat jalan yang terkait dengan order tersebut
   - Untuk setiap surat jalan, update data tanda terima yang terkait dengan:
     - `penerima` dari order
     - `alamat_penerima` dari order
3. Menampilkan summary hasil update

## Contoh Output

```
=== Update Tanda Terima Penerima ===

Ditemukan 5 order dengan data penerima

Order: ORD-2026-001
  - Penerima: PT. Example Corp
  - Alamat: Jl. Example No. 123, Jakarta
    [Tanda Terima #45 - SJ-2026-001]
      * penerima: 'PT. Old Corp' → 'PT. Example Corp'
      * alamat: 'Jl. Old Address' → 'Jl. Example No. 123, Jakarta'
      ✓ Updated

=== SELESAI ===
Total Order: 5
Total Tanda Terima ditemukan: 12
Total Tanda Terima diupdate: 8
```

## Catatan Penting

- **Selalu gunakan `--dry-run` terlebih dahulu** untuk melihat preview data yang akan diupdate
- Script hanya akan mengupdate data tanda terima yang memiliki perbedaan dengan data order
- Data yang sudah sama tidak akan diupdate ulang
- Semua proses menggunakan database transaction, jika terjadi error maka semua perubahan akan di-rollback
- Script akan menampilkan detail perubahan untuk setiap tanda terima

## Troubleshooting

### Command tidak ditemukan
Jalankan command berikut untuk clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```

### Error saat menjalankan
Periksa log error di `storage/logs/laravel.log`
