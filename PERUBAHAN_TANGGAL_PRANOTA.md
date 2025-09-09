## 📅 PERUBAHAN FORMAT TANGGAL PRANOTA

### ✅ **Perubahan yang Dilakukan:**

**File:** `resources/views/pranota-supir/create.blade.php`

**Perubahan:**
```blade
<!-- SEBELUM -->
<input type="date" name="tanggal_pranota" id="tanggal_pranota" class="{{ $readonlyInputClasses }}" value="{{ now()->toDateString() }}" readonly>

<!-- SESUDAH -->
<input type="text" name="tanggal_pranota" id="tanggal_pranota" class="{{ $readonlyInputClasses }}" value="{{ now()->format('d/M/Y') }}" readonly>
```

### 📊 **Format Output:**

| **Sebelum** | **Sesudah** |
|-------------|-------------|
| 2025-09-09  | 09/Sep/2025 |
| 2025-01-01  | 01/Jan/2025 |
| 2025-12-31  | 31/Dec/2025 |

### 💡 **Keuntungan:**

1. ✅ **Konsisten** dengan format di seluruh aplikasi
2. ✅ **User-friendly** - mudah dibaca dan dipahami
3. ✅ **Tidak ambigu** - Sep jelas September
4. ✅ **Format internasional** yang mudah dimengerti
5. ✅ **Cocok untuk print dan export**

### 🔧 **Detail Teknis:**

- **Input Type:** Diubah dari `date` ke `text`
- **Value:** Dari `now()->toDateString()` ke `now()->format('d/M/Y')`
- **Status:** Tetap `readonly` (tidak bisa diubah user)
- **Class:** Tetap menggunakan `$readonlyInputClasses`

### 🚀 **Hasil Akhir:**

Field tanggal pranota sekarang akan menampilkan:
- **Format:** dd/mmm/yyyy (contoh: 09/Sep/2025)
- **Readonly:** User tidak bisa mengubah tanggal
- **Otomatis:** Selalu menggunakan tanggal hari ini
- **Konsisten:** Sama dengan format tanggal di bagian lain aplikasi

### 📋 **Catatan:**

- Filter tanggal (start_date dan end_date) tetap menggunakan `type="date"` untuk date picker
- Perubahan hanya pada field "Tanggal Pranota" yang readonly
- Format sudah sesuai standar aplikasi (dd/mmm/yyyy)
