# Container Billing Control (Project 03)

Modul dibuka dari menu **Container Billing Control** AYPSIS atau `/container-billing` setelah login. Permission: `container-billing-manage`; migrasi memberikan akses kepada user ID 1. Pengguna `kiky` mengikuti bypass permission AYPSIS yang sudah ada. Berikan permission tersebut kepada operator melalui manajemen permission AYPSIS.

## Instalasi

Database dasar AYPSIS harus sudah tersedia (termasuk tabel users, permissions, dan user_permissions). Migrasi ini hanya menambahkan modul; bukan pengganti instalasi/restore database AYPSIS. Jika tabel dasar belum ada, migrasi berhenti sebelum membuat tabel modul.

```sh
php artisan migrate --path=database/migrations/2026_09_25_100000_create_container_billing_tables.php
php artisan test --filter=ContainerBillingTest
node tests/container-billing-storage.mjs
node tests/container-billing-boot.mjs
```

Halaman pertama menginisialisasi baseline bawaan project 03 beserta migrasi internal versi aslinya. Untuk menggunakan data operasional terakhir, pilih JSON yang sesuai melalui menu Cadangkan / Pulihkan asli. File backup bertanggal tidak otomatis dipilih karena ada beberapa versi berbeda. Database browser project 03 tidak dapat dibaca otomatis dari origin AYPSIS; ekspor JSON dari aplikasi asal lalu restore di modul ini.

## Implementasi

- Laravel menangani halaman Blade, login/middleware AYPSIS, permission, validasi struktur record, transaksi database, dan kontrol revisi untuk mencegah penimpaan data dari tab/operator dengan snapshot lama (HTTP 409; muat ulang).
- HTML/CSS dan mesin aturan bisnis JavaScript V7.1.38 dipertahankan. Perhitungan, validasi bisnis, approval, pembayaran, dan tampilan tetap memakai kode asli; aturan bisnis tersebut belum dipindah menjadi mesin PHP. Endpoint penyimpanan hanya untuk operator tepercaya dengan permission kelola penuh, termasuk restore/reset.
- Satu bug inisialisasi sumber diperbaiki: `normalizeTakeBasedCycles()` membaca `masters[container]` yang tidak didefinisikan. Integrasi memakai `selectMasterVersion(masterList, container, start)` dengan data master yang sudah dimuat dan pemilih versi asli.
- `container_billing_records` menyimpan sebelas koleksi asli sebagai record JSON lossless: master (termasuk vendor), tarif, rental, expected, invoice, detached invoice, payment, pranota, audit, settings, dan rental overrides. Field bersarang dan evidence historis tidak dibuang. `container_billing_revisions` menyimpan revisi global; `updated_by` mencatat operator terakhir.
- Tabel modul dipisahkan dari transaksi sewa AYPSIS lama karena definisi siklus/ID dan evidence project 03 berbeda. Tidak ada penggabungan otomatis terhadap invoice atau pembayaran AYPSIS lama.
- Restore seluruh snapshot dan inisialisasi dilakukan dalam satu transaksi database. Operasi batch juga atomik. Alur pengguna yang terdiri dari beberapa operasi masih mengikuti urutan asli; bukan satu transaksi bisnis server menyeluruh. Kegagalan koneksi menghentikan penulisan selanjutnya sampai halaman dimuat ulang.
- Data server dipakai bersama oleh operator; tampilan merupakan snapshot saat halaman dibuka. Muat ulang untuk melihat perubahan operator lain. Filter dan snapshot analisis lokal tetap mengikuti perilaku asli.
- Aset baseline hanya dapat diakses setelah login dan permission, tidak diletakkan pada folder publik.
- Sesuaikan batas `post_max_size` PHP dan batas request web server untuk ukuran JSON backup terbesar (misalnya 64M). Restore terlalu besar ditolak dan tidak mengganti data lama.

## Pembaruan sumber

Sumber `03/` tidak diubah. Konversi mekanis yang dapat diulang:

```sh
node scripts/build-container-billing.mjs
```

Script menyalin aset ke `resources/container-billing/`, menghasilkan Blade, mengganti helper IndexedDB dengan adapter Laravel, dan menambahkan commit inisialisasi. Tampilan tidak dibungkus layout AYPSIS agar style dan ukuran aslinya tetap sama. Menu AYPSIS membukanya di tab baru.
