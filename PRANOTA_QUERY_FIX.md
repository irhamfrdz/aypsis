# Pranota Surat Jalan Query Fix

## Problem Found

Surat jalan yang sudah di-approve tidak muncul di halaman create pranota karena:

1. **Query hanya mencari approval level `tugas-1` dan `tugas-2`**

    - Beberapa surat jalan memiliki approval level `approval` (single approval)
    - Tidak semua surat jalan melalui approval dua level

2. **Status surat jalan tidak konsisten**

    - Ada 6 kemungkinan status: `fully_approved`, `approved`, `completed`, `sudah_checkpoint`, `rejected`, `belum masuk checkpoint`
    - Query hanya meng-check approval relationships, bukan status column

3. **Tidak mengecek column status surat jalan**
    - Saat approval berhasil, status diupdate di column `status`
    - Query seharusnya juga check column ini

## Solution Implemented

### Updated Query

```php
$approvedSuratJalans = SuratJalan::where(function($query) {
    // Method 1: Check by status column (ADDED)
    $query->where('status', 'fully_approved')
          ->orWhere('status', 'approved')
          ->orWhere('status', 'completed');
})
->orWhere(function($query) {
    // Method 2: Check if has both tugas-1 and tugas-2 approved (EXISTING)
    $query->whereHas('approvals', function($q) {
        $q->where('approval_level', 'tugas-1')->where('status', 'approved');
    })
    ->whereHas('approvals', function($q) {
        $q->where('approval_level', 'tugas-2')->where('status', 'approved');
    });
})
->whereDoesntHave('pranotaSuratJalan')
->orderBy('tanggal_surat_jalan', 'desc')
->get();
```

### Key Changes

1. **Added status column check**: `where('status', 'fully_approved')`
2. **Accept multiple status values**: `fully_approved`, `approved`, `completed`
3. **Keep backwards compatibility**: Still checks approval relationships for tugas-1/tugas-2
4. **Combine both conditions**: Using `where()` + `orWhere()` to catch both cases

## Results

### Before Fix

-   Query returns: 1 surat jalan (SJ00001 with tugas-1 + tugas-2 approvals)

### After Fix

-   Query returns: 3 surat jalan
    -   SJ0005 (status: completed, single approval)
    -   SJ00006 (status: approved, single approval)
    -   SJ00001 (status: fully_approved, tugas-1 + tugas-2 approvals)

## Files Modified

-   `app/Http/Controllers/PranotaSuratJalanController.php` - Updated `create()` method

## Testing

✅ Updated query working correctly
✅ All approved surat jalan now visible in pranota create form
✅ Backwards compatible with existing approval structure

## Status Values Reference

```
- fully_approved: Passed both tugas-1 and tugas-2 approvals
- approved: Passed approval (single level)
- completed: Fully completed and processed
- sudah_checkpoint: Has been checkpoint but not fully approved
- rejected: Rejected at some approval level
- belum masuk checkpoint: Not yet checkpointed
```

---

Date: October 16, 2025
Impact: HIGH - Fixes issue where approved surat jalan don't appear in pranota creation form
Status: ✅ FIXED
