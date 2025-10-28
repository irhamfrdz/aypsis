<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TandaTerimaLcl;
use App\Models\TandaTerimaLclItem;
use App\Models\Term;
use App\Models\JenisBarang;
use App\Models\MasterTujuanKirim;
use App\Models\User;
use Carbon\Carbon;

class TandaTerimaLclSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada data referensi yang diperlukan
        $terms = Term::all();
        $jenisBarangs = JenisBarang::all();
        $tujuanKirims = MasterTujuanKirim::all();
        $users = User::all();

        if ($terms->isEmpty() || $jenisBarangs->isEmpty() || $tujuanKirims->isEmpty()) {
            $this->command->warn('Pastikan data Term, JenisBarang, dan MasterTujuanKirim sudah ada sebelum menjalankan seeder ini.');
            return;
        }

        $tandaTerimaData = [
            [
                'nomor_tanda_terima' => 'TTR-LCL-001-2025',
                'tanggal_tanda_terima' => Carbon::now()->subDays(7),
                'no_surat_jalan_customer' => 'SJ-CUS-001-2025',
                'term_id' => $terms->first()->id,
                'nama_penerima' => 'PT. Maju Bersama Sejahtera',
                'pic_penerima' => 'Budi Santoso',
                'telepon_penerima' => '081234567890',
                'alamat_penerima' => 'Jl. Raya Industri No. 45, Kawasan Industri Pulogadung, Jakarta Timur 13260',
                'nama_pengirim' => 'PT. Sumber Makmur',
                'pic_pengirim' => 'Sari Dewi',
                'telepon_pengirim' => '081987654321',
                'alamat_pengirim' => 'Jl. Gatot Subroto No. 123, Menteng, Jakarta Pusat 10270',
                'nama_barang' => 'Spare Part Mesin Produksi',
                'jenis_barang_id' => $jenisBarangs->first()->id,
                'kuantitas' => 15,
                'keterangan_barang' => 'Spare part mesin produksi untuk pabrik tekstil, kondisi baru dalam kemasan original',
                'supir' => 'Ahmad Wijaya',
                'no_plat' => 'B 1234 ABC',
                'tujuan_pengiriman_id' => $tujuanKirims->first()->id,
                'tipe_kontainer' => 'lcl',
                'nomor_kontainer' => 'MRKU1234567',
                'size_kontainer' => '20ft',
                'status' => 'confirmed',
                'created_by' => $users->isNotEmpty() ? $users->first()->id : null,
                'updated_by' => $users->isNotEmpty() ? $users->first()->id : null,
            ],
            [
                'nomor_tanda_terima' => 'TTR-LCL-002-2025',
                'tanggal_tanda_terima' => Carbon::now()->subDays(5),
                'no_surat_jalan_customer' => 'SJ-CUS-002-2025',
                'term_id' => $terms->first()->id,
                'nama_penerima' => 'CV. Berkah Jaya',
                'pic_penerima' => 'Rina Sari',
                'telepon_penerima' => '081555666777',
                'alamat_penerima' => 'Jl. Ahmad Yani No. 88, Surabaya, Jawa Timur 60234',
                'nama_pengirim' => 'PT. Global Trading',
                'pic_pengirim' => 'Hendra Kusuma',
                'telepon_pengirim' => '081444555666',
                'alamat_pengirim' => 'Jl. Sudirman No. 567, Medan, Sumatera Utara 20111',
                'nama_barang' => 'Peralatan Elektronik',
                'jenis_barang_id' => $jenisBarangs->skip(1)->first()->id ?? $jenisBarangs->first()->id,
                'kuantitas' => 8,
                'keterangan_barang' => 'Peralatan elektronik untuk kantor, sudah dikemas dengan bubble wrap',
                'supir' => 'Dodi Rahman',
                'no_plat' => 'L 5678 DEF',
                'tujuan_pengiriman_id' => $tujuanKirims->skip(1)->first()->id ?? $tujuanKirims->first()->id,
                'tipe_kontainer' => 'lcl',
                'nomor_kontainer' => 'HLBU2345678',
                'size_kontainer' => '40ft',
                'status' => 'draft',
                'created_by' => $users->isNotEmpty() ? $users->first()->id : null,
                'updated_by' => $users->isNotEmpty() ? $users->first()->id : null,
            ],
            [
                'nomor_tanda_terima' => 'TTR-LCL-003-2025',
                'tanggal_tanda_terima' => Carbon::now()->subDays(3),
                'no_surat_jalan_customer' => null, // Tanpa surat jalan customer
                'term_id' => $terms->skip(1)->first()->id ?? $terms->first()->id,
                'nama_penerima' => 'Toko Sumber Rejeki',
                'pic_penerima' => 'Ibu Siti',
                'telepon_penerima' => '081222333444',
                'alamat_penerima' => 'Jl. Pasar Baru No. 15, Bandung, Jawa Barat 40111',
                'nama_pengirim' => 'PT. Distributor Nusantara',
                'pic_pengirim' => 'Pak Agus',
                'telepon_pengirim' => '081777888999',
                'alamat_pengirim' => 'Jl. Malioboro No. 234, Yogyakarta, DIY 55271',
                'nama_barang' => 'Produk Fashion',
                'jenis_barang_id' => $jenisBarangs->skip(2)->first()->id ?? $jenisBarangs->first()->id,
                'kuantitas' => 25,
                'keterangan_barang' => 'Pakaian jadi siap jual untuk toko retail',
                'supir' => 'Supir Customer',
                'no_plat' => 'Plat Customer',
                'tujuan_pengiriman_id' => $tujuanKirims->skip(2)->first()->id ?? $tujuanKirims->first()->id,
                'tipe_kontainer' => 'lcl',
                'nomor_kontainer' => null, // Belum ditentukan kontainernya
                'size_kontainer' => '40hc',
                'status' => 'delivered',
                'created_by' => $users->isNotEmpty() ? $users->first()->id : null,
                'updated_by' => $users->isNotEmpty() ? $users->first()->id : null,
            ],
            [
                'nomor_tanda_terima' => 'TTR-LCL-004-2025',
                'tanggal_tanda_terima' => Carbon::now()->subDays(1),
                'no_surat_jalan_customer' => 'SJ-CUS-004-2025',
                'term_id' => $terms->first()->id,
                'nama_penerima' => 'PT. Teknologi Masa Depan',
                'pic_penerima' => 'Bambang Prasetyo',
                'telepon_penerima' => '081666777888',
                'alamat_penerima' => 'Jl. TB Simatupang No. 99, Jakarta Selatan 12560',
                'nama_pengirim' => 'CV. Import Export',
                'pic_pengirim' => 'Lisa Wati',
                'telepon_pengirim' => '081333444555',
                'alamat_pengirim' => 'Jl. Diponegoro No. 456, Semarang, Jawa Tengah 50241',
                'nama_barang' => 'Komponen IT',
                'jenis_barang_id' => $jenisBarangs->skip(3)->first()->id ?? $jenisBarangs->first()->id,
                'kuantitas' => 12,
                'keterangan_barang' => 'Komponen IT untuk server, barang fragile',
                'supir' => 'Joko Susanto',
                'no_plat' => 'F 9999 GHI',
                'tujuan_pengiriman_id' => $tujuanKirims->skip(3)->first()->id ?? $tujuanKirims->first()->id,
                'tipe_kontainer' => 'lcl',
                'nomor_kontainer' => 'OOLU3456789',
                'size_kontainer' => '45ft',
                'status' => 'confirmed',
                'created_by' => $users->isNotEmpty() ? $users->first()->id : null,
                'updated_by' => $users->isNotEmpty() ? $users->first()->id : null,
            ],
            [
                'nomor_tanda_terima' => 'TTR-LCL-005-2025',
                'tanggal_tanda_terima' => Carbon::now(),
                'no_surat_jalan_customer' => 'SJ-CUS-005-2025',
                'term_id' => $terms->skip(2)->first()->id ?? $terms->first()->id,
                'nama_penerima' => 'UD. Harapan Baru',
                'pic_penerima' => null, // Tanpa PIC
                'telepon_penerima' => null, // Tanpa telepon
                'alamat_penerima' => 'Jl. Pemuda No. 77, Makassar, Sulawesi Selatan 90111',
                'nama_pengirim' => 'PT. Supplier Utama',
                'pic_pengirim' => 'Eko Prasetio',
                'telepon_pengirim' => '081111222333',
                'alamat_pengirim' => 'Jl. Asia Afrika No. 789, Denpasar, Bali 80361',
                'nama_barang' => 'Produk Kerajinan',
                'jenis_barang_id' => $jenisBarangs->skip(4)->first()->id ?? $jenisBarangs->first()->id,
                'kuantitas' => 30,
                'keterangan_barang' => 'Kerajinan tangan untuk ekspor, dikemas khusus',
                'supir' => 'Made Wirawan',
                'no_plat' => 'DK 1111 JKL',
                'tujuan_pengiriman_id' => $tujuanKirims->skip(4)->first()->id ?? $tujuanKirims->first()->id,
                'tipe_kontainer' => 'lcl',
                'nomor_kontainer' => 'CSVU4567890',
                'size_kontainer' => '20ft',
                'status' => 'draft',
                'created_by' => $users->isNotEmpty() ? $users->first()->id : null,
                'updated_by' => $users->isNotEmpty() ? $users->first()->id : null,
            ],
        ];

        foreach ($tandaTerimaData as $index => $data) {
            $this->command->info("Membuat Tanda Terima LCL: {$data['nomor_tanda_terima']}");
            
            // Create main record
            $tandaTerima = TandaTerimaLcl::create($data);

            // Create sample items for each tanda terima
            $this->createSampleItems($tandaTerima, $index + 1);
        }

        $this->command->info('Seeder TandaTerimaLcl berhasil dijalankan!');
    }

    /**
     * Create sample items for tanda terima LCL
     */
    private function createSampleItems(TandaTerimaLcl $tandaTerima, int $recordNumber): void
    {
        // Sample items data based on record number
        $itemsData = [
            // Record 1: 2 items
            1 => [
                ['panjang' => 150.00, 'lebar' => 100.00, 'tinggi' => 80.00, 'tonase' => 0.50],
                ['panjang' => 120.00, 'lebar' => 80.00, 'tinggi' => 60.00, 'tonase' => 0.30],
            ],
            // Record 2: 1 item
            2 => [
                ['panjang' => 200.00, 'lebar' => 150.00, 'tinggi' => 100.00, 'tonase' => 0.75],
            ],
            // Record 3: 3 items
            3 => [
                ['panjang' => 100.00, 'lebar' => 80.00, 'tinggi' => 60.00, 'tonase' => 0.25],
                ['panjang' => 110.00, 'lebar' => 85.00, 'tinggi' => 65.00, 'tonase' => 0.28],
                ['panjang' => 95.00, 'lebar' => 75.00, 'tinggi' => 55.00, 'tonase' => 0.22],
            ],
            // Record 4: 2 items
            4 => [
                ['panjang' => 180.00, 'lebar' => 120.00, 'tinggi' => 90.00, 'tonase' => 0.60],
                ['panjang' => 160.00, 'lebar' => 110.00, 'tinggi' => 85.00, 'tonase' => 0.55],
            ],
            // Record 5: 1 item
            5 => [
                ['panjang' => 250.00, 'lebar' => 200.00, 'tinggi' => 120.00, 'tonase' => 1.20],
            ],
        ];

        $items = $itemsData[$recordNumber] ?? [
            ['panjang' => 100.00, 'lebar' => 80.00, 'tinggi' => 60.00, 'tonase' => 0.30],
        ];

        foreach ($items as $itemNumber => $item) {
            // Calculate volume (panjang x lebar x tinggi) / 1,000,000 to convert cm³ to m³
            $volume = ($item['panjang'] * $item['lebar'] * $item['tinggi']) / 1000000;

            TandaTerimaLclItem::create([
                'tanda_terima_lcl_id' => $tandaTerima->id,
                'item_number' => $itemNumber + 1,
                'panjang' => $item['panjang'],
                'lebar' => $item['lebar'],
                'tinggi' => $item['tinggi'],
                'meter_kubik' => $volume,
                'tonase' => $item['tonase'],
            ]);

            $itemNum = $itemNumber + 1;
            $this->command->info("  - Item {$itemNum}: {$item['panjang']}x{$item['lebar']}x{$item['tinggi']} cm, Volume: " . number_format($volume, 6) . " m³");
        }
    }
}