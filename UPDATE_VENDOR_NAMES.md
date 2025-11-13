# Update Vendor Names Scripts

Dokumentasi untuk script update vendor names di database.

## 📋 Deskripsi

Script ini digunakan untuk mengubah nama vendor dari nama lengkap perusahaan menjadi kode singkat:

-   `PT. DEPO PETIKEMAS EXPRESSINDO` → `DPE`
-   `PT. ZONA LINTAS SAMUDERA` → `ZONA`

## 🛠️ Cara Penggunaan

### Metode 1: Menggunakan Artisan Command (Recommended)

#### Lihat semua perubahan yang akan dilakukan (Dry Run)

```bash
php artisan vendor:update-names --dry-run
```

#### Update semua tabel

```bash
php artisan vendor:update-names
```

#### Update tabel tertentu saja

```bash
php artisan vendor:update-names --table=kontainers
```

#### Kombinasi dry-run dan tabel tertentu

```bash
php artisan vendor:update-names --dry-run --table=kontainers
```

### Metode 2: Menggunakan PHP Script Langsung

```bash
php update_vendor_names.php
```

## 📊 Tabel yang Akan Diupdate

Script akan memproses tabel-tabel berikut (jika ada):

1. `kontainers`
2. `tagihan_kontainer_sewa`
3. `pranota_tagihan_kontainers`

## ⚙️ Fitur

### Artisan Command

-   ✅ **Dry Run Mode**: Melihat preview tanpa mengubah data
-   ✅ **Table Specific**: Update tabel tertentu saja
-   ✅ **Transaction Safe**: Menggunakan database transaction
-   ✅ **Error Handling**: Auto rollback jika terjadi error
-   ✅ **Summary Report**: Menampilkan ringkasan hasil update
-   ✅ **Colored Output**: Output dengan warna untuk kemudahan membaca

### PHP Script

-   ✅ **Standalone**: Dapat dijalankan tanpa Artisan
-   ✅ **Transaction Safe**: Menggunakan database transaction
-   ✅ **Error Handling**: Auto rollback jika terjadi error
-   ✅ **Detailed Output**: Output detail untuk setiap tabel

## 🔒 Keamanan

-   Script menggunakan **database transaction** untuk memastikan data consistency
-   Jika terjadi error, semua perubahan akan di-**rollback**
-   Gunakan **--dry-run** untuk preview sebelum melakukan perubahan actual

## 📝 Output Example

### Dry Run Output

```
=================================================
  UPDATE VENDOR NAMES
=================================================

🔍 DRY RUN MODE - No changes will be made

Processing table: kontainers
-----------------------------------
  ✓ 'PT. DEPO PETIKEMAS EXPRESSINDO' → 'DPE': 13 records
  ✓ 'PT. ZONA LINTAS SAMUDERA' → 'ZONA': 643 records
  Subtotal: 656 records

=================================================
✓ DRY RUN COMPLETE! Would update: 656 records
=================================================
```

### Actual Run Output

```
=================================================
  UPDATE VENDOR NAMES
=================================================

Processing table: kontainers
-----------------------------------
  ✓ 'PT. DEPO PETIKEMAS EXPRESSINDO' → 'DPE': 13 records
  ✓ 'PT. ZONA LINTAS SAMUDERA' → 'ZONA': 643 records
  Subtotal: 656 records

=================================================
✓ SUCCESS! Total records updated: 656
=================================================

Final Vendor Summary in kontainers table:
-----------------------------------
+---------+---------------+
| Vendor  | Total Records |
+---------+---------------+
| DPE     | 13            |
| ZONA    | 643           |
+---------+---------------+
```

## ⚠️ Peringatan

-   **BACKUP DATABASE** sebelum menjalankan script
-   Gunakan **--dry-run** terlebih dahulu untuk melihat perubahan yang akan dilakukan
-   Script ini akan mengubah data secara permanen (kecuali dry-run mode)

## 🔄 Rollback

Jika perlu rollback manual, gunakan query berikut:

```sql
-- Rollback DPE
UPDATE kontainers SET vendor = 'PT. DEPO PETIKEMAS EXPRESSINDO' WHERE vendor = 'DPE';

-- Rollback ZONA
UPDATE kontainers SET vendor = 'PT. ZONA LINTAS SAMUDERA' WHERE vendor = 'ZONA';
```

## 📞 Support

Jika terjadi masalah atau pertanyaan, hubungi tim development.

---

**Last Updated**: November 12, 2025
