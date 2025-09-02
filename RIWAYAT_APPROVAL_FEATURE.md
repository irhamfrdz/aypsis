# Fitur Riwayat Approval

## Deskripsi

Fitur riwayat approval memungkinkan user untuk melihat dan mengelola history permohonan yang sudah diproses (Selesai, Bermasalah, atau Dibatalkan).

## Akses

-   **URL**: `/approval/riwayat`
-   **Route Name**: `approval.riwayat`
-   **Navigation**: Tab "📚 Riwayat Approval" pada dashboard approval

## Fitur Utama

### 1. Navigasi Tab

-   **Dashboard Approval**: Permohonan yang belum diselesaikan
-   **Riwayat Approval**: Permohonan yang sudah diproses

### 2. Filter Komprehensif

-   ✅ **Filter Vendor**: AYP, ZONA, SOC, DPE
-   ✅ **Filter Status**: Selesai, Bermasalah, Dibatalkan
-   ✅ **Filter Kegiatan**: Berdasarkan master kegiatan
-   ✅ **Filter Tanggal**: Dari dan sampai (berdasarkan updated_at)
-   ✅ **Reset Filter**: Hapus semua filter aktif

### 3. Active Filter Display

Menampilkan badge untuk filter yang sedang aktif:

-   🔵 Vendor: ZONA
-   🟢 Status: Selesai
-   🟣 Kegiatan: Tarik Kontainer Sewa
-   🟡 Periode: 01-09-2025 s/d 30-09-2025

### 4. Summary Statistics

Dashboard mini menampilkan:

-   🟢 **Permohonan Selesai**: Jumlah dengan status "Selesai"
-   🟡 **Permohonan Bermasalah**: Jumlah dengan status "Bermasalah"
-   🔴 **Permohonan Dibatalkan**: Jumlah dengan status "Dibatalkan"

### 5. Data Table

Menampilkan kolom:

-   **Nomor Memo**: Identifikasi permohonan
-   **Supir**: Nama panggilan supir
-   **Kegiatan**: Nama kegiatan dari master_kegiatan
-   **Tujuan**: Tujuan permohonan
-   **Vendor**: Vendor perusahaan
-   **Nomor Kontainer**: Daftar kontainer (comma-separated)
-   **Tanggal Selesai**: Timestamp updated_at (dd-mm-yyyy HH:mm)
-   **Status**: Badge dengan icon dan warna
-   **Aksi**: Tombol detail dan timeline

### 6. Status Badge Colors

```php
'Selesai' => 'bg-green-100 text-green-800' + ✅
'Bermasalah' => 'bg-yellow-100 text-yellow-800' + ⚠️
'Dibatalkan' => 'bg-red-100 text-red-800' + ❌
```

### 7. Action Buttons

-   **👁️ Detail**: Modal popup dengan detail permohonan
-   **📅 Timeline**: Modal popup dengan timeline proses (hanya jika ada checkpoint)

### 8. Pagination

-   **Items per page**: 15
-   **Preserve filters**: URL parameters tetap di pagination
-   **Laravel pagination**: Menggunakan links() dengan query preservation

## Modal Components

### Detail Modal

-   **Purpose**: Menampilkan informasi lengkap permohonan
-   **Content**: ID, Status, dan detail lainnya (dapat diperluas)
-   **Loading**: Spinner animation saat load data

### Timeline Modal

-   **Purpose**: Menampilkan kronologi permohonan
-   **Content**:
    -   ✅ Permohonan Selesai
    -   🔵 Checkpoint Terakhir
    -   🟡 Permohonan Dibuat
-   **Format**: Timeline vertical dengan icon dan timestamp

## Controller Method: `riwayat()`

### Query Logic

```php
$query = Permohonan::whereIn('status', ['Selesai', 'Bermasalah', 'Dibatalkan'])
    ->with(['supir', 'kontainers', 'checkpoints']);
```

### Filter Implementation

-   **Vendor Filter**: `where('vendor_perusahaan', request('vendor'))`
-   **Status Filter**: `where('status', request('status'))`
-   **Kegiatan Filter**: `where('kegiatan', request('kegiatan'))`
-   **Date Range**: `whereBetween('updated_at', [start, end])`

### Data Preparation

-   **Vendors**: Distinct vendor_perusahaan dari permohonan selesai
-   **Kegiatans**: Semua master kegiatan (ordered by nama_kegiatan)
-   **Status Options**: Array predefined status

## Responsive Design

-   ✅ **Grid Layout**: Responsive grid untuk filter form
-   ✅ **Table Responsive**: Horizontal scroll pada mobile
-   ✅ **Button Responsive**: Flex layout untuk action buttons
-   ✅ **Modal Responsive**: Mobile-friendly modal sizing

## Empty State

Ketika tidak ada data:

-   📄 **Icon**: Document icon
-   **Message**: "Tidak ada riwayat permohonan yang ditemukan"
-   **Action**: Link untuk reset filter (jika ada filter aktif)

## JavaScript Functionality

-   **Modal Management**: Show/hide modal dengan backdrop click
-   **Loading States**: Spinner untuk AJAX calls
-   **Dynamic Content**: Placeholder content untuk detail dan timeline

## Future Enhancements

1. **AJAX Detail Loading**: Real data fetch untuk modal detail
2. **Export Feature**: Export filtered data ke Excel/PDF
3. **Advanced Search**: Full-text search across fields
4. **Bulk Actions**: Mass operations pada selected records
5. **Real Timeline**: Integration dengan checkpoint data
6. **Attachment Viewer**: Preview lampiran dalam modal

## Integration Points

-   **Dashboard Link**: Bi-directional navigation dengan approval dashboard
-   **Route Integration**: Seamless dengan existing approval workflow
-   **Permission**: Same permission sebagai approval dashboard
-   **Styling**: Consistent dengan existing approval interface

## Testing Checklist

-   ✅ Route registration
-   ✅ Controller method
-   ✅ View rendering
-   ✅ Filter functionality
-   ✅ Pagination with filters
-   ✅ Modal JavaScript
-   ✅ Responsive design
-   ✅ Error handling
