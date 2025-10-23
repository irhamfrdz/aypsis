# Fitur Auto Create Prospek dari Pembayaran FCL

## Deskripsi

Ketika pembayaran pranota surat jalan dilakukan, sistem akan otomatis membuat data prospek untuk setiap surat jalan dengan tipe kontainer **FCL**.

## Alur Kerja

### 1. User melakukan pembayaran

-   User membuka form pembayaran pranota surat jalan
-   User memilih satu atau lebih pranota yang akan dibayar
-   Sistem menampilkan badge "→ Prospek" untuk pranota yang memiliki surat jalan tipe FCL

### 2. Sistem membuat prospek otomatis

Setelah pembayaran berhasil disimpan, sistem akan:

-   Loop melalui semua surat jalan yang terkait dengan pranota yang dibayar
-   Mengecek tipe kontainer setiap surat jalan
-   Jika tipe kontainer = "FCL", buat data prospek baru

## Data Prospek yang Dibuat

### Data yang Diisi dari Surat Jalan:

| Field Prospek       | Sumber Data                        | Keterangan                                                             |
| ------------------- | ---------------------------------- | ---------------------------------------------------------------------- |
| `tanggal`           | Tanggal hari ini (saat pembayaran) | Tanggal masuk prospek                                                  |
| `nama_supir`        | `surat_jalans.supir`               | Nama supir dari surat jalan                                            |
| `barang`            | `surat_jalans.jenis_barang`        | Jenis barang/muatan                                                    |
| `pt_pengirim`       | `surat_jalans.pengirim`            | Nama pengirim/shipper                                                  |
| `tipe`              | `surat_jalans.tipe_kontainer`      | Tipe kontainer (FCL/LCL)                                               |
| `ukuran`            | `surat_jalans.size`                | Ukuran kontainer (20/40)                                               |
| `tujuan_pengiriman` | `surat_jalans.tujuan_pengiriman`   | Tujuan pengiriman                                                      |
| `keterangan`        | Auto generated                     | Format: "Auto generated dari Surat Jalan: [nomor] \| Pranota: [nomor]" |
| `status`            | `aktif`                            | Status default prospek                                                 |

### Data yang Belum Diisi (NULL):

-   `nomor_kontainer` - Belum ada karena belum masuk checkpoint
-   `no_seal` - Belum ada karena belum masuk checkpoint
-   `nama_kapal` - Akan diisi nanti

## Implementasi Teknis

### File yang Dimodifikasi:

#### 1. Controller: `app/Http/Controllers/PembayaranPranotaSuratJalanController.php`

```php
// Import model Prospek dan SuratJalan
use App\Models\Prospek;
use App\Models\SuratJalan;

// Method baru untuk create prospek
private function createProspekFromFclSuratJalan($pranotaId)
{
    // Loop surat jalan, cek tipe FCL, create prospek
    // Return jumlah prospek yang dibuat
}

// Modifikasi method store
public function store(Request $request)
{
    // ... existing code ...

    // Setelah pembayaran dibuat, create prospek
    $prospeksCount = $this->createProspekFromFclSuratJalan($pranotaId);
    $totalProspeksCreated += $prospeksCount;

    // ... existing code ...

    // Update success message
    if ($totalProspeksCreated > 0) {
        $successMessage .= ' ' . $totalProspeksCreated . ' data prospek FCL telah dibuat otomatis.';
    }
}
```

#### 2. View: `resources/views/pembayaran-pranota-surat-jalan/create.blade.php`

-   Tambah kolom "Tipe" di tabel pranota
-   Tambah badge "→ Prospek" untuk surat jalan FCL
-   Tambah info notice di header tabel
-   Tambah legend di footer tabel

## Logging

Sistem mencatat aktivitas di log file:

### Log per prospek dibuat:

```php
Log::info('Prospek created from FCL Surat Jalan', [
    'prospek_id' => $prospek->id,
    'surat_jalan_id' => $suratJalan->id,
    'surat_jalan_no' => $suratJalan->no_surat_jalan,
    'pranota_id' => $pranotaId,
    'pranota_no' => $pranota->nomor_pranota,
    'tipe_kontainer' => $suratJalan->tipe_kontainer,
    'supir' => $suratJalan->supir,
    'pengirim' => $suratJalan->pengirim
]);
```

### Log total prospek per pranota:

```php
Log::info('Total prospek created from payment', [
    'pranota_id' => $pranotaId,
    'total_prospeks' => $prospeksCreated
]);
```

### Log error (jika ada):

```php
Log::error('Error creating prospek from FCL surat jalan', [
    'pranota_id' => $pranotaId,
    'error' => $e->getMessage(),
    'trace' => $e->getTraceAsString()
]);
```

## Error Handling

-   Jika terjadi error saat membuat prospek, **pembayaran tetap berhasil**
-   Error hanya dicatat di log, tidak mempengaruhi proses pembayaran
-   User tetap mendapat success message untuk pembayaran

## Contoh Penggunaan

### Skenario 1: Pembayaran dengan 1 pranota, 2 surat jalan (1 FCL, 1 LCL)

-   User bayar 1 pranota
-   Pranota memiliki 2 surat jalan:
    -   Surat Jalan A (FCL) → **Dibuat prospek**
    -   Surat Jalan B (LCL) → Tidak dibuat prospek
-   Success message: "Pembayaran berhasil disimpan untuk 1 pranota surat jalan. 1 data prospek FCL telah dibuat otomatis."

### Skenario 2: Pembayaran dengan 2 pranota, masing-masing punya FCL

-   User bayar 2 pranota
-   Pranota 1: 1 surat jalan FCL → **Dibuat 1 prospek**
-   Pranota 2: 2 surat jalan FCL → **Dibuat 2 prospek**
-   Success message: "Pembayaran berhasil disimpan untuk 2 pranota surat jalan. 3 data prospek FCL telah dibuat otomatis."

### Skenario 3: Tidak ada FCL

-   User bayar pranota dengan surat jalan LCL saja
-   Tidak ada prospek yang dibuat
-   Success message: "Pembayaran berhasil disimpan untuk 1 pranota surat jalan."

## Fitur UI

### Visual Indicator

-   Badge biru "→ Prospek" muncul di kolom Tipe untuk surat jalan FCL
-   Info notice di header tabel menjelaskan auto-create prospek
-   Legend di footer tabel menjelaskan arti badge

### Responsif

-   Tabel tetap responsive dengan kolom tambahan
-   Badge compact dan tidak mengganggu layout

## Database

### Tabel yang Terlibat:

1. `pranota_surat_jalans` - Data pranota
2. `pranota_surat_jalan_items` - Relasi many-to-many
3. `surat_jalans` - Data surat jalan (cek tipe_kontainer)
4. `prospek` - Data prospek yang dibuat

### Field Kunci:

-   `surat_jalans.tipe_kontainer` - Harus "FCL" (case insensitive)
-   `prospek.status` - Default "aktif"

## Testing Checklist

-   [ ] Pembayaran pranota dengan FCL berhasil create prospek
-   [ ] Pembayaran pranota dengan LCL tidak create prospek
-   [ ] Pembayaran multi pranota create prospek sesuai jumlah FCL
-   [ ] Data prospek sesuai dengan surat jalan
-   [ ] Success message menampilkan jumlah prospek
-   [ ] Badge "→ Prospek" muncul untuk FCL
-   [ ] Log tercatat dengan benar
-   [ ] Error tidak mengganggu proses pembayaran
-   [ ] Nomor kontainer dan seal masih NULL
-   [ ] Status prospek default "aktif"

## Maintenance

### Jika ingin mengubah field yang di-copy:

Edit method `createProspekFromFclSuratJalan()` di controller:

```php
$prospekData = [
    'tanggal' => now(),
    'nama_supir' => $suratJalan->supir ?? null,
    'tipe' => $suratJalan->tipe_kontainer ?? null,
    // Tambah/ubah field di sini
];
```

### Jika ingin mengubah kondisi tipe kontainer:

```php
// Sekarang: hanya FCL
if (strtoupper($suratJalan->tipe_kontainer) !== 'FCL') {
    continue;
}

// Contoh: FCL atau SPECIAL
if (!in_array(strtoupper($suratJalan->tipe_kontainer), ['FCL', 'SPECIAL'])) {
    continue;
}
```

## Author & Version

-   Created: Oktober 2025
-   Developer: GitHub Copilot
-   Version: 1.0
