# Fitur Update Prospek dari Checkpoint FCL

## Deskripsi

Ketika supir melakukan checkpoint untuk surat jalan dengan **tipe FCL**, sistem akan otomatis mengupdate data prospek yang terkait dengan:

-   **Nomor Kontainer** dari checkpoint
-   **No. Seal** dari checkpoint

## Alur Kerja

### 1. Supir Melakukan Checkpoint

-   Supir login dan buka surat jalan yang ditugaskan
-   Isi form checkpoint:
    -   Tanggal checkpoint
    -   Pilih nomor kontainer
    -   Input no. seal
    -   Upload gambar (opsional)
    -   Catatan (opsional)
-   Submit checkpoint

### 2. Sistem Menyimpan Checkpoint

-   Data checkpoint disimpan ke surat jalan
-   Status surat jalan berubah: `sudah_checkpoint`
-   Approval record dibuat dengan status `pending`

### 3. Sistem Cek Tipe Kontainer

```php
if (strtoupper($suratJalan->tipe_kontainer) === 'FCL') {
    // Lanjut update prospek
}
```

### 4. Sistem Cari Prospek yang Cocok

Kriteria pencarian prospek:

-   `nama_supir` = `surat_jalans.supir`
-   `pt_pengirim` = `surat_jalans.pengirim`
-   `status` = 'aktif'
-   `nomor_kontainer` = NULL (belum diisi)
-   `tanggal` dalam range 7 hari ke belakang sampai 1 hari ke depan

### 5. Sistem Update Prospek

Jika prospek ditemukan, update:

```php
$prospek->update([
    'nomor_kontainer' => $nomorKontainer, // dari checkpoint
    'no_seal' => $noSeal,                 // dari checkpoint
    'updated_by' => Auth::id()
]);
```

## Implementasi Teknis

### File yang Dimodifikasi:

#### 1. Controller: `app/Http/Controllers/CheckpointController.php`

**Import Model Prospek:**

```php
use App\Models\Prospek;
```

**Method Baru: `updateProspekFromCheckpoint()`**

```php
private function updateProspekFromCheckpoint($suratJalan, $nomorKontainer, $noSeal)
{
    // Cek tipe FCL
    // Cari prospek yang cocok
    // Update nomor kontainer dan seal
    // Log hasil
}
```

**Modifikasi Method `storeSuratJalan()`:**

```php
public function storeSuratJalan(Request $request, $suratJalan)
{
    // ... existing code ...

    DB::commit();

    // Update prospek jika FCL
    $this->updateProspekFromCheckpoint($suratJalan, $nomorKontainers, $request->no_seal);

    return redirect()->route('supir.dashboard')
        ->with('success', '...');
}
```

## Kriteria Matching Prospek

### Kriteria Wajib:

1. **Nama Supir** harus sama persis
2. **PT/Pengirim** harus sama persis
3. **Status** harus 'aktif'
4. **Nomor Kontainer** harus NULL (belum terisi)

### Kriteria Tanggal:

-   Prospek dibuat dalam range: **7 hari ke belakang** sampai **1 hari ke depan** dari hari ini
-   Ini untuk menangani kasus prospek dibuat lebih dulu atau bersamaan dengan checkpoint

### Logika Pemilihan:

-   Jika ada **multiple prospek** yang cocok → Ambil yang **pertama** (paling lama)
-   Hanya **1 prospek** yang diupdate per checkpoint

## Data yang Diupdate

| Field Prospek     | Sumber Data      | Keterangan                  |
| ----------------- | ---------------- | --------------------------- |
| `nomor_kontainer` | Checkpoint input | Bisa multiple, dipisah koma |
| `no_seal`         | Checkpoint input | Nomor seal kontainer        |
| `updated_by`      | Auth::id()       | ID user yang login          |
| `updated_at`      | Auto             | Timestamp update            |

### Field yang TIDAK Diupdate:

