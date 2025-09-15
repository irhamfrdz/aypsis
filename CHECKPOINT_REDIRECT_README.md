# Fitur Auto Redirect ke Checkpoint untuk Perbaikan Kontainer

## Deskripsi

Sistem telah dikonfigurasi agar ketika membuat permohonan dengan kegiatan **"PERBAIKAN KONTAINER"**, data akan otomatis dikirim ke checkpoint supir seperti halnya kegiatan **"ANTAR KONTAINER SEWA"**.

## Kegiatan yang Memerlukan Checkpoint Otomatis

-   ✅ **ANTAR KONTAINER SEWA** - Redirect otomatis ke checkpoint
-   ✅ **PERBAIKAN KONTAINER** - Redirect otomatis ke checkpoint
-   ❌ **TARIK KONTAINER SEWA** - Redirect normal ke index

## Logika Redirect

### Kondisi untuk Redirect ke Checkpoint:

1. Kegiatan adalah "PERBAIKAN KONTAINER" atau "ANTAR KONTAINER SEWA"
2. User yang login adalah supir yang ditugaskan pada permohonan tersebut
3. Permohonan berhasil dibuat

### Kondisi untuk Redirect Normal:

-   Kegiatan selain yang disebutkan di atas
-   User yang login bukan supir yang ditugaskan
-   Permohonan gagal dibuat

## Implementasi Teknis

### File yang Dimodifikasi:

1. `app/Http/Controllers/PermohonanController.php` - Method `store()`
2. Ditambahkan import `use Illuminate\Support\Facades\Auth;`

### Kode yang Ditambahkan:

```php
// Cek apakah kegiatan memerlukan checkpoint otomatis
$kegiatanLower = strtolower($mk->nama_kegiatan);
$requiresCheckpoint = in_array($kegiatanLower, [
    'antar kontainer sewa',
    'perbaikan kontainer'
]);

if ($requiresCheckpoint && Auth::check() && Auth::user()->karyawan?->id === $permohonan->supir_id) {
    // Redirect ke checkpoint hanya jika user yang login adalah supir yang ditugaskan
    return redirect()->route('supir.checkpoint.create', $permohonan)
        ->with('success', 'Permohonan berhasil ditambahkan! Silakan lengkapi checkpoint.');
} else {
    // Redirect normal ke index
    return redirect()->route('permohonan.index')
        ->with('success', 'Permohonan berhasil ditambahkan!');
}
```

## Alur Kerja

1. **Admin/User membuat permohonan** dengan kegiatan "PERBAIKAN KONTAINER"
2. **Sistem mendeteksi jenis kegiatan**
3. **Jika user adalah supir yang ditugaskan** → Redirect ke halaman checkpoint
4. **Jika user bukan supir** → Redirect ke halaman index permohonan
5. **Supir melengkapi checkpoint** dengan data kontainer dan lokasi

## Keamanan

-   Checkpoint hanya dapat diakses oleh supir yang ditugaskan pada permohonan
-   Sistem memverifikasi `Auth::user()->karyawan?->id === $permohonan->supir_id`
-   Jika tidak sesuai, akan mendapat error 403 Forbidden

## Testing

Script test telah dibuat di `test_checkpoint_logic.php` untuk memverifikasi logika redirect.
