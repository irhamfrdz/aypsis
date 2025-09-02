# Fitur Pencarian Grup Kontainer

## 📋 Overview

Fitur pencarian grup memungkinkan Anda untuk mencari nomor kontainer tertentu dan secara otomatis menampilkan **semua kontainer dalam grup yang sama**. Ini sangat berguna untuk melihat kontainer-kontainer yang terkait dalam satu tagihan atau periode yang sama.

## ✨ Fitur Utama

### 1. **Pencarian Otomatis Grup**

-   Ketika Anda mencari nomor kontainer, sistem akan:
    -   ✅ Mencari kontainer yang dimaksud
    -   ✅ Mengidentifikasi grup yang terkait
    -   ✅ Menampilkan **SEMUA** kontainer dalam grup tersebut
    -   ✅ Mengurutkan berdasarkan nomor kontainer dan periode

### 2. **Visual Indicators**

-   🔵 **Mode Pencarian Grup**: Banner biru menunjukkan bahwa pencarian dalam mode grup
-   🟡 **Highlight Container**: Container yang dicari diberi highlight warna kuning/orange
-   📍 **Label "Container yang dicari"**: Untuk mengidentifikasi container target

### 3. **Informasi Detail**

-   Total periode dalam grup
-   Jumlah kontainer unik dalam grup
-   Kode grup yang sedang ditampilkan
-   Link untuk clear pencarian

## 🎯 Cara Penggunaan

### Langkah 1: Masukkan Nomor Container

```
Contoh: CBHU5911444
```

### Langkah 2: Sistem Mendeteksi Grup

```
Container CBHU5911444 → Grup: TK125010000004
```

### Langkah 3: Menampilkan Semua Container dalam Grup

```
Hasil: Menampilkan 19 periode dari 4 kontainer dalam grup "TK125010000004"
- CBHU3952697 (periode 1, 2, 3)
- CBHU5911444 (periode 1, 2, 3, 4, 5, 6, 7, 8) ← Container yang dicari
- CSLU1247770 (periode 1, 2, 3, 4, 5)
- CXDU1108080 (periode 1, 2, 3)
```

## 🔧 Technical Implementation

### Controller Logic (`DaftarTagihanKontainerSewaController.php`)

```php
// Handle search functionality with group-based search
if ($request->filled('q')) {
    $searchTerm = $request->input('q');

    // First, check if search term matches a container number
    $foundContainer = DaftarTagihanKontainerSewa::where('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')->first();

    if ($foundContainer && $foundContainer->group) {
        // If container found and has a group, search by that group
        $query->where('group', $foundContainer->group);
    } else {
        // Otherwise, do regular search
        $query->where(function ($q) use ($searchTerm) {
            $q->where('vendor', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('nomor_kontainer', 'LIKE', '%' . $searchTerm . '%')
              ->orWhere('group', 'LIKE', '%' . $searchTerm . '%');
        });
    }
}
```

### View Features (`index.blade.php`)

1. **Smart Placeholder**: Menjelaskan fungsi pencarian grup
2. **Group Search Banner**: Indikator mode pencarian grup
3. **Container Highlighting**: Highlight container yang dicari
4. **Enhanced Results Info**: Informasi detail tentang grup

## 📊 Contoh Hasil Pencarian

### Pencarian: "CBHU5911444"

```
🔵 Mode Pencarian Grup: Menampilkan semua kontainer dalam grup "TK125010000004" yang terkait dengan "CBHU5911444"

📊 Ditemukan 19 periode dari 4 kontainer dalam grup "TK125010000004"

Tabel hasil:
┌─────────────┬─────────┬─────────┬──────────────────────────────┐
│ Container   │ Vendor  │ Periode │ Masa                         │
├─────────────┼─────────┼─────────┼──────────────────────────────┤
│ CBHU3952697 │ DPE     │ 1       │ 24 januari - 23 februari     │
│ CBHU3952697 │ DPE     │ 2       │ 24 februari - 23 maret       │
│ CBHU3952697 │ DPE     │ 3       │ 24 maret - 23 april          │
│ 🟡CBHU5911444│ DPE     │ 1       │ 24 januari - 23 februari     │ ← Dicari
│ 🟡CBHU5911444│ DPE     │ 2       │ 24 februari - 23 maret       │ ← Dicari
│ ...         │ ...     │ ...     │ ...                          │
└─────────────┴─────────┴─────────┴──────────────────────────────┘
```

## 🎨 Visual Elements

### Container Normal

```
┌─────────────────────────┐
│   📦 CBHU3952697       │
│   (Normal container)    │
└─────────────────────────┘
```

### Container yang Dicari

```
┌─────────────────────────┐
│ 🟡 📦 CBHU5911444      │
│ 📍 Container yang dicari │
└─────────────────────────┘
```

### Mode Pencarian Grup Banner

```
┌──────────────────────────────────────────────────┐
│ 👥 Mode Pencarian Grup:                          │
│ Menampilkan semua kontainer dalam grup           │
│ "TK125010000004" yang terkait dengan             │
│ "CBHU5911444"                               [✕]  │
└──────────────────────────────────────────────────┘
```

## 🔄 Fallback Behavior

Jika pencarian tidak cocok dengan nomor kontainer atau kontainer tidak memiliki grup:

-   ✅ Sistem akan melakukan pencarian reguler
-   ✅ Mencari di vendor, nomor_kontainer, dan group
-   ✅ Tidak ada mode grup, tampilan normal

## 🚀 Benefits

1. **Efisiensi**: Satu pencarian untuk melihat semua kontainer terkait
2. **Konteks**: Memahami hubungan antar kontainer dalam grup
3. **Visual**: Mudah mengidentifikasi container target dan grup
4. **Intuitif**: Placeholder menjelaskan fungsi dengan jelas
5. **Fleksibel**: Fallback ke pencarian reguler jika diperlukan

## 📝 Test Cases

### ✅ Test Case 1: Container dengan Grup

```
Input: "CBHU5911444"
Expected: Menampilkan 4 kontainer dalam grup TK125010000004
Result: ✅ PASS
```

### ✅ Test Case 2: Vendor Name

```
Input: "DPE"
Expected: Pencarian reguler di vendor, nomor kontainer, grup
Result: ✅ PASS (fallback ke regular search)
```

### ✅ Test Case 3: Group Code

```
Input: "TK125"
Expected: Pencarian reguler mencari semua yang mengandung "TK125"
Result: ✅ PASS (fallback ke regular search)
```

## 🔧 Maintenance

Fitur ini terintegrasi dengan:

-   ✅ Filter existing (vendor, size, tarif, status)
-   ✅ Pagination system
-   ✅ Sort ordering (nomor_kontainer → periode)
-   ✅ URL parameters
-   ✅ Session persistence

Tidak ada maintenance khusus diperlukan, sistem akan otomatis bekerja dengan data grup yang ada.
