# Update Sistem Generate Nomor Voyage Pergerakan Kapal

## Format Nomor Voyage Baru

Format: `[Nickname Kapal 2 digit][No Urut 01][Kode Kota Asal 1 digit][Kode Kota Tujuan 1 digit][Tahun 2 digit]`

### Contoh:

-   **SP01JB25** = SP (Sekar Permata) + 01 (urut Jakarta) + J (Jakarta) + B (Batam) + 25 (tahun 2025)
-   **A101JT25** = A1 (Alexindo 1) + 01 (urut Jakarta) + J (Jakarta) + T (Tanjung Pinang) + 25 (tahun 2025)

## Perubahan yang Dilakukan

### 1. Controller Update (`PergerakanKapalController.php`)

-   **Nickname Kapal**: Mengambil nickname 2 digit dari tabel `master_kapals`
-   **No Urut**: Fixed "01" sesuai permintaan (nomor urut dari Jakarta)
-   **Kode Kota**: Mapping berdasarkan huruf pertama kota pelabuhan
-   **Validasi**: Error handling jika nickname kapal atau data pelabuhan tidak ditemukan

### 2. Frontend Update (`create.blade.php`)

-   Menghapus mapping pelabuhan lama
-   Tetap menggunakan API call untuk generate voyage
-   Auto generate saat semua field required terisi

### 3. Mapping Kota ke Kode

```php
$kotaCodes = [
    'Jakarta' => 'J',
    'Surabaya' => 'S',
    'Medan' => 'M',
    'Makassar' => 'K',
    'Bitung' => 'T',
    'Balikpapan' => 'L',
    'Pontianak' => 'P',
    'Banjarmasin' => 'N',
    'Batam' => 'B',
    'Semarang' => 'G',
    'Palembang' => 'A',
    'Denpasar' => 'D',
    'Jayapura' => 'Y',
    'Sorong' => 'O',
    'Ambon' => 'Z',
    'Tanjung Pinang' => 'T'
];
```

## Data Sample yang Ditambahkan

### Master Kapal dengan Nickname

-   KM SEKAR PERMATA → Nickname: SP
-   KM ALEXINDO 1 → Nickname: A1
-   KM ALKEN PRINCESS → Nickname: AP
-   dll. (Auto generated nickname untuk kapal existing)

### Master Pelabuhan dengan Kota

-   Sunda Kelapa → Jakarta
-   Batu Ampar → Batam
-   Sri Bintan Pura → Tanjung Pinang

## Testing

✅ **Test Result:**

-   KM SEKAR PERMATA (Jakarta → Batam) = **SP01JB25**
-   KM ALEXINDO 1 (Jakarta → Tanjung Pinang) = **A101JT25**

## Route API

-   **Endpoint**: `GET /api/pergerakan-kapal/generate-voyage`
-   **Parameters**: `nama_kapal`, `pelabuhan_asal`, `pelabuhan_tujuan`
-   **Response**: `{"voyage_number": "SP01JB25"}`

## Cara Penggunaan

1. Pilih nama kapal (harus ada nickname di master kapal)
2. Pilih pelabuhan asal (harus ada kota di master pelabuhan)
3. Pilih pelabuhan tujuan (harus ada kota di master pelabuhan)
4. Klik tombol "Generate" atau otomatis generate saat semua field terisi
5. Nomor voyage akan muncul dengan format baru

## Error Handling

-   ❌ "Nickname kapal tidak ditemukan" - jika kapal tidak memiliki nickname
-   ❌ "Data pelabuhan tidak ditemukan" - jika pelabuhan tidak ada di master
-   ❌ "Missing required parameters" - jika parameter API tidak lengkap
