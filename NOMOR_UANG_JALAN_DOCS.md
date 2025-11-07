# Nomor Uang Jalan System Documentation

## Format

Nomor Uang Jalan menggunakan format: **UJ + 2digit bulan + 2digit tahun + 6digit running number**

### Contoh:

-   `UJ1125000001` = UJ + November 2025 + Running number 000001
-   `UJ1125000002` = UJ + November 2025 + Running number 000002
-   `UJ1225000003` = UJ + Desember 2025 + Running number 000003
-   `UJ0126000004` = UJ + Januari 2026 + Running number 000004

## Fitur Utama

### 1. Running Number Tidak Reset Bulanan

-   Running number terus bertambah secara berurutan
-   Tidak direset ketika ganti bulan/tahun
-   Memastikan setiap nomor uang jalan unik sepanjang masa

### 2. Otomatis Generate

-   Sistem otomatis generate nomor ketika form create dibuka
-   Field nomor uang jalan menjadi readonly
-   User tidak perlu input manual

### 3. Format Prefix Berubah Sesuai Bulan/Tahun

-   Bagian bulan/tahun berubah otomatis sesuai tanggal sistem
-   Running number tetap kontinyu

## Implementasi

### Model (UangJalan.php)

```php
public static function generateNomorUangJalan()
{
    // Logic untuk generate nomor dengan running number kontinyu
}
```

### Controller (UangJalanController.php)

```php
// Auto generate di method create()
$nomorUangJalan = UangJalan::generateNomorUangJalan();

// Auto assign di method store()
$nomorUangJalan = $request->nomor_uang_jalan ?: UangJalan::generateNomorUangJalan();
```

### View (create.blade.php)

```html
<!-- Field readonly dengan nilai auto-generated -->
<input
    type="text"
    name="nomor_uang_jalan"
    value="{{ $nomorUangJalan }}"
    readonly
/>
```

## Database

-   Field `nomor_uang_jalan` ditambahkan sebagai string(50) unique
-   Migration telah dijalankan untuk menambahkan field baru beserta field lainnya
