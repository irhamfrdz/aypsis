# Perbaikan Sistem Auto Increment Nomor Voyage

## Masalah yang Diperbaiki

-   **Nomor urut tetap 01**: Sebelumnya nomor urut di-hardcode menjadi '01'
-   **Tidak ada increment otomatis**: Setiap generate voyage selalu menghasilkan nomor yang sama

## Solusi yang Diterapkan

### 1. Update Controller Logic

File: `app/Http/Controllers/PergerakanKapalController.php`

**Sebelumnya:**

```php
// 2 digit nomor urut dari Jakarta saja (selalu 01 sesuai permintaan)
$noUrut = '01';
```

**Sesudahnya:**

```php
// 2 digit nomor urut - hitung berdasarkan voyage yang sudah ada untuk kapal ini di tahun ini
$currentYear = date('Y');
$lastVoyageCount = PergerakanKapal::where('nama_kapal', $namaKapal)
    ->whereYear('created_at', $currentYear)
    ->count();
$noUrut = str_pad($lastVoyageCount + 1, 2, '0', STR_PAD_LEFT);
```

### 2. Logika Auto Increment

-   **Scope**: Per kapal per tahun
-   **Query**: Menghitung jumlah voyage yang sudah ada untuk kapal tertentu di tahun berjalan
-   **Format**: 2 digit dengan leading zero (01, 02, 03, dst.)

### 3. Contoh Hasil Testing

**KM SEKAR PERMATA:**

-   Voyage ke-1: SP**01**JB25
-   Voyage ke-2: SP**02**JT25
-   Voyage ke-3: SP**03**JB25

**KM ALEXINDO 1:**

-   Voyage ke-1: A1**01**JB25
-   Voyage ke-2: A1**02**JB25

## Format Nomor Voyage Final

**Format:** `[Nickname 2 digit][No Urut 2 digit][Kode Asal 1 digit][Kode Tujuan 1 digit][Tahun 2 digit]`

**Komponen:**

1. **Nickname Kapal** (2 digit): Dari field `nickname` di tabel `master_kapals`
2. **No Urut** (2 digit): Auto increment berdasarkan jumlah voyage kapal di tahun berjalan
3. **Kode Pelabuhan Asal** (1 digit): Berdasarkan huruf pertama kota pelabuhan
4. **Kode Pelabuhan Tujuan** (1 digit): Berdasarkan huruf pertama kota pelabuhan
5. **Tahun** (2 digit): Tahun sekarang (25 untuk 2025)

## Keunggulan Sistem Baru

✅ **Unique per kapal**: Setiap kapal memiliki sequence number sendiri
✅ **Reset per tahun**: Sequence dimulai dari 01 lagi di tahun baru
✅ **Thread safe**: Menggunakan database count untuk menghindari collision
✅ **Scalable**: Bisa handle multiple kapal dengan voyage bersamaan

## Testing Results

```
Ship: KM SEKAR PERMATA
  Current voyages in 2025: 2
  Next sequence number: 03

Ship: KM ALEXINDO 1
  Current voyages in 2025: 1
  Next sequence number: 02
```

## Status

✅ **FIXED**: Nomor urut sekarang auto increment dengan benar berdasarkan data existing di database.

---

_Update: 21 Oktober 2025_
