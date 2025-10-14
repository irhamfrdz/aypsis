# Perubahan Input Kemasan Surat Jalan - Dokumentasi

## Overview
Mengubah input karton, plastik, dan terpal dari input numerik (jumlah) menjadi dropdown dengan pilihan "Pakai" dan "Tidak Pakai".

## Perubahan yang Dilakukan

### 1. Form Create Surat Jalan (`resources/views/surat-jalan/create.blade.php`)

#### Sebelum:
```html
<!-- Input numerik untuk quantity -->
<input type="number" name="karton" value="{{ old('karton', 0) }}" min="0">
<input type="number" name="plastik" value="{{ old('plastik', 0) }}" min="0">
<input type="number" name="terpal" value="{{ old('terpal', 0) }}" min="0">
```

#### Sesudah:
```html
<!-- Dropdown untuk status pakai/tidak pakai -->
<select name="karton">
    <option value="">Pilih Status Karton</option>
    <option value="pakai">Pakai</option>
    <option value="tidak_pakai">Tidak Pakai</option>
</select>

<select name="plastik">
    <option value="">Pilih Status Plastik</option>
    <option value="pakai">Pakai</option>
    <option value="tidak_pakai">Tidak Pakai</option>
</select>

<select name="terpal">
    <option value="">Pilih Status Terpal</option>
    <option value="pakai">Pakai</option>
    <option value="tidak_pakai">Tidak Pakai</option>
</select>
```

### 2. Controller Validation (`app/Http/Controllers/SuratJalanController.php`)

#### Sebelum:
```php
'karton' => 'nullable|integer|min:0',
'plastik' => 'nullable|integer|min:0',
'terpal' => 'nullable|integer|min:0',
```

#### Sesudah:
```php
'karton' => 'nullable|in:pakai,tidak_pakai',
'plastik' => 'nullable|in:pakai,tidak_pakai',  
'terpal' => 'nullable|in:pakai,tidak_pakai',
```

### 3. Database Migration
**File:** `database/migrations/2025_10_14_111854_change_kemasan_columns_to_string_in_surat_jalans_table.php`

#### Schema Changes:
```php
public function up(): void
{
    Schema::table('surat_jalans', function (Blueprint $table) {
        // Change columns from integer to string
        $table->string('karton')->nullable()->change();
        $table->string('plastik')->nullable()->change();
        $table->string('terpal')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('surat_jalans', function (Blueprint $table) {
        // Revert back to integer
        $table->integer('karton')->nullable()->change();
        $table->integer('plastik')->nullable()->change();
        $table->integer('terpal')->nullable()->change();
    });
}
```

### 4. View Detail Surat Jalan (`resources/views/surat-jalan/show.blade.php`)

#### Sebelum:
```html
<!-- Menampilkan angka quantity -->
<p class="text-sm text-gray-900 font-semibold">{{ number_format($suratJalan->karton) }}</p>
```

#### Sesudah:
```html
<!-- Menampilkan badge status dengan warna -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
    {{ $suratJalan->karton == 'pakai' ? 'bg-green-100 text-green-800' : 
       ($suratJalan->karton == 'tidak_pakai' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
    {{ $suratJalan->karton ? ucwords(str_replace('_', ' ', $suratJalan->karton)) : 'Tidak diset' }}
</span>
```

## Nilai yang Disimpan di Database

### Format Baru:
- **"pakai"** - Jika kemasan digunakan
- **"tidak_pakai"** - Jika kemasan tidak digunakan  
- **NULL** - Jika belum diset

### Visual Indicators:
- 🟢 **Pakai** - Badge hijau
- 🔴 **Tidak Pakai** - Badge merah
- ⚫ **Tidak diset** - Badge abu-abu

## UI/UX Improvements

### 1. **Clarity**: 
- Lebih jelas dari segi konteks - user memilih apakah menggunakan kemasan atau tidak
- Menghilangkan kebingungan tentang jumlah quantity yang tepat

### 2. **Consistency**:
- Semua tipe kemasan memiliki format input yang sama
- Konsisten dengan pola dropdown lainnya di aplikasi

### 3. **Validation**:
- Validasi lebih ketat dengan pilihan terbatas
- Mengurangi kemungkinan input data yang salah

### 4. **Visual Display**:
- Badge berwarna di halaman detail memberikan info visual yang jelas
- Status mudah dipahami dengan sekali lihat

## Migration Commands

```bash
# Generate migration
php artisan make:migration change_kemasan_columns_to_string_in_surat_jalans_table --table=surat_jalans

# Run migration  
php artisan migrate

# Rollback if needed
php artisan migrate:rollback
```

## Testing Scenarios

### 1. **Create New Surat Jalan**
- ✅ Pilih "Pakai" untuk karton → Data tersimpan sebagai "pakai"
- ✅ Pilih "Tidak Pakai" untuk plastik → Data tersimpan sebagai "tidak_pakai"  
- ✅ Biarkan kosong untuk terpal → Data tersimpan sebagai NULL

### 2. **View Detail**
- ✅ Status "pakai" muncul sebagai badge hijau "Pakai"
- ✅ Status "tidak_pakai" muncul sebagai badge merah "Tidak Pakai"
- ✅ Status NULL muncul sebagai badge abu-abu "Tidak diset"

### 3. **Validation**
- ✅ Input "pakai" → Valid
- ✅ Input "tidak_pakai" → Valid
- ✅ Input kosong → Valid (nullable)
- ❌ Input "invalid_value" → Validation Error

## File yang Dimodifikasi

1. `resources/views/surat-jalan/create.blade.php` - Form input
2. `app/Http/Controllers/SuratJalanController.php` - Validation rules (store & update)
3. `database/migrations/2025_10_14_111854_change_kemasan_columns_to_string_in_surat_jalans_table.php` - Schema changes
4. `resources/views/surat-jalan/show.blade.php` - Display format

## Notes

- ⚠️ **Data Migration**: Existing numeric data akan dikonversi ke string. Pastikan backup database sebelum migrate.
- 📝 **Edit Form**: File `edit.blade.php` perlu diupdate jika belum memiliki input kemasan.
- 🎨 **Styling**: Badge colors dapat disesuaikan dengan design system aplikasi.

Perubahan ini meningkatkan user experience dengan memberikan pilihan yang lebih intuitif dan mudah dipahami untuk pengelolaan kemasan dalam surat jalan.