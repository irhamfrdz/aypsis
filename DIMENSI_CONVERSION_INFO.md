# Perubahan Satuan Dimensi LCL - Dari Centimeter ke Meter

## Ringkasan Perubahan
Sistem LCL (Less Container Load) telah diperbarui untuk menggunakan satuan meter (m) sebagai input dimensi, menggantikan centimeter (cm) sebelumnya. Perubahan ini dilakukan untuk:

1. **Konsistensi perhitungan**: Volume dihitung langsung dalam meter kubik (m³)
2. **Kemudahan input**: User memasukkan dimensi dalam meter yang lebih logis untuk kontainer
3. **Akurasi perhitungan**: Eliminasi pembagian dengan 1.000.000 untuk konversi cm³ ke m³

## File yang Diubah

### 1. Views/Forms
- `create-lcl.blade.php`: Label dan perhitungan JavaScript diubah dari cm ke m
- `edit-lcl.blade.php`: Label dan perhitungan JavaScript diubah dari cm ke m  
- `show-lcl.blade.php`: Header tabel diubah dari cm ke m

### 2. JavaScript Calculation
**Sebelum:**
```javascript
volume = (panjang * lebar * tinggi) / 1000000; // cm³ to m³
```

**Sesudah:**
```javascript
volume = panjang * lebar * tinggi; // m × m × m = m³
```

### 3. Database Migration
File: `2025_10_27_151528_update_lcl_dimensions_from_cm_to_meters.php`
- Konversi data lama dari cm ke m (dengan asumsi nilai > 10 adalah cm)
- Update comment kolom database dari "cm" ke "meters"

## Konversi Data Otomatis

Migrasi akan otomatis mengkonversi data yang ada dengan logika:
- **Jika dimensi > 10**: Dianggap dalam cm, dibagi 100 untuk konversi ke meter
- **Jika dimensi ≤ 10**: Dianggap sudah dalam meter, tidak diubah
- **Volume**: Dihitung ulang berdasarkan dimensi yang sudah dikonversi

## Panduan untuk User

### Input Dimensi Baru
- **Panjang**: Masukkan dalam meter (contoh: 2.4 untuk 2.4 meter)
- **Lebar**: Masukkan dalam meter (contoh: 1.2 untuk 1.2 meter)  
- **Tinggi**: Masukkan dalam meter (contoh: 2.6 untuk 2.6 meter)

### Contoh Perhitungan
- Panjang: 2.4 m
- Lebar: 1.2 m
- Tinggi: 2.6 m
- **Volume = 2.4 × 1.2 × 2.6 = 7.488 m³**

## Backward Compatibility
- Data lama otomatis dikonversi oleh migrasi
- Sistem tetap dapat menangani data dalam format baru
- Rollback tersedia jika diperlukan dengan `php artisan migrate:rollback`

## Testing
Setelah perubahan:
1. Test input dimensi baru dengan nilai dalam meter
2. Verifikasi perhitungan volume otomatis
3. Cek tampilan data lama sudah terkonversi dengan benar
4. Test fungsi container splitting dengan satuan baru

## Tanggal Implementasi
27 Oktober 2025

---
**Catatan**: Perubahan ini meningkatkan akurasi dan kemudahan penggunaan sistem LCL dengan menggunakan satuan yang lebih logis untuk dimensi kontainer.