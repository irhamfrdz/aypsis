# Tagihan CAT Permissions Seeder

Seeder ini digunakan untuk menambahkan permissions yang diperlukan untuk fitur Tagihan CAT (Container Annual Test).

## Permissions yang akan ditambahkan:

### Main Permissions:

-   `tagihan-cat` - Akses Tagihan CAT
-   `tagihan-cat.view` - Melihat Daftar Tagihan CAT
-   `tagihan-cat.create` - Membuat Tagihan CAT Baru
-   `tagihan-cat.update` - Mengupdate Data Tagihan CAT
-   `tagihan-cat.delete` - Menghapus Data Tagihan CAT

### Route-based Permissions:

-   `tagihan-cat.index` - Index Tagihan CAT
-   `tagihan-cat.create` - Create Tagihan CAT
-   `tagihan-cat.store` - Store Tagihan CAT
-   `tagihan-cat.show` - Show Tagihan CAT
-   `tagihan-cat.edit` - Edit Tagihan CAT
-   `tagihan-cat.update` - Update Tagihan CAT
-   `tagihan-cat.destroy` - Destroy Tagihan CAT

### Additional Permissions:

-   `tagihan-cat.print` - Print Tagihan CAT
-   `tagihan-cat.export` - Export Tagihan CAT
-   `tagihan-cat.import` - Import Tagihan CAT

## Cara menjalankan di server:

### Opsi 1: Menggunakan script khusus (Direkomendasikan)

```bash
php run_tagihan_cat_permissions_seeder.php
```

### Opsi 2: Menggunakan artisan command

```bash
php artisan db:seed --class=TagihanCatPermissionsSeeder
```

### Opsi 3: Jalankan semua seeder (jika belum pernah dijalankan)

```bash
php artisan db:seed
```

## Yang dilakukan seeder:

1. **Membuat permissions** jika belum ada
2. **Assign ke role admin** jika role tersebut ada
3. **Assign ke user admin** jika user tersebut ada
4. **Menampilkan informasi** tentang apa yang telah dilakukan

## Catatan:

-   Seeder ini aman dijalankan berulang kali karena akan melewati permissions yang sudah ada
-   Permissions akan otomatis di-assign ke user admin jika ada
-   Tidak akan menghapus permissions yang sudah ada sebelumnya
