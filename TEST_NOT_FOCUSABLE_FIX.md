# Test Script untuk Fix "Not Focusable" Error

## Manual Testing Checklist

### 1. Basic Form Loading

-   [ ] Buka halaman `/surat-jalan/create`
-   [ ] Pastikan form loading tanpa error
-   [ ] Cek console browser tidak ada error JavaScript

### 2. Field Readonly Testing

-   [ ] Pilih order dari dropdown
-   [ ] Pastikan field berikut terisi otomatis dan readonly:
    -   [ ] Pengirim
    -   [ ] Jenis Barang
    -   [ ] Tujuan Pengambilan
    -   [ ] Tujuan Pengiriman
    -   [ ] Tipe Kontainer
    -   [ ] Term
    -   [ ] No. Pemesanan
    -   [ ] Uang Jalan (setelah pilih size)

### 3. Focus Prevention Testing

-   [ ] Coba click pada field readonly
-   [ ] Pastikan field tidak bisa di-focus (cursor tidak masuk)
-   [ ] Cek console log: "Prevented focus on readonly field: [field_name]"

### 4. Tab Navigation Testing

-   [ ] Gunakan Tab key untuk navigasi
-   [ ] Pastikan field readonly di-skip
-   [ ] Focus langsung ke field yang bisa diedit

### 5. Form Validation Testing

-   [ ] Kosongkan field required (tanggal, no surat jalan, kegiatan)
-   [ ] Click Submit
-   [ ] Pastikan tidak ada error "not focusable"
-   [ ] Validasi error muncul dengan normal

### 6. Browser Compatibility

-   [ ] Test di Chrome
-   [ ] Test di Firefox
-   [ ] Test di Edge
-   [ ] Test di Safari (jika available)

### 7. Console Error Check

-   [ ] Buka Developer Tools
-   [ ] Tab Console
-   [ ] Pastikan tidak ada error merah
-   [ ] Debug log harus muncul:
    ```
    Applied focus prevention to X readonly fields
    Auto-updating uang jalan for selected order (jika ada order)
    ```

## Automated Test Commands

### Browser Console Tests

```javascript
// Test 1: Check if readonly fields exist
console.log(
    "Readonly fields:",
    document.querySelectorAll("input[readonly]").length
);

// Test 2: Check tabindex attribute
document.querySelectorAll("input[readonly]").forEach((field) => {
    console.log(field.name + " tabindex:", field.getAttribute("tabindex"));
});

// Test 3: Test focus prevention
const readonlyField = document.querySelector("input[readonly]");
if (readonlyField) {
    readonlyField.focus();
    console.log(
        "Active element after focus attempt:",
        document.activeElement.name
    );
}

// Test 4: Check event listeners
console.log(
    "Focus prevention events attached:",
    document.querySelectorAll("input[readonly]").length > 0
);
```

### PHP/Laravel Tests

```bash
# Test form rendering
php artisan route:list | grep surat-jalan

# Check if create route exists
curl -I http://localhost:8000/surat-jalan/create

# Test with specific order
curl -I "http://localhost:8000/surat-jalan/create?order_id=1"
```

## Expected Results

### ✅ Success Criteria

1. **No Console Errors**: Console bersih tanpa error merah
2. **Readonly Fields Skip Focus**: Field readonly tidak bisa di-focus
3. **Tab Navigation Works**: Tab skip readonly fields
4. **Validation Works**: Form validation normal tanpa "not focusable"
5. **JavaScript Functions Load**: Debug logs muncul di console

### ❌ Failure Indicators

1. Console error: "X is not focusable"
2. Field readonly bisa di-focus/edit
3. Tab navigation stuck di readonly field
4. Form validation gagal submit
5. JavaScript tidak load

## Debugging Commands

### Check Field Attributes

```javascript
// Check specific field
const field = document.querySelector('input[name="tujuan_pengiriman"]');
console.log({
    readonly: field.hasAttribute("readonly"),
    tabindex: field.getAttribute("tabindex"),
    focusable: field.tabIndex !== -1,
});
```

### Monitor Focus Events

```javascript
// Add temporary focus monitor
document.addEventListener(
    "focus",
    function (e) {
        console.log("Focus event on:", e.target.name || e.target.tagName);
    },
    true
);
```

### Test Form Validation

```javascript
// Trigger validation manually
const form = document.querySelector("form");
const firstRequired = form.querySelector("[required]");
firstRequired.value = "";
form.reportValidity();
```

## Fix Verification

### Before Fix (Expected Issues):

-   Console error: "tujuan_pengiriman is not focusable"
-   Form validation stops working
-   User experience interrupted
-   Tab navigation broken

### After Fix (Expected Behavior):

-   Clean console (no focus errors)
-   Form validation works smoothly
-   Tab navigation skips readonly fields
-   User experience improved
-   Debug logs show prevention working

## Report Template

```
Date: [Today's Date]
Browser: [Chrome/Firefox/Edge/Safari]
Test Status: [PASS/FAIL]

Issues Found:
- [ ] Console errors present
- [ ] Focus prevention not working
- [ ] Tab navigation issues
- [ ] Form validation problems

Notes:
[Any additional observations]

Tested by: [Your Name]
```

---

_Test procedures created: November 2, 2025_  
_Run these tests after implementing the fix_
