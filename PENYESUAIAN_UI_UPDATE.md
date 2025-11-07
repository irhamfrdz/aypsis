# Update Penyesuaian Logic - User Friendly Interface

## 🎯 Perubahan Yang Dibuat

### Sebelum (Old Logic):

-   User harus mengetik nilai negatif (-25000) untuk pengurangan
-   User harus mengetik nilai positif (+25000) untuk penambahan
-   Rentan kesalahan karena user lupa tanda minus
-   Interface kurang intuitif

### Sesudah (New Logic):

-   ✅ **Dropdown selector**: `(-)` untuk mengurangi, `(+)` untuk menambah
-   ✅ **Input amount**: Hanya nilai positif (tanpa tanda)
-   ✅ **Auto calculation**: System otomatis convert ke nilai final
-   ✅ **User friendly**: Tidak perlu ingat tanda minus

## 🛠️ Implementasi Teknis

### 1. Form Interface Changes

#### Create Form (`create.blade.php`)

```html
<div class="flex">
    <select
        id="penyesuaianType"
        onchange="updateTotalWithPenyesuaian()"
        class="rounded-l-md border-gray-300 border-r-0"
    >
        <option value="subtract">-</option>
        <option value="add">+</option>
    </select>
    <input
        type="number"
        name="penyesuaian_amount"
        id="penyesuaian_amount"
        min="0"
        step="0.01"
        placeholder="0.00"
    />
    <input type="hidden" name="penyesuaian" id="penyesuaian" />
</div>
```

#### Edit Form (`edit.blade.php`)

```php
@php
    $currentPenyesuaian = old('penyesuaian', $pranotaUangJalan->penyesuaian);
    $isAddition = $currentPenyesuaian >= 0;
    $displayAmount = abs($currentPenyesuaian);
@endphp
<select id="penyesuaianType">
    <option value="subtract" {{ !$isAddition ? 'selected' : '' }}>-</option>
    <option value="add" {{ $isAddition ? 'selected' : '' }}>+</option>
</select>
<input type="number" name="penyesuaian_amount" value="{{ $displayAmount }}">
```

### 2. JavaScript Logic

```javascript
function updateTotalWithPenyesuaian() {
    const penyesuaianType = document.getElementById("penyesuaianType").value;
    const penyesuaianAmount =
        parseFloat(document.getElementById("penyesuaian_amount").value) || 0;

    // Hitung penyesuaian berdasarkan tipe
    let penyesuaian = 0;
    if (penyesuaianType === "subtract") {
        penyesuaian = -Math.abs(penyesuaianAmount); // Selalu negatif
    } else if (penyesuaianType === "add") {
        penyesuaian = Math.abs(penyesuaianAmount); // Selalu positif
    }

    // Update hidden input dengan nilai final
    document.getElementById("penyesuaian").value = penyesuaian;

    // Update total calculation...
}
```

### 3. Controller Validation

```php
$request->validate([
    'penyesuaian' => 'nullable|numeric',
    'penyesuaian_amount' => 'nullable|numeric|min:0', // Hanya positif
    'keterangan_penyesuaian' => 'nullable|string|max:500',
]);
```

## 📊 Test Results

### Logic Test Results:

-   ✅ **Pengurangan**: 25000 (subtract) → -25000 ✅ PASS
-   ✅ **Penambahan**: 50000 (add) → +50000 ✅ PASS
-   ✅ **Potongan**: 15000 (subtract) → -15000 ✅ PASS

### Example Calculations:

```
Subtotal: Rp 500.000

📉 Pengurangan:
- Input: 25.000 (dropdown: -)
- Result: Rp -25.000
- Total: Rp 475.000

📈 Penambahan:
- Input: 75.000 (dropdown: +)
- Result: Rp +75.000
- Total: Rp 575.000
```

## 🎯 User Experience Benefits

### Before:

❌ User confusion dengan tanda minus  
❌ Error-prone (lupa tanda)  
❌ Tidak intuitif

### After:

✅ **Clear visual indicator**: Dropdown (-) / (+)  
✅ **Error prevention**: Input hanya positive  
✅ **Intuitive**: Pilih aksi, masukkan nominal  
✅ **Consistent**: UI pattern yang familiar

## 🔄 Workflow Baru

1. **User memilih tipe**: Dropdown (-) atau (+)
2. **User input nominal**: Hanya angka positif
3. **System calculate**: Auto convert ke nilai final
4. **Real-time update**: Total langsung terupdate
5. **Submit**: Hidden field berisi nilai final

## 📝 Use Cases

### Pengurangan (Subtract):

-   Potongan pajak: 15.000 → penyesuaian: -15.000
-   Biaya admin: 5.000 → penyesuaian: -5.000
-   Denda keterlambatan: 25.000 → penyesuaian: -25.000

### Penambahan (Add):

-   Bonus kinerja: 50.000 → penyesuaian: +50.000
-   Kompensasi lembur: 30.000 → penyesuaian: +30.000
-   Tunjangan khusus: 100.000 → penyesuaian: +100.000

## 🌐 Testing URLs

-   **Create New**: `/pranota-uang-jalan/create`
-   **Edit Existing**: `/pranota-uang-jalan/{id}/edit`
-   **View Result**: `/pranota-uang-jalan/{id}`
-   **Print**: `/pranota-uang-jalan/{id}/print`

## 🎉 Status: **COMPLETED & TESTED**

Logika penyesuaian baru telah diimplementasi dengan interface yang lebih user-friendly dan telah lolos semua test!
