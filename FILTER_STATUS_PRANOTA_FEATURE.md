# Filter Status Pranota Feature

## 📋 Overview

Filter untuk memfilter tagihan berdasarkan status dalam sistem pranota.

## 🎯 Fitur Filter Status Pranota

### 📊 Opsi Filter

| Filter                        | Keterangan                     | Icon | Fungsi                                     |
| ----------------------------- | ------------------------------ | ---- | ------------------------------------------ |
| **Semua Status Pranota**      | Default - tampilkan semua      | -    | Tidak ada filter                           |
| **🔄 Belum Masuk Pranota**    | `status_pranota = null`        | 🔄   | Tagihan yang tersedia untuk dibuat pranota |
| **🔵 Included (Draft)**       | `status_pranota = 'included'`  | 🔵   | Tagihan sudah masuk pranota draft          |
| **🟡 Invoiced (Terkirim)**    | `status_pranota = 'invoiced'`  | 🟡   | Pranota sudah dikirim                      |
| **🟢 Paid (Lunas)**           | `status_pranota = 'paid'`      | 🟢   | Pranota sudah dibayar                      |
| **🔴 Cancelled (Dibatalkan)** | `status_pranota = 'cancelled'` | 🔴   | Pranota dibatalkan                         |

### 🔗 URL Filter Examples

```
# Belum masuk pranota
/daftar-tagihan-kontainer-sewa?status_pranota=null

# Status included
/daftar-tagihan-kontainer-sewa?status_pranota=included

# Status invoiced
/daftar-tagihan-kontainer-sewa?status_pranota=invoiced

# Status paid
/daftar-tagihan-kontainer-sewa?status_pranota=paid

# Status cancelled
/daftar-tagihan-kontainer-sewa?status_pranota=cancelled

# Kombinasi filter
/daftar-tagihan-kontainer-sewa?vendor=ZONA&status_pranota=included&size=20
```

## 🎨 Visual Features

### 📍 Filter Dropdown

-   **Lokasi**: Form filter di atas tabel daftar tagihan
-   **Style**: Background orange untuk highlight
-   **Icons**: Emoji untuk setiap status

### 🏷️ Active Filter Badges

Ketika filter aktif, muncul badge yang menampilkan:

-   Status filter yang dipilih
-   Icon dan warna sesuai status
-   Tombol reset untuk clear filter

### 📊 Status Column

-   **Kolom baru**: "Status Pranota" di tabel
-   **Visual badges**: Warna berbeda setiap status
-   **Link**: Klik nomor invoice untuk lihat detail pranota

## ⚙️ Implementation

### Controller Logic

```php
// Handle status pranota filter
if ($request->filled('status_pranota')) {
    $statusPranota = $request->input('status_pranota');
    if ($statusPranota === 'null') {
        // Filter untuk tagihan yang belum masuk pranota
        $query->whereNull('status_pranota');
    } else {
        // Filter untuk status pranota spesifik
        $query->where('status_pranota', $statusPranota);
    }
}
```

### View Components

1. **Filter Dropdown**: Select dengan option status
2. **Active Badges**: Tampilan filter yang aktif
3. **Status Column**: Display status di tabel
4. **Reset Button**: Clear semua filter

## 🎯 Use Cases

### 👥 Untuk Finance Team

-   **Filter "Belum Masuk Pranota"**: Lihat tagihan yang siap dibuat pranota
-   **Filter "Included"**: Monitor pranota draft
-   **Filter "Invoiced"**: Track pranota yang sudah dikirim
-   **Filter "Paid"**: Lihat tagihan yang sudah lunas

### 📈 Untuk Management

-   **Dashboard View**: Kombinasi filter untuk laporan
-   **Status Tracking**: Monitor progress pembayaran
-   **Quick Filter**: Akses cepat ke data spesifik

## 🔄 Status Lifecycle Integration

Filter ini terintegrasi dengan lifecycle status pranota:

```
null → included → invoiced → paid
  ↓                            ↑
cancelled ←─────────────────────┘
```

## ✅ Testing Results

```
✅ Filter 'null' (belum masuk pranota): 1194 records
✅ Filter 'included': 1 records
✅ Filter 'invoiced': 1 records
✅ Filter 'paid': 1 records
✅ Controller logic working
✅ Visual components working
✅ Badge display working
```

## 🚀 Benefits

1. **Efisiensi**: Quick access ke data spesifik
2. **Visibility**: Clear view of payment status
3. **Workflow**: Support business process
4. **User Experience**: Intuitive filtering
5. **Integration**: Seamless dengan sistem pranota
