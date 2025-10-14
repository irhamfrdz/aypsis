# Update Supir Input - Master Karyawan Integration

## Perubahan yang Telah Dilakukan

### ✅ Input Supir dari Master Karyawan:

1. **Controller (SuratJalanController.php)**

    - ✅ Import model `Karyawan`
    - ✅ Query karyawan dengan divisi/pekerjaan supir
    - ✅ Pass data `$supirs` ke view
    - ✅ Filter: `divisi LIKE '%supir%' OR pekerjaan LIKE '%supir%'`

2. **Form (create.blade.php)**

    - ✅ Input text supir diubah menjadi dropdown select
    - ✅ Dropdown terisi dari master karyawan divisi supir
    - ✅ Option value berisi nama lengkap supir
    - ✅ Data-attribute `data-plat` berisi nomor plat supir

3. **Auto-Fill No Plat**
    - ✅ JavaScript function `updateNoPlat()` untuk auto-fill
    - ✅ Event `onchange` pada dropdown supir
    - ✅ No plat otomatis terisi saat supir dipilih
    - ✅ No plat akan kosong jika tidak ada supir dipilih

## Detail Implementasi:

### Query Karyawan Supir:

```php
$supirs = Karyawan::where('divisi', 'LIKE', '%supir%')
                 ->orWhere('pekerjaan', 'LIKE', '%supir%')
                 ->whereNotNull('nama_lengkap')
                 ->orderBy('nama_lengkap')
                 ->get(['id', 'nama_lengkap', 'plat']);
```

### Dropdown HTML:

```html
<select name="supir" id="supir-select" onchange="updateNoPlat()">
    <option value="">Pilih Supir</option>
    @foreach($supirs as $supir)
    <option value="{{ $supir->nama_lengkap }}" data-plat="{{ $supir->plat }}">
        {{ $supir->nama_lengkap }}
    </option>
    @endforeach
</select>
```

### JavaScript Auto-Fill:

```javascript
function updateNoPlat() {
    const supirSelect = document.getElementById("supir-select");
    const noPlatInput = document.getElementById("no-plat-input");

    if (supirSelect.selectedIndex > 0) {
        const selectedOption = supirSelect.options[supirSelect.selectedIndex];
        const platNumber = selectedOption.getAttribute("data-plat");
        noPlatInput.value = platNumber || "";
    } else {
        noPlatInput.value = "";
    }
}
```

## Fitur yang Tersedia:

### ✅ Dropdown Supir:

-   Menampilkan semua karyawan dengan divisi atau pekerjaan "supir"
-   Sorted berdasarkan nama lengkap
-   Option value berisi nama lengkap untuk disimpan ke database

### ✅ Auto-Fill No Plat:

-   No plat otomatis terisi saat memilih supir
-   Mengambil data dari kolom `plat` di master karyawan
-   Otomatis kosong jika ganti ke "Pilih Supir"

### ✅ User Experience:

-   Dropdown dengan placeholder "Pilih Supir"
-   Info text: "No. plat akan otomatis terisi berdasarkan supir yang dipilih"
-   Validasi tetap berjalan normal
-   Old input value terjaga saat ada error

## Data Source:

### Model Karyawan:

-   **Tabel**: `karyawans`
-   **Filter Field**: `divisi` dan `pekerjaan`
-   **Display Field**: `nama_lengkap`
-   **Auto-Fill Field**: `plat`

## Status: SELESAI ✅

Semua fitur telah berhasil diimplementasikan:

-   ✅ Input supir menggunakan dropdown dari master karyawan
-   ✅ Filter karyawan divisi supir
-   ✅ Auto-fill no plat berdasarkan supir yang dipilih
-   ✅ JavaScript berfungsi dengan baik
-   ✅ Form validation tetap berjalan normal

Sekarang user dapat memilih supir dari master data dan no plat akan otomatis terisi!
