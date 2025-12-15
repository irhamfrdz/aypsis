# Fix Migration Error - Tabel Sudah Ada

## Error yang Muncul
```
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'tanda_terimas_lcl' already exists
```

## Penyebab
Tabel `tanda_terimas_lcl` sudah ada di database server, tapi ada migration file yang mencoba membuat tabel tersebut lagi.

## Solusi

### Opsi 1: Manual Insert ke Tabel Migrations (RECOMMENDED)

Jalankan query SQL berikut di database server untuk menandai migration sebagai sudah dijalankan:

```sql
-- Cek migration mana yang belum dijalankan
SELECT * FROM migrations ORDER BY id DESC LIMIT 10;

-- Insert migration yang error ke tabel migrations (sesuaikan nama file dan batch number)
INSERT INTO migrations (migration, batch) VALUES 
('2025_12_15_144324_add_container_fields_to_tanda_terimas_lcl_table', (SELECT MAX(batch) FROM migrations) + 1),
('2025_12_15_160000_add_single_penerima_pengirim_to_tanda_terimas_lcl_table', (SELECT MAX(batch) FROM migrations) + 1),
('2025_12_15_144001_drop_unused_pivot_tables_tanda_terima_lcl', (SELECT MAX(batch) FROM migrations) + 1),
('2025_12_15_144932_create_tanda_terima_lcl_kontainer_pivot_table', (SELECT MAX(batch) FROM migrations) + 1);
```

**ATAU** jika batch number tidak bisa nested query:

```sql
-- Cek batch number terakhir
SELECT MAX(batch) as last_batch FROM migrations;

-- Misal hasilnya 25, maka gunakan 26
INSERT INTO migrations (migration, batch) VALUES 
('2025_12_15_144324_add_container_fields_to_tanda_terimas_lcl_table', 26),
('2025_12_15_160000_add_single_penerima_pengirim_to_tanda_terimas_lcl_table', 26),
('2025_12_15_144001_drop_unused_pivot_tables_tanda_terima_lcl', 26),
('2025_12_15_144932_create_tanda_terima_lcl_kontainer_pivot_table', 26);
```

### Opsi 2: Hapus Migration File yang Bermasalah (TIDAK DISARANKAN)

Jika migration yang error adalah migration lama yang mencoba create table, hapus file migration tersebut dari folder `database/migrations/` di server.

### Opsi 3: Rollback dan Migrate Ulang (BERISIKO)

```bash
# HATI-HATI: Ini akan menghapus data!
php artisan migrate:rollback --step=5
php artisan migrate
```

## Setelah Fix

Setelah menjalankan salah satu opsi di atas, coba jalankan lagi:

```bash
php artisan migrate
```

## Verifikasi

Pastikan semua kolom ada di tabel:

```sql
DESCRIBE tanda_terimas_lcl;
```

Kolom yang harus ada:
- `id`
- `nomor_tanda_terima`
- `tanggal_tanda_terima`
- `nama_penerima`, `pic_penerima`, `telepon_penerima`, `alamat_penerima`
- `nama_pengirim`, `pic_pengirim`, `telepon_pengirim`, `alamat_pengirim`
- `nomor_kontainer`, `size_kontainer`, `tipe_kontainer`
- `tujuan_pengiriman_id`
- `created_at`, `updated_at`

Dan pastikan tabel pivot ada:

```sql
SHOW TABLES LIKE '%tanda_terima_lcl%';
```

Harus ada:
- `tanda_terimas_lcl`
- `tanda_terima_lcl_items`
- `tanda_terima_lcl_kontainer_pivot`
