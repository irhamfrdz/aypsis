# Resizable Table Component - Documentation

## Overview
Komponen ini memungkinkan kolom tabel dapat diperbesar dan diperkecil seperti di Excel dengan cara drag handle separator pada header kolom.

## Features
- ✅ Drag and drop untuk resize kolom
- ✅ Visual separator/handle yang jelas
- ✅ Hover effect untuk memudahkan identifikasi
- ✅ Batasan min-max width (80px - 600px)
- ✅ Smooth cursor interaction
- ✅ Reusable component

## Quick Start

### 1. Include Component
Tambahkan di bagian bawah file blade Anda (sebelum @endpush atau @endsection):
```blade
@include('components.resizable-table')
```

### 2. Update Table Element
Tambahkan class dan id pada table:
```blade
<table class="min-w-full divide-y divide-gray-200 resizable-table" id="yourTableId">
```

### 3. Update Table Headers
Untuk setiap `<th>` yang ingin dapat diresize, tambahkan:
- Class `resizable-th`
- Style `position: relative;`
- Div `resize-handle` di dalam th

```blade
<th class="resizable-th px-6 py-3 text-left" style="position: relative;">
    Column Name
    <div class="resize-handle"></div>
</th>
```

Kolom terakhir (biasanya "Aksi") tidak perlu diresize, jadi tidak perlu menambahkan class dan handle:
```blade
<th class="px-6 py-3 text-left">Aksi</th>
```

### 4. Initialize JavaScript
Tambahkan di section scripts:
```blade
@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('yourTableId'); // Ganti dengan ID table Anda
});
</script>
@endpush
```

## Complete Example

```blade
@extends('layouts.app')

@section('content')
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 resizable-table" id="productsTable">
        <thead class="bg-gray-50">
            <tr>
                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="position: relative;">
                    ID
                    <div class="resize-handle"></div>
                </th>
                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="position: relative;">
                    Name
                    <div class="resize-handle"></div>
                </th>
                <th class="resizable-th px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="position: relative;">
                    Price
                    <div class="resize-handle"></div>
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <!-- Table body content -->
        </tbody>
    </table>
</div>
@endsection

@include('components.resizable-table')

@push('scripts')
<script>
$(document).ready(function() {
    initResizableTable('productsTable');
});
</script>
@endpush
```

## Tables Already Implemented
✅ `resources/views/orders/index.blade.php` - ordersTable
✅ `resources/views/surat-jalan/index.blade.php` - suratJalanTable

## Tables To Be Implemented
⏳ `resources/views/approval-surat-jalan/index.blade.php`
⏳ `resources/views/uang-jalan/index.blade.php`
⏳ `resources/views/pranota-uang-jalan/index.blade.php`
⏳ `resources/views/tanda-terima/index.blade.php`
⏳ `resources/views/vendor-kontainer-sewa/index.blade.php`
⏳ `resources/views/daftar-tagihan-kontainer-sewa/index.blade.php`
⏳ `resources/views/surat-jalan-bongkaran/index.blade.php`
⏳ `resources/views/tagihan-ob/index.blade.php`
⏳ `resources/views/tagihan-cat/index.blade.php`
⏳ And more...

## Tips
1. **Performance**: Komponen ini ringan dan tidak mempengaruhi performa
2. **Styling**: Anda bisa custom width handle dengan mengubah CSS di component
3. **Mobile**: Resize handle akan otomatis terlihat pada hover di desktop
4. **Multiple Tables**: Bisa digunakan untuk multiple tables dalam satu halaman, cukup gunakan ID unik dan panggil `initResizableTable()` untuk masing-masing

## Troubleshooting

**Q: Handle tidak terlihat?**
A: Pastikan `style="position: relative;"` sudah ada di `<th>` element

**Q: Tidak bisa resize?**
A: Pastikan sudah memanggil `initResizableTable('tableId')` dengan ID yang benar

**Q: Ukuran tidak berubah?**
A: Periksa apakah ada CSS lain yang override width property

## Support
Jika ada masalah, periksa console browser untuk error JavaScript.
