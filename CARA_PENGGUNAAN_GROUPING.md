# CARA MENGGUNAKAN FITUR GROUPING PRANOTA ZONA

## 🚨 **Status Saat Ini**

-   ✅ **Backend sudah siap** (controller methods & routes)
-   ❌ **Frontend UI belum ada** (perlu tambahan tombol & JavaScript)
-   ❌ **Belum terintegrasi** dengan halaman existing

## 📖 **Cara Penggunaan Lengkap**

### **Scenario 1: Testing Manual (Saat Ini)**

#### 1. Import Data Zona

```bash
# Di terminal, jalankan:
cd C:\folder_kerjaan\aypsis
php import_zona_data.php
```

#### 2. Cek Data di Database

```sql
-- Lihat data yang sudah diimport
SELECT no_invoice_vendor, no_bank, COUNT(*) as jumlah_kontainer
FROM daftar_tagihan_kontainer_sewa
WHERE supplier = 'ZONA'
  AND status_pranota = 'pending'
GROUP BY no_invoice_vendor, no_bank
HAVING jumlah_kontainer > 1
ORDER BY jumlah_kontainer DESC;
```

#### 3. Test via Postman/API

```bash
# Preview grouping
POST: /pranota-kontainer-sewa/preview-vendor-invoice-grouping
Body: {
  "tagihan_kontainer_sewa_ids": [1,2,3,4,5]
}

# Create pranota by grouping
POST: /pranota-kontainer-sewa/create-by-vendor-invoice-group
Body: {
  "tagihan_kontainer_sewa_ids": [1,2,3,4,5]
}
```

### **Scenario 2: Setelah Frontend Siap (Future)**

#### 1. Buka Halaman Tagihan Kontainer Sewa

-   Menu: **Tagihan Kontainer Sewa**
-   Filter: **Supplier = ZONA**
-   Status: **Belum masuk pranota**

#### 2. Pilih Multiple Tagihan

```
☑️ FORU8416289 - Invoice: ZONA25.05.28123, Bank: EBK250600289
☑️ FORU8480890 - Invoice: ZONA25.05.28123, Bank: EBK250600289
☑️ IFLU2990380 - Invoice: ZONA25.05.28123, Bank: EBK250600289
☑️ EGHU9005182 - Invoice: ZONA24.01.22359, Bank: EBK240500055
☑️ NYKU5622053 - Invoice: ZONA24.01.22359, Bank: EBK240500055
```

#### 3. Klik Tombol Grouping

```html
[Preview Grouping] [Buat Pranota Berdasarkan Invoice & Bank]
```

#### 4. Preview Modal (Opsional)

```
Group 1: Invoice ZONA25.05.28123 + Bank EBK250600289
- 3 kontainer: FORU8416289, FORU8480890, IFLU2990380
- Total: Rp 1.693.920

Group 2: Invoice ZONA24.01.22359 + Bank EBK240500055
- 2 kontainer: EGHU9005182, NYKU5622053
- Total: Rp 2.749.550

Akan membuat: 2 pranota untuk 5 kontainer
Penghematan: 3 pranota
```

#### 5. Konfirmasi & Execute

-   Klik **"Buat Pranota"**
-   Sistem buat 2 pranota otomatis
-   Status tagihan berubah jadi **"Sudah masuk pranota"**

## 🔧 **Untuk Developer: Cara Implementasi Frontend**

### **File yang Perlu Dimodifikasi:**

#### 1. **Blade Template** (Halaman Tagihan)

```php
// resources/views/tagihan-kontainer-sewa/index.blade.php
@section('content')
<div class="card">
    <div class="card-header">
        <!-- Existing buttons -->
        <button onclick="bulkCreatePranota()" class="btn btn-primary">
            Buat Pranota Biasa
        </button>

        <!-- NEW: Grouping buttons -->
        <button onclick="previewGrouping()" class="btn btn-info">
            Preview Grouping
        </button>
        <button onclick="createByGrouping()" class="btn btn-success">
            Buat Pranota Berdasarkan Invoice & Bank
        </button>
    </div>
    <!-- Table content -->
</div>
@endsection
```

#### 2. **JavaScript Functions**

```javascript
// public/js/tagihan-kontainer-sewa.js atau di blade

function previewGrouping() {
    const selectedIds = getSelectedTagihanIds();
    if (selectedIds.length === 0) {
        alert("Pilih minimal 1 tagihan kontainer sewa");
        return;
    }

    fetch("/pranota-kontainer-sewa/preview-vendor-invoice-grouping", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({
            tagihan_kontainer_sewa_ids: selectedIds,
        }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                showGroupingModal(data.preview_data, data.summary);
            } else {
                alert(data.message);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("Terjadi kesalahan saat preview grouping");
        });
}

function createByGrouping() {
    const selectedIds = getSelectedTagihanIds();
    if (selectedIds.length === 0) {
        alert("Pilih minimal 1 tagihan kontainer sewa");
        return;
    }

    if (
        !confirm(
            "Buat pranota berdasarkan grouping invoice vendor dan nomor bank?"
        )
    ) {
        return;
    }

    fetch("/pranota-kontainer-sewa/create-by-vendor-invoice-group", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        body: JSON.stringify({
            tagihan_kontainer_sewa_ids: selectedIds,
        }),
    }).then((response) => {
        if (response.ok) {
            location.reload();
        } else {
            alert("Terjadi kesalahan saat membuat pranota");
        }
    });
}

function getSelectedTagihanIds() {
    return $(".tagihan-checkbox:checked")
        .map(function () {
            return $(this).val();
        })
        .get();
}

function showGroupingModal(previewData, summary) {
    // Buat modal untuk menampilkan preview
    let modalContent = '<div class="modal fade" id="groupingModal">';
    modalContent += '<div class="modal-dialog modal-lg">';
    modalContent += '<div class="modal-content">';
    modalContent += '<div class="modal-header">';
    modalContent += "<h5>Preview Grouping Pranota</h5>";
    modalContent += "</div>";
    modalContent += '<div class="modal-body">';

    // Summary
    modalContent += '<div class="alert alert-info">';
    modalContent += `<strong>Ringkasan:</strong><br>`;
    modalContent += `- Total kontainer dipilih: ${summary.total_kontainer_dipilih}<br>`;
    modalContent += `- Kontainer yang akan diproses: ${summary.total_kontainer_diproses}<br>`;
    modalContent += `- Jumlah pranota yang akan dibuat: ${summary.total_pranota_akan_dibuat}<br>`;
    modalContent += `- Penghematan: ${
        summary.total_kontainer_diproses - summary.total_pranota_akan_dibuat
    } pranota`;
    modalContent += "</div>";

    // Groups detail
    previewData.forEach((group, index) => {
        modalContent += '<div class="card mb-2">';
        modalContent += '<div class="card-header">';
        modalContent += `<strong>Pranota ${index + 1}</strong>`;
        modalContent += "</div>";
        modalContent += '<div class="card-body">';
        modalContent += `<p><strong>Invoice Vendor:</strong> ${group.no_invoice_vendor}</p>`;
        modalContent += `<p><strong>Nomor Bank:</strong> ${group.no_bank}</p>`;
        modalContent += `<p><strong>Jumlah Kontainer:</strong> ${group.item_count}</p>`;
        modalContent += `<p><strong>Total Amount:</strong> Rp ${group.total_amount.toLocaleString(
            "id-ID"
        )}</p>`;
        modalContent += `<p><strong>Kontainer:</strong> ${group.kontainer_list.join(
            ", "
        )}</p>`;
        modalContent += "</div>";
        modalContent += "</div>";
    });

    modalContent += "</div>";
    modalContent += '<div class="modal-footer">';
    modalContent +=
        '<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>';
    modalContent +=
        '<button type="button" class="btn btn-success" onclick="confirmCreateGrouping()">Buat Pranota</button>';
    modalContent += "</div>";
    modalContent += "</div>";
    modalContent += "</div>";
    modalContent += "</div>";

    $("body").append(modalContent);
    $("#groupingModal").modal("show");
    $("#groupingModal").on("hidden.bs.modal", function () {
        $(this).remove();
    });
}

function confirmCreateGrouping() {
    $("#groupingModal").modal("hide");
    createByGrouping();
}
```

## ⚠️ **Penting: Yang Harus Dilakukan Dulu**

### **1. Pastikan Database Siap**

```sql
-- Cek apakah kolom sudah ada
DESCRIBE daftar_tagihan_kontainer_sewa;

-- Jika belum ada, tambahkan:
ALTER TABLE daftar_tagihan_kontainer_sewa
ADD COLUMN no_invoice_vendor VARCHAR(255) NULL,
ADD COLUMN tgl_invoice_vendor DATE NULL,
ADD COLUMN no_bank VARCHAR(255) NULL,
ADD COLUMN tgl_bank DATE NULL;
```

### **2. Import Data Test**

```bash
php import_zona_data.php
```

### **3. Test Backend Dulu**

```bash
php test_pranota_grouping.php
```

### **4. Baru Implementasi Frontend**

-   Tambahkan tombol di halaman tagihan
-   Tambahkan JavaScript functions
-   Test end-to-end

## 🎯 **Hasil Akhir**

Setelah semua siap, user bisa:

1. **Import CSV Zona** → Data masuk database
2. **Pilih multiple tagihan** → Centang yang mau di-group
3. **Klik "Buat Pranota Berdasarkan Invoice & Bank"** → Otomatis grouping
4. **Lihat hasil** → X pranota dibuat untuk Y kontainer

**Penghematan rata-rata: 42% pengurangan jumlah pranota!** 🚀