-   ✗ `tanggal` - Tetap tanggal awal prospek
-   ✗ `nama_supir` - Sudah ada dari pembayaran
-   ✗ `barang` - Sudah ada dari pembayaran
-   ✗ `pt_pengirim` - Sudah ada dari pembayaran
-   ✗ `tipe` - Sudah ada dari pembayaran
-   ✗ `ukuran` - Sudah ada dari pembayaran
-   ✗ `tujuan_pengiriman` - Sudah ada dari pembayaran
-   ✗ `status` - Tetap 'aktif'

## Logging

### Log Success:

```php
Log::info('Prospek berhasil diupdate dari checkpoint FCL', [
    'prospek_id' => $prospek->id,
    'surat_jalan_id' => $suratJalan->id,
    'nomor_kontainer' => $nomorKontainer,
    'no_seal' => $noSeal,
    'supir' => $suratJalan->supir,
    'pengirim' => $suratJalan->pengirim
]);
```

### Log Not FCL:

```php
Log::info('Surat jalan bukan FCL, skip update prospek', [
    'surat_jalan_id' => $suratJalan->id,
    'tipe_kontainer' => $suratJalan->tipe_kontainer
]);
```

### Log No Match:

```php
Log::info('Tidak ada prospek yang cocok untuk diupdate', [
    'surat_jalan_id' => $suratJalan->id,
    'supir' => $suratJalan->supir,
    'pengirim' => $suratJalan->pengirim
]);
```

### Log Error:

```php
Log::error('Error updating prospek from checkpoint', [
    'surat_jalan_id' => $suratJalan->id,
    'error' => $e->getMessage(),
    'trace' => $e->getTraceAsString()
]);
```

## Error Handling

**Prinsip: Error TIDAK mengganggu proses checkpoint**

-   Update prospek dilakukan **setelah** DB::commit()
-   Jika error saat update prospek → **Hanya log error**
-   Checkpoint **tetap berhasil** disimpan
-   User **tetap** mendapat success message
-   Prospek bisa **diupdate manual** nanti jika diperlukan

## Skenario Testing

### Skenario 1: Happy Path - FCL dengan Prospek Match ✅

**Setup:**

-   Surat Jalan: FCL, Supir: JONI, Pengirim: PT ABC
-   Prospek exists: Supir: JONI, Pengirim: PT ABC, no_kontainer: NULL

**Action:**

-   Supir checkpoint dengan kontainer: ABCD1234567, Seal: S123456

**Expected Result:**

-   ✅ Checkpoint berhasil disimpan
-   ✅ Prospek diupdate:
    -   `nomor_kontainer` = "ABCD1234567"
    -   `no_seal` = "S123456"
-   ✅ Log: "Prospek berhasil diupdate dari checkpoint FCL"

### Skenario 2: Surat Jalan LCL - Skip Update ✅

**Setup:**

-   Surat Jalan: **LCL**, Supir: BUDI, Pengirim: PT XYZ
-   Prospek exists: Supir: BUDI, Pengirim: PT XYZ, no_kontainer: NULL

**Action:**

-   Supir checkpoint dengan kontainer dan seal

**Expected Result:**

-   ✅ Checkpoint berhasil disimpan
-   ✅ Prospek **TIDAK** diupdate (tetap NULL)
-   ✅ Log: "Surat jalan bukan FCL, skip update prospek"

### Skenario 3: FCL tapi No Matching Prospek ✅

**Setup:**

-   Surat Jalan: FCL, Supir: JONI, Pengirim: PT ABC
-   **Tidak ada** prospek dengan supir JONI dan pengirim PT ABC

**Action:**

-   Supir checkpoint dengan kontainer dan seal

**Expected Result:**

-   ✅ Checkpoint berhasil disimpan
-   ✅ Prospek tidak ada yang diupdate (normal)
-   ✅ Log: "Tidak ada prospek yang cocok untuk diupdate"

