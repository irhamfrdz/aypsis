# STATUS KONTAINER - DOKUMENTASI LENGKAP

## 📊 **STATUS YANG ADA DI DATABASE SAAT INI**

Berdasarkan data aktual dalam database:

| Status           | Jumlah       | Warna Display                               | Kategori      |
| ---------------- | ------------ | ------------------------------------------- | ------------- |
| **Tersedia**     | 20 kontainer | 🟢 Hijau (`bg-green-100 text-green-800`)    | Ready to use  |
| **Disewa**       | 1 kontainer  | 🟡 Kuning (`bg-yellow-100 text-yellow-800`) | In use        |
| **Digunakan**    | 2 kontainer  | 🟡 Kuning (`bg-yellow-100 text-yellow-800`) | In use        |
| **available**    | 2 kontainer  | ⚪ Abu-abu (`bg-gray-100 text-gray-800`)    | Legacy        |
| **dikembalikan** | 3 kontainer  | ⚪ Abu-abu (`bg-gray-100 text-gray-800`)    | Return status |

**Total:** 28 kontainer

## 📋 **STATUS YANG DIDEFINISIKAN DI FORM**

Di form create/edit (`resources/views/master-kontainer/create.blade.php`):

```html
<option value="Tersedia">Tersedia</option>
<option value="Disewa">Disewa</option>
<option value="Perbaikan">Perbaikan</option>
<!-- ❌ Tidak ada data -->
<option value="Rusak">Rusak</option>
<!-- ❌ Tidak ada data -->
<option value="Dijual">Dijual</option>
<!-- ❌ Tidak ada data -->
```

## 🎨 **LOGIKA PEWARNAAN STATUS (index.blade.php)**

```php
@php
    $statusClass = 'bg-gray-100 text-gray-800'; // Default abu-abu
    if (in_array($kontainer->status, ['Tersedia', 'Baik']))
        $statusClass = 'bg-green-100 text-green-800';      // 🟢 Hijau
    if (in_array($kontainer->status, ['Disewa', 'Digunakan']))
        $statusClass = 'bg-yellow-100 text-yellow-800';    // 🟡 Kuning
    if (in_array($kontainer->status, ['Rusak', 'Perbaikan']))
        $statusClass = 'bg-red-100 text-red-800';          // 🔴 Merah
@endphp
```

### **Kategori Warna:**

**🟢 HIJAU (Tersedia/Siap Pakai):**

-   `Tersedia` ✅ (20 kontainer)
-   `Baik` ❌ (belum ada data)

**🟡 KUNING (Sedang Digunakan):**

-   `Disewa` ✅ (1 kontainer)
-   `Digunakan` ✅ (2 kontainer)

**🔴 MERAH (Bermasalah):**

-   `Rusak` ❌ (belum ada data)
-   `Perbaikan` ❌ (belum ada data)

**⚪ ABU-ABU (Lainnya/Legacy):**

-   `available` ✅ (2 kontainer) - status dalam bahasa Inggris
-   `dikembalikan` ✅ (3 kontainer) - status return
-   `Dijual` ❌ (belum ada data)

## 🔧 **SARAN PERBAIKAN**

### **1. Standarisasi Status**

Status yang ada saat ini tidak konsisten. Saran:

**Status Standard:**

-   `Tersedia` - Kontainer siap disewakan
-   `Disewa` - Kontainer sedang disewa pelanggan
-   `Perbaikan` - Kontainer dalam perbaikan
-   `Rusak` - Kontainer rusak, tidak bisa digunakan
-   `Dijual` - Kontainer akan dijual

### **2. Migrasi Data Legacy**

```sql
-- Update status bahasa Inggris ke Indonesia
UPDATE kontainers SET status = 'Tersedia' WHERE status = 'available';
UPDATE kontainers SET status = 'Tersedia' WHERE status = 'dikembalikan';
UPDATE kontainers SET status = 'Disewa' WHERE status = 'Digunakan';
```

### **3. Tambah Status Baru (Opsional)**

Jika diperlukan, bisa ditambahkan:

-   `Maintenance` - Sedang maintenance rutin
-   `Hilang` - Kontainer hilang/tidak ditemukan
-   `Retired` - Kontainer pensiunan

## 📊 **STATISTIK CURRENT**

**Status Aktif:**

-   Total kontainer: 28
-   Siap pakai (Tersedia + available + dikembalikan): 25 (89.3%)
-   Sedang digunakan (Disewa + Digunakan): 3 (10.7%)
-   Bermasalah (Rusak + Perbaikan): 0 (0%)

## ✅ **STATUS YANG PERLU DITANGANI**

1. **available** (2 kontainer) → Perlu diubah ke `Tersedia`
2. **dikembalikan** (3 kontainer) → Perlu diubah ke `Tersedia`
3. **Digunakan** (2 kontainer) → Perlu diubah ke `Disewa`

Dengan perbaikan ini, akan ada standarisasi yang konsisten untuk status kontainer.
