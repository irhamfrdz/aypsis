# Fitur Checkbox dan Tombol Buat Pranota

## 📋 Overview

Fitur ini menambahkan kemampuan untuk memilih multiple tagihan menggunakan checkbox dan membuat pranota baik untuk item individual maupun multiple items sekaligus.

## ✨ Fitur yang Ditambahkan

### 1. **Checkbox Selection System**

-   ✅ **Master Checkbox**: Checkbox di header untuk select/deselect semua item
-   ✅ **Individual Checkboxes**: Checkbox di setiap row untuk seleksi individual
-   ✅ **Visual Feedback**: Counter dan status informasi real-time
-   ✅ **Smart State Management**: Indeterminate state ketika sebagian item dipilih

### 2. **Tombol Buat Pranota**

-   🟢 **Individual Pranota**: Tombol hijau "Pranota" di setiap row kolom aksi
-   🟢 **Bulk Pranota**: Tombol "Buat Pranota Terpilih" dengan counter items
-   🔄 **Dynamic State**: Tombol bulk disabled ketika tidak ada item terpilih

### 3. **Visual Indicators**

-   📊 **Counter**: Menampilkan jumlah item terpilih "(0)"
-   📝 **Status Text**: "Pilih item untuk membuat pranota" / "X item terpilih"
-   🎨 **Color Coding**: Text hijau saat ada item terpilih

## 🎯 Layout & Structure

### **Header Tabel**

```
┌─────────────────────────────────────────────────────────────┐
│ [☑] │ Grup │ Vendor │ Container │ Size │ ... │ Status │ Aksi │
└─────────────────────────────────────────────────────────────┘
```

### **Setiap Row**

```
┌─────────────────────────────────────────────────────────────┐
│ [☑] │ TK.. │ DPE    │ CBHU..    │ 20'  │ ... │ ●      │ 🔍📝🟢❌ │
└─────────────────────────────────────────────────────────────┘
```

### **Bulk Action Bar**

```
┌──────────────────────────────────────────────────────────┐
│ [🟢 Buat Pranota Terpilih (3)] │ 3 item terpilih        │
└──────────────────────────────────────────────────────────┘
```

## 🎨 Tombol Aksi di Kolom Aksi

### **Sebelum (3 tombol)**

```
[🔍 Lihat] [📝 Edit] [❌ Hapus]
```

### **Sesudah (4 tombol)**

```
[🔍 Lihat] [📝 Edit] [🟢 Pranota] [❌ Hapus]
```

## 💻 JavaScript Functionality

### **Core Functions**

1. **`updateBulkActionState()`**

    - Update counter dan status text
    - Enable/disable bulk action button
    - Update visual feedback

2. **`buatPranota(id)`**

    - Handle individual pranota creation
    - Confirmation dialog
    - Single item processing

3. **`buatPranotaTerpilih()`**
    - Handle bulk pranota creation
    - Validation dan confirmation
    - Multiple items processing

### **Event Handlers**

1. **Master Checkbox**

    ```javascript
    selectAllCheckbox.addEventListener("change", function () {
        rowCheckboxes.forEach((checkbox) => {
            checkbox.checked = this.checked;
        });
        updateBulkActionState();
    });
    ```

2. **Individual Checkboxes**
    ```javascript
    checkbox.addEventListener("change", function () {
        // Update master checkbox state (indeterminate)
        // Update bulk action state
    });
    ```

## 🔄 User Experience Flow

### **Scenario 1: Individual Pranota**

1. User klik tombol "Pranota" pada row tertentu
2. Confirmation dialog muncul: "Buat pranota untuk tagihan ini?"
3. Jika OK → Alert: "Membuat pranota untuk tagihan ID: X"
4. (Future) Redirect ke halaman create pranota

### **Scenario 2: Bulk Pranota**

1. User pilih checkbox di beberapa row
2. Counter update real-time: "Buat Pranota Terpilih (3)"
3. Status berubah: "3 item terpilih" (warna hijau)
4. User klik tombol bulk action
5. Confirmation: "Buat pranota untuk 3 tagihan terpilih?"
6. Jika OK → Alert dengan daftar ID

### **Scenario 3: Select All**

1. User klik master checkbox
2. Semua row checkbox ter-check otomatis
3. Bulk button enabled dengan total count
4. User bisa uncheck individual items
5. Master checkbox jadi indeterminate

## 🎛️ Button States

### **Bulk Pranota Button States**

```css
/* Disabled (no selection) */
.bg-gray-400.cursor-not-allowed

/* Enabled (has selection) */
.bg-green-600.hover:bg-green-700
```

### **Individual Pranota Button**

```css
/* Always enabled */
.bg-green-100.text-green-700.hover: bg-green-200;
```

## 📱 Responsive Design

-   Buttons stack pada layar kecil
-   Counter tetap visible
-   Touch-friendly checkbox size (16x16px)
-   Horizontal scroll untuk tabel lebar

## 🔧 Technical Implementation

### **HTML Structure**

```html
<!-- Master Checkbox -->
<input type="checkbox" id="select-all" class="w-4 h-4 text-indigo-600..." />

<!-- Row Checkbox -->
<input
    type="checkbox"
    name="selected_items[]"
    value="{{ $tagihan->id }}"
    class="row-checkbox w-4 h-4 text-indigo-600..."
/>

<!-- Individual Pranota Button -->
<button
    onclick="buatPranota({{ $tagihan->id }})"
    class="bg-green-100 text-green-700..."
>
    <!-- Bulk Pranota Button -->
    <button
        id="bulk-pranota-btn"
        onclick="buatPranotaTerpilih()"
        class="bg-green-600 text-white..."
        disabled
    ></button>
</button>
```

### **CSS Classes Used**

-   `w-4 h-4`: Checkbox size
-   `text-indigo-600`: Checkbox color
-   `bg-green-100/600`: Button colors
-   `disabled:bg-gray-400`: Disabled state
-   `transition-colors`: Smooth color transitions

## 🚀 Future Enhancements

### **Planned Features**

1. **Route Integration**: Actual pranota creation routes
2. **Progress Indicators**: Loading states for bulk operations
3. **Keyboard Shortcuts**: Ctrl+A untuk select all
4. **Advanced Filtering**: Select by criteria (vendor, status, etc)
5. **Export Selected**: Export only selected items
6. **Status Updates**: Real-time status updates

### **Potential Routes**

```php
// Individual pranota
Route::get('/pranota/create', [PranotaController::class, 'create'])
    ->name('pranota.create');

// Bulk pranota
Route::post('/pranota/create-bulk', [PranotaController::class, 'createBulk'])
    ->name('pranota.create-bulk');
```

## ✅ Testing Checklist

-   [x] Master checkbox selects/deselects all
-   [x] Individual checkboxes work independently
-   [x] Counter updates correctly
-   [x] Bulk button enables/disables properly
-   [x] Individual pranota buttons work
-   [x] Confirmation dialogs appear
-   [x] Visual feedback (colors, text) correct
-   [x] Responsive layout maintained
-   [x] No JavaScript errors in console

## 🎉 Benefits

1. **Efficiency**: Bulk operations untuk multiple items
2. **UX**: Clear visual feedback dan intuitive controls
3. **Flexibility**: Individual dan bulk actions
4. **Professional**: Modern checkbox interface
5. **Scalability**: Easy to extend untuk fitur lain

Fitur checkbox dan pranota sekarang sudah fully functional dan siap untuk integrasi dengan backend pranota system! 🚀
