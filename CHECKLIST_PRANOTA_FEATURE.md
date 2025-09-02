# Fitur Checklist Pranota untuk Pembayaran Batch

## Overview

Fitur ini memungkinkan pengguna untuk memilih multiple pranota menggunakan checkbox dan melanjutkan ke proses pembayaran batch secara langsung dari halaman daftar pranota.

## Fitur yang Diimplementasikan

### 1. Checkbox Selection

-   **Checkbox individual**: Setiap pranota dengan status 'sent' memiliki checkbox untuk dipilih
-   **Checkbox "Pilih Semua"**: Tersedia di header tabel dan di sidebar untuk memilih/deselect semua pranota
-   **Visual indicator**: Pranota yang tidak bisa dipilih (status selain 'sent') menampilkan status dalam text

### 2. Visual Feedback

-   **Counter selection**: Menampilkan jumlah pranota yang dipilih dan total amount
-   **Indeterminate state**: Checkbox "Pilih Semua" menampilkan state intermediate saat sebagian dipilih
-   **Button state**: Tombol "Proses Pembayaran" disabled saat tidak ada pranota yang dipilih

### 3. Batch Payment Process

-   **Confirmation dialog**: Menampilkan detail pranota yang dipilih dengan total amount sebelum proses
-   **Direct redirect**: Langsung redirect ke form pembayaran dengan pranota yang sudah dipilih
-   **Validation**: Memastikan hanya pranota dengan status 'sent' yang bisa diproses

## Technical Implementation

### Frontend (Blade Template)

**File**: `resources/views/pranota/index.blade.php`

#### Struktur HTML

```html
<!-- Bulk Actions Section -->
<div class="flex justify-between items-center mb-4">
    <div class="flex items-center space-x-4">
        <label class="flex items-center">
            <input
                type="checkbox"
                id="selectAll"
                onchange="toggleAllCheckboxes()"
            />
            <span class="ml-2">Pilih Semua</span>
        </label>
        <span id="selectedCount">0 pranota dipilih</span>
    </div>
    <button
        id="processPembayaranBtn"
        onclick="processPembayaranBatch()"
        disabled
    >
        Proses Pembayaran
    </button>
</div>

<!-- Table with Checkboxes -->
<table>
    <thead>
        <tr>
            <th>
                <input
                    type="checkbox"
                    id="selectAllHeader"
                    onchange="toggleAllCheckboxes()"
                />
            </th>
            <!-- other columns -->
        </tr>
    </thead>
    <tbody>
        @foreach($pranotaList as $pranota)
        <tr>
            <td>
                @if($pranota->status === 'sent')
                <input
                    type="checkbox"
                    class="pranota-checkbox"
                    value="{{ $pranota->id }}"
                    data-amount="{{ $pranota->total_amount }}"
                    data-no-invoice="{{ $pranota->no_invoice }}"
                    onchange="updateSelection()"
                />
                @else
                <span class="text-gray-400">{{ $pranota->status }}</span>
                @endif
            </td>
            <!-- other columns -->
        </tr>
        @endforeach
    </tbody>
</table>
```

#### JavaScript Functions

**toggleAllCheckboxes()**

-   Sync checkbox "Pilih Semua" di header dan sidebar
-   Select/deselect semua checkbox pranota yang tersedia
-   Update selection count dan button state

**updateSelection()**

-   Update counter dengan jumlah pranota dipilih dan total amount
-   Enable/disable tombol "Proses Pembayaran"
-   Update state checkbox "Pilih Semua" (checked, unchecked, indeterminate)

**processPembayaranBatch()**

-   Validate ada pranota yang dipilih
-   Tampilkan confirmation dialog dengan detail
-   Submit form POST ke route pembayaran dengan pranota_ids[]

### Backend (Controller)

**File**: `app/Http/Controllers/PembayaranPranotaKontainerController.php`

#### Method create() - Updated

```php
public function create(Request $request)
{
    // If pranota_ids provided from pranota index, redirect to payment form
    if ($request->has('pranota_ids') && !empty($request->pranota_ids)) {
        return $this->showPaymentForm($request);
    }

    // Otherwise show pranota selection page
    $pranotaList = Pranota::where('status', 'sent')
        ->whereNotExists(function ($query) {
            // Exclude pranota with pending payments
        })
        ->get();

    return view('pembayaran-pranota-kontainer.create', compact('pranotaList'));
}
```

#### Method showPaymentForm()

-   Validate pranota_ids array
-   Check eligibility (status 'sent', no pending payment)
-   Generate payment number
-   Calculate total amount
-   Return payment form view

### Model Updates

**File**: `app/Models/Pranota.php`

#### Added Methods:

-   `hasPaymentPending()`: Check if pranota has pending payment
-   `getSimplePaymentStatus()`: Return 'Dibayar' or 'Belum Dibayar'
-   `getSimplePaymentStatusColor()`: Return appropriate Tailwind color classes
-   `getPaymentDate()`: Get actual payment date from approved payments

#### Relationships:

-   `pembayaranKontainer()`: Many-to-many relationship with PembayaranPranotaKontainer

## User Experience Flow

1. **View Pranota List**: User sees daftar pranota dengan checkbox untuk yang status 'sent'
2. **Select Pranota**: User dapat memilih individual atau gunakan "Pilih Semua"
3. **Real-time Feedback**: Counter dan total amount terupdate secara real-time
4. **Process Payment**: Klik tombol "Proses Pembayaran" (enabled hanya jika ada selection)
5. **Confirmation**: Dialog konfirmasi menampilkan detail pranota dan total amount
6. **Redirect**: Langsung ke form pembayaran dengan data pranota yang sudah dipilih

## Benefits

### For Users:

-   **Efficiency**: Batch processing multiple pranota sekaligus
-   **Clarity**: Visual feedback yang jelas tentang selection dan total
-   **Safety**: Confirmation dialog mencegah kesalahan processing

### For System:

-   **Data Integrity**: Validation di frontend dan backend
-   **Performance**: Efficient query untuk filter pranota yang eligible
-   **Maintainability**: Separation of concerns antara selection dan payment processing

## Security & Validation

### Frontend Validation:

-   Only display checkbox for eligible pranota (status 'sent')
-   Disable process button when no selection
-   Confirmation dialog before proceeding

### Backend Validation:

-   Validate pranota_ids array exists and not empty
-   Check each pranota exists in database
-   Verify pranota status is 'sent'
-   Ensure no pending payments exist

## Future Enhancements

1. **Bulk Status Update**: Update multiple pranota status at once
2. **Export Selected**: Export selected pranota to PDF/Excel
3. **Payment Templates**: Save common payment configurations
4. **Advanced Filtering**: Filter pranota by date range, amount, etc.
5. **Keyboard Shortcuts**: Ctrl+A for select all, etc.

## Testing

### Manual Testing:

1. Verify checkbox behavior dengan different pranota status
2. Test "Pilih Semua" functionality
3. Validate selection counter dan total calculation
4. Confirm payment process works dengan selected pranota
5. Test edge cases (no selection, all paid pranota, etc.)

### Browser Compatibility:

-   Modern browsers dengan JavaScript enabled
-   Responsive design untuk mobile/tablet
-   Graceful degradation untuk older browsers
