# PRANOTA SURAT JALAN - DISABLED

## Status: DEACTIVATED ❌

Pranota Surat Jalan telah dinonaktifkan dan digantikan dengan **Pranota Uang Jalan**.

## Perubahan yang Dilakukan

### 1. **Routes - DISABLED**
- ❌ `Route::prefix('pranota-surat-jalan')` → Dinonaktifkan
- ✅ `Route::resource('pranota-uang-jalan')` → Menggantikan fungsionalitas

### 2. **Controller - RENAMED & REPURPOSED** 
- ❌ `PranotaSuratJalanController.php` → Dibackup ke `PranotaSuratJalanController-BACKUP-DISABLED.php`
- ✅ `PranotaSuratJalanController.php` → Sekarang menangani **pranota uang jalan**

### 3. **Model - DISABLED**
- ❌ `PranotaSuratJalan.php` → Diubah ke `PranotaSuratJalan-DISABLED.php` 
- ✅ `PranotaUangJalan.php` → Model baru untuk uang jalan

### 4. **Views - DISABLED**
- ❌ `resources/views/pranota-surat-jalan/` → Diubah ke `pranota-surat-jalan-DISABLED/`
- ✅ `resources/views/pranota-uang-jalan/` → Views baru untuk uang jalan

### 5. **Navigation Menu - UPDATED**
- ❌ Menu "Pranota Surat Jalan" → Dihapus dari sidebar
- ✅ Menu "Pranota Uang Jalan" → Ditambahkan ke sidebar

## Alasan Perubahan

1. **Workflow Lebih Logis**: Sekarang pranota dibuat berdasarkan uang jalan yang sudah dibuat, bukan dari surat jalan langsung
2. **Status Management**: Status uang jalan dikelola dengan baik dari `belum_dibayar` → `sudah_masuk_pranota` → `lunas`
3. **Data Consistency**: Eliminasi duplikasi data dan konflik status

## File yang Masih Ada (untuk Recovery)

```
- app/Http/Controllers/PranotaSuratJalanController-BACKUP-DISABLED.php
- app/Models/PranotaSuratJalan-DISABLED.php  
- resources/views/pranota-surat-jalan-DISABLED/
```

## URL Baru

- Old: `/pranota-surat-jalan` ❌ 
- New: `/pranota-uang-jalan` ✅

## Permissions Baru

- `pranota-uang-jalan-view`
- `pranota-uang-jalan-create` 
- `pranota-uang-jalan-update`
- `pranota-uang-jalan-delete`

---
**Tanggal Perubahan**: 7 November 2025  
**Status**: Selesai ✅  
**Recovery**: File backup tersedia jika diperlukan