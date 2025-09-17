# Master Divisi Seeder

Seeder ini digunakan untuk mengisi data master divisi/divisi ke dalam database aplikasi Aypsis.

## Struktur Data

Seeder ini akan membuat divisi/divisi berikut:

1. **IT (Information Technology)** - Kode: `IT`

    - Mengelola sistem IT, pengembangan software, dan infrastruktur teknologi

2. **Finance** - Kode: `FIN`

    - Mengelola keuangan perusahaan, akuntansi, dan pelaporan keuangan

3. **Operations** - Kode: `OPS`

    - Mengelola operasi harian perusahaan dan koordinasi kegiatan

4. **Human Resources** - Kode: `HR`

    - Mengelola rekrutmen, pengembangan karyawan, dan administrasi personalia

5. **ABK (Anak Buah Kapal)** - Kode: `ABK`

    - Mengelola kru kapal dan operasi pelayaran

6. **Admin** - Kode: `ADM`

    - Mengelola administrasi umum dan dokumentasi perusahaan

7. **Marketing** - Kode: `MKT`

    - Mengelola promosi, penjualan, dan hubungan dengan pelanggan

8. **Procurement** - Kode: `PRC`

    - Mengelola pembelian, supplier, dan rantai pasok

9. **Quality Control** - Kode: `QC`

    - Memastikan standar kualitas produk dan layanan

10. **Maintenance** - Kode: `MNT`

    - Mengelola perawatan dan perbaikan aset perusahaan

11. **Legal** - Kode: `LEG`

    - Mengelola aspek legal perusahaan dan kontrak

12. **Security** - Kode: `SEC`
    - Mengelola keamanan perusahaan dan aset

## Cara Menjalankan Seeder

### 1. Jalankan Semua Seeder (Direkomendasikan)

```bash
php artisan db:seed
```

### 2. Jalankan Hanya Divisi Seeder

```bash
php artisan db:seed --class=DivisiSeeder
```

### 3. Jalankan dengan Force (Jika perlu menimpa data)

```bash
php artisan db:seed --class=DivisiSeeder --force
```

### 4. Jalankan Bersama Migration (Fresh Install)

```bash
php artisan migrate:fresh --seed
```

## Fitur Seeder

-   **Duplicate Protection**: Seeder akan melewati divisi yang sudah ada berdasarkan kode_divisi atau nama_divisi
-   **Detailed Logging**: Menampilkan informasi detail tentang divisi yang dibuat atau dilewati
-   **Summary Report**: Menampilkan ringkasan hasil seeding
-   **Safe Execution**: Tidak akan menimpa data yang sudah ada

## Verifikasi Hasil

Setelah menjalankan seeder, Anda dapat memverifikasi dengan:

### 1. Melalui Web Interface

-   Login ke aplikasi
-   Akses menu **Master → Divisi**
-   Pastikan semua divisi sudah terdaftar

### 2. Melalui Database

```sql
SELECT id, nama_divisi, kode_divisi, is_active FROM divisis ORDER BY nama_divisi;
```

### 3. Melalui Artisan Command

```bash
php artisan tinker
```

```php
App\Models\Divisi::count(); // Total divisi
App\Models\Divisi::where('is_active', true)->count(); // Divisi aktif
```

## Troubleshooting

### Error: "Class DivisiSeeder not found"

Pastikan file `DivisiSeeder.php` sudah ada di folder `database/seeders/`

### Error: "Table divisis doesn't exist"

Jalankan migration terlebih dahulu:

```bash
php artisan migrate
```

### Error: "Duplicate entry"

Seeder sudah dilengkapi dengan protection untuk mencegah duplikasi data

## Catatan

-   Semua divisi akan dibuat dengan status `is_active = true`
-   Kode divisi menggunakan format singkatan yang konsisten
-   Deskripsi divisi disediakan untuk memberikan pemahaman fungsi masing-masing divisi
-   Seeder ini aman untuk dijalankan berulang kali

## Support

Jika mengalami masalah dengan seeder ini, periksa:

1. File `DivisiSeeder.php` sudah ada dan tidak ada syntax error
2. Model `Divisi` sudah terdefinisi dengan benar
3. Migration `create_divisis_table` sudah dijalankan
4. Database connection sudah dikonfigurasi dengan benar
