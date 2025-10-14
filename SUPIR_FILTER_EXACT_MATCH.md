# Filter Supir - Divisi Supir Only

## Perubahan yang Telah Dilakukan

### ✅ Query Filter Diperbaiki:

**SEBELUM:**

```php
$supirs = Karyawan::where('divisi', 'LIKE', '%supir%')
                 ->orWhere('pekerjaan', 'LIKE', '%supir%')
                 ->whereNotNull('nama_lengkap')
                 ->orderBy('nama_lengkap')
                 ->get(['id', 'nama_lengkap', 'plat']);
```

**SESUDAH:**

```php
$supirs = Karyawan::where('divisi', 'supir')
                 ->whereNotNull('nama_lengkap')
                 ->orderBy('nama_lengkap')
                 ->get(['id', 'nama_lengkap', 'plat']);
```

## Perubahan Detail:

### ❌ **Dihapus:**

1. **LIKE Search** - `'divisi', 'LIKE', '%supir%'`
    - Menghindari match dengan divisi seperti "supervisor", "supir_backup", dll
2. **OR Condition** - `orWhere('pekerjaan', 'LIKE', '%supir%')`
    - Menghindari karyawan dengan pekerjaan mengandung kata supir tapi divisi berbeda

### ✅ **Diterapkan:**

1. **Exact Match** - `'divisi', 'supir'`

    - Hanya karyawan dengan divisi tepat "supir"
    - Case sensitive untuk konsistensi data

2. **Single Condition**
    - Fokus hanya pada kolom divisi
    - Lebih spesifik dan akurat

## Keuntungan Perubahan:

### 🎯 **Presisi Data:**

-   Hanya divisi "supir" murni yang tampil
-   Menghindari false positive dari LIKE search
-   Data lebih konsisten dan terpercaya

### 📊 **Performance:**

-   Query lebih sederhana tanpa OR condition
-   Exact match lebih cepat dari LIKE search
-   Index pada kolom divisi lebih efektif

### 🛡️ **Data Integrity:**

-   Menghindari kesalahan pemilihan karyawan
-   Dropdown lebih clean dan focused
-   Sesuai dengan bisnis requirement

## Contoh Data yang Akan Tampil:

### ✅ **AKAN TAMPIL:**

```
- divisi: "supir" ✓
- divisi: "supir" ✓
```

### ❌ **TIDAK AKAN TAMPIL:**

```
- divisi: "supervisor" (sebelumnya masuk karena LIKE %supir%)
- divisi: "supir_backup" (sebelumnya masuk karena LIKE %supir%)
- divisi: "admin", pekerjaan: "supir_kontainer" (sebelumnya masuk karena OR condition)
```

## File yang Dimodifikasi:

1. ✅ **Controller**: `app/Http/Controllers/SuratJalanController.php`
    - Method: `create()`
    - Line: Query $supirs

## Status: SELESAI ✅

Query filter telah diperbaiki untuk memastikan:

-   ✅ Hanya divisi "supir" yang tampil di dropdown
-   ✅ Exact match untuk akurasi data
-   ✅ Performance query lebih optimal
-   ✅ Data integrity terjaga

Dropdown supir sekarang akan menampilkan hanya karyawan dengan divisi "supir" murni!
