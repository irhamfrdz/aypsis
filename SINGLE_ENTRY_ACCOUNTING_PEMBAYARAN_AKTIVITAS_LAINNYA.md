# Single-Entry Accounting System untuk Pembayaran Aktivitas Lainnya

## 📋 **Overview**

Sistem pembayaran aktivitas lainnya telah diubah dari double-entry bookkeeping menjadi single-entry system yang lebih sederhana. Sistem ini hanya mengupdate saldo bank/kas yang dipilih tanpa membuat jurnal entry yang kompleks.

## 🔄 **Perubahan Sistem**

### **Sebelum (Double-Entry):**

```
Debit:  Beban Aktivitas Lainnya    Rp 100.000
Kredit: Bank BCA                   Rp 100.000
```

### **Sesudah (Single-Entry):**

```
Bank BCA: Saldo - Rp 100.000
(Tidak ada account beban/biaya)
```

## 🚀 **Implementasi**

### **1. Store (Create) Pembayaran:**

-   Simpan data pembayaran ke tabel `pembayaran_aktivitas_lainnya`
-   **Kurangi saldo** bank yang dipilih sesuai nominal pembayaran
-   Log aktivitas untuk audit trail

### **2. Update Pembayaran:**

-   **Kembalikan saldo** bank lama (jika bank atau nominal berubah)
-   **Kurangi saldo** bank baru sesuai nominal baru
-   Update record pembayaran

### **3. Delete Pembayaran:**

-   **Kembalikan saldo** bank sesuai nominal pembayaran yang dihapus
-   Delete record pembayaran
-   Log aktivitas penghapusan

## 💾 **Database Schema**

### **Tabel: `pembayaran_aktivitas_lainnya`**

```sql
CREATE TABLE pembayaran_aktivitas_lainnya (
    id BIGINT PRIMARY KEY,
    nomor_pembayaran VARCHAR(50),
    tanggal_pembayaran DATE,
    total_pembayaran DECIMAL(15,2),
    pilih_bank BIGINT, -- FK to akun_coa
    aktivitas_pembayaran TEXT,
    is_dp BOOLEAN DEFAULT FALSE,
    created_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### **Tabel: `akun_coa` (Bank/Kas)**

```sql
-- Kolom saldo sudah ada
saldo DECIMAL(15,2) DEFAULT 0
```

## 🎯 **Business Logic**

### **Create Pembayaran:**

```php
// 1. Validasi input
// 2. Simpan pembayaran
$pembayaran = PembayaranAktivitasLainnya::create([...]);

// 3. Kurangi saldo bank (single-entry)
$bankCoa->decrement('saldo', $totalPembayaran);
```

### **Update Pembayaran:**

```php
// 1. Kembalikan saldo bank lama
$oldBankCoa->increment('saldo', $oldTotalPembayaran);

// 2. Kurangi saldo bank baru
$newBankCoa->decrement('saldo', $totalPembayaran);

// 3. Update record pembayaran
```

### **Delete Pembayaran:**

```php
// 1. Kembalikan saldo bank
$bankCoa->increment('saldo', $totalPembayaran);

// 2. Hapus record pembayaran
$pembayaran->delete();
```

## 📊 **Benefits Single-Entry:**

✅ **Simplicity**: Lebih mudah dipahami dan maintain  
✅ **Performance**: Lebih cepat karena tidak buat jurnal entry  
✅ **Cash Flow**: Langsung terlihat impact ke saldo bank  
✅ **User-Friendly**: Interface lebih sederhana

## ⚠️ **Limitations:**

❌ **Audit Trail**: Tidak ada jejak audit lengkap seperti double-entry  
❌ **Financial Reports**: Reporting terbatas dibanding sistem akuntansi penuh  
❌ **Compliance**: Tidak sesuai standar akuntansi formal

## 🔒 **Security & Data Integrity:**

-   **Database Transaction**: Semua operasi wrapped dalam transaction
-   **Error Handling**: Rollback otomatis jika ada error
-   **Logging**: Log semua aktivitas untuk audit trail
-   **Validation**: Validasi input yang ketat

## 🔄 **Future Migration Path:**

Ketika siap upgrade ke double-entry:

1. Create jurnal tables
2. Migration script untuk convert existing data
3. Update controller untuk create jurnal entries
4. Maintain backward compatibility

## 📝 **Files Modified:**

-   `app/Http/Controllers/PembayaranAktivitasLainnyaController.php`
-   Method: `store()`, `update()`, `destroy()`

## 🎯 **Testing Checklist:**

-   [ ] Create pembayaran baru - saldo bank berkurang
-   [ ] Update pembayaran - saldo disesuaikan dengan benar
-   [ ] Delete pembayaran - saldo dikembalikan
-   [ ] Ganti bank saat update - saldo bank lama dan baru benar
-   [ ] Error handling - rollback transaction jika gagal

---

**Date:** October 9, 2025  
**System:** AYPSIS - Single Entry Accounting
