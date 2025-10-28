# Update: Pembulatan Total Volume LCL

## Ringkasan Perubahan
Total volume pada sistem LCL telah diperbarui untuk menggunakan format yang lebih rapi dengan pembulatan 3 angka desimal, menggantikan 6 angka desimal sebelumnya.

## Perubahan Format

### Sebelum:
- Total Volume: `1000.000000 m³` (6 desimal)
- Display yang terlalu panjang dan sulit dibaca

### Sesudah:
- Total Volume: `1000.000 m³` (3 desimal)
- Display lebih rapi dan mudah dibaca

## File yang Diubah

### 1. create-lcl.blade.php
- **JavaScript function `calculateTotals()`**:
  - `totalVolume.toFixed(6)` → `totalVolume.toFixed(3)`
- **Summary display HTML**:
  - `0.000000 m³` → `0.000 m³`
- **Hidden field value**:
  - `totalVolume.toFixed(6)` → `totalVolume.toFixed(3)`

### 2. show-lcl.blade.php
- **Total summary**:
  - `number_format($tandaTerima->items->sum('meter_kubik'), 6)` → `number_format($tandaTerima->items->sum('meter_kubik'), 3)`

## Catatan Penting

### Volume Individual Item
- Volume per item tetap menggunakan 6 desimal untuk presisi perhitungan
- Hanya **total volume** yang dibulatkan menjadi 3 desimal untuk display

### Contoh Tampilan
```
Item 1: 12.345678 m³ (presisi tinggi)
Item 2: 8.765432 m³  (presisi tinggi)
--------------------------------
Total: 21.111 m³     (dibulatkan untuk display)
```

## Alasan Perubahan
1. **Keterbacaan**: Format dengan 3 desimal lebih mudah dibaca
2. **Praktis**: Untuk kontainer, presisi 3 desimal sudah sangat memadai
3. **Konsistensi UI**: Mengurangi panjang angka yang ditampilkan di interface
4. **Standar industri**: 3 desimal adalah standar umum untuk volume kontainer

## Impact
- ✅ UI lebih rapi dan profesional
- ✅ Angka lebih mudah dibaca dan dipahami
- ✅ Tidak mengurangi akurasi perhitungan internal
- ✅ Data dalam database tetap presisi tinggi

## Tanggal Update
27 Oktober 2025

---
**Catatan**: Perubahan ini hanya mempengaruhi tampilan (display), perhitungan internal dan penyimpanan data tetap menggunakan presisi tinggi.