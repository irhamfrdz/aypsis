# Setup Permission Invoice Aktivitas Lain

## Cara Menjalankan

### Opsi 1: Menggunakan PHP Script (Recommended)
```bash
php add_invoice_aktivitas_lain_permissions.php
```

### Opsi 2: Menggunakan Artisan Seeder
```bash
php artisan db:seed --class=AddInvoiceAktivitasLainPermissionsSeeder
```

### Opsi 3: Menggunakan PowerShell
```powershell
php add_invoice_aktivitas_lain_permissions.php
```

## Permissions yang Ditambahkan

Script ini akan menambahkan 4 permissions berikut ke database:

1. **invoice-aktivitas-lain-view** - Untuk melihat/view invoice aktivitas lain
2. **invoice-aktivitas-lain-create** - Untuk membuat invoice aktivitas lain baru
3. **invoice-aktivitas-lain-update** - Untuk mengedit invoice aktivitas lain
4. **invoice-aktivitas-lain-delete** - Untuk menghapus invoice aktivitas lain

## Automatic Admin Assignment

Script akan secara otomatis menambahkan semua permissions ini ke role **admin** jika role tersebut ditemukan di database.

## Mengatur Permission di User Management

Setelah menjalankan script, Anda dapat mengatur permission untuk user melalui:

1. Login sebagai admin
2. Buka menu **Master User** > **Data User**
3. Klik **Edit** pada user yang ingin diatur permissionnya
4. Scroll ke section **Aktivitas Lain-lain**
5. Expand module tersebut dengan klik icon ▶
6. Centang checkbox untuk **Invoice Aktivitas Lain** sesuai kebutuhan:
   - ✓ View - Untuk melihat daftar dan detail invoice
   - ✓ Create - Untuk membuat invoice baru
   - ✓ Update - Untuk mengedit invoice
   - ✓ Delete - Untuk menghapus invoice
7. Klik **Perbarui** untuk menyimpan

## Troubleshooting

Jika terjadi error saat menjalankan script:

1. Pastikan Anda berada di root directory project
2. Pastikan file `.env` sudah dikonfigurasi dengan benar
3. Pastikan database sudah running
4. Cek apakah package `spatie/laravel-permission` sudah terinstall

## Verifikasi

Untuk memverifikasi permission sudah berhasil ditambahkan:

```sql
SELECT * FROM permissions WHERE name LIKE 'invoice-aktivitas-lain%';
```

Atau melalui Laravel Tinker:
```bash
php artisan tinker
>>> \Spatie\Permission\Models\Permission::where('name', 'like', 'invoice-aktivitas-lain%')->get()
```