### Skenario 4: FCL tapi Prospek Sudah Terisi ✅

**Setup:**

-   Surat Jalan: FCL, Supir: JONI, Pengirim: PT ABC
-   Prospek exists: Supir: JONI, Pengirim: PT ABC, **no_kontainer: "XYZ123"** (sudah terisi)

**Action:**

-   Supir checkpoint dengan kontainer baru

**Expected Result:**

-   ✅ Checkpoint berhasil disimpan
-   ✅ Prospek **TIDAK** diupdate (karena sudah ada nomor kontainer)
-   ✅ Log: "Tidak ada prospek yang cocok untuk diupdate"

### Skenario 5: Multiple Kontainer ✅

**Setup:**

-   Surat Jalan: FCL, 2 kontainer
-   Prospek exists dengan no_kontainer: NULL

**Action:**

-   Supir checkpoint dengan: ABCD1234567, EFGH7654321

**Expected Result:**

-   ✅ Checkpoint berhasil disimpan
-   ✅ Prospek diupdate:
    -   `nomor_kontainer` = "ABCD1234567, EFGH7654321" (gabungan)

### Skenario 6: Error saat Update - Tidak Fail Checkpoint ✅

**Setup:**

-   Surat Jalan: FCL
-   Database error saat update prospek

**Expected Result:**

-   ✅ Checkpoint **tetap berhasil** disimpan
-   ✅ User dapat success message
-   ✅ Log error dicatat
-   ⚠️ Prospek tidak terupdate (bisa manual nanti)

## Timeline Data

```
Day 0: Pembayaran Pranota FCL
├─ Prospek dibuat otomatis
├─ nomor_kontainer: NULL
├─ no_seal: NULL
└─ status: 'aktif'

Day 1-7: Supir Checkpoint
├─ Input nomor kontainer
├─ Input no. seal
├─ Submit checkpoint
└─ Sistem update prospek:
    ├─ nomor_kontainer: "ABCD1234567"
    ├─ no_seal: "S123456"
    └─ updated_at: now()

Result: Prospek Complete!
├─ tanggal: ✓
├─ nama_supir: ✓
├─ barang: ✓
├─ pt_pengirim: ✓
├─ tipe: ✓
├─ ukuran: ✓
├─ nomor_kontainer: ✓ (dari checkpoint)
├─ no_seal: ✓ (dari checkpoint)
├─ tujuan_pengiriman: ✓
└─ status: 'aktif'
```

## Benefits

1. **Otomatis** - Nomor kontainer dan seal langsung masuk ke prospek
2. **Akurat** - Data langsung dari checkpoint supir
3. **Konsisten** - Matching berdasarkan supir dan pengirim yang sama
4. **Aman** - Error tidak mengganggu proses checkpoint
5. **Traceable** - Semua activity ter-log dengan detail
6. **User-Friendly** - Supir tidak perlu input ke prospek secara manual

## Maintenance

### Jika ingin mengubah kriteria matching:

Edit method `updateProspekFromCheckpoint()` di CheckpointController:

```php
$prospeks = Prospek::where('nama_supir', $suratJalan->supir)
    ->where('pt_pengirim', $suratJalan->pengirim)
    // Tambah/ubah kriteria di sini
    ->get();
```

### Jika ingin update field lain:

```php
$prospek->update([
    'nomor_kontainer' => $nomorKontainer,
    'no_seal' => $noSeal,
    'nama_kapal' => $request->nama_kapal, // contoh field tambahan
    'updated_by' => Auth::id()
]);
```

### Jika ingin ubah range tanggal:

```php
->whereBetween('tanggal', [
    now()->subDays(14)->format('Y-m-d'), // dari 14 hari
    now()->addDays(2)->format('Y-m-d')   // sampai 2 hari
])
```

## Author & Version

-   Created: Oktober 2025
-   Developer: GitHub Copilot
-   Version: 1.0
-   Related Feature: Auto-create Prospek from FCL Payment
