<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coa;

class CoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coas = [
            // Existing data
            [
                'nomor_akun' => '1001',
                'nama_akun' => 'Kas',
                'tipe_akun' => 'Aset',
                'saldo' => 5000000.00,
            ],
            [
                'nomor_akun' => '1002',
                'nama_akun' => 'Bank BCA',
                'tipe_akun' => 'Aset',
                'saldo' => 15000000.00,
            ],
            [
                'nomor_akun' => '2001',
                'nama_akun' => 'Hutang Usaha',
                'tipe_akun' => 'Kewajiban',
                'saldo' => 2500000.00,
            ],
            [
                'nomor_akun' => '3001',
                'nama_akun' => 'Modal',
                'tipe_akun' => 'Ekuitas',
                'saldo' => 20000000.00,
            ],
            [
                'nomor_akun' => '4001',
                'nama_akun' => 'Pendapatan Sewa Kontainer',
                'tipe_akun' => 'Pendapatan',
                'saldo' => 7500000.00,
            ],
            [
                'nomor_akun' => '5001',
                'nama_akun' => 'Biaya Operasional',
                'tipe_akun' => 'Beban',
                'saldo' => 1200000.00,
            ],
            [
                'nomor_akun' => '1003',
                'nama_akun' => 'Piutang Usaha',
                'tipe_akun' => 'Aset',
                'saldo' => 3500000.00,
            ],
            [
                'nomor_akun' => '2002',
                'nama_akun' => 'Hutang Bank',
                'tipe_akun' => 'Kewajiban',
                'saldo' => 8000000.00,
            ],

            // Additional COA data for pagination testing
            [
                'nomor_akun' => '1004',
                'nama_akun' => 'Bank Mandiri',
                'tipe_akun' => 'Aset',
                'saldo' => 12000000.00,
            ],
            [
                'nomor_akun' => '1005',
                'nama_akun' => 'Bank BRI',
                'tipe_akun' => 'Aset',
                'saldo' => 18000000.00,
            ],
            [
                'nomor_akun' => '1006',
                'nama_akun' => 'Persediaan Barang',
                'tipe_akun' => 'Aset',
                'saldo' => 5500000.00,
            ],
            [
                'nomor_akun' => '1007',
                'nama_akun' => 'Peralatan Kantor',
                'tipe_akun' => 'Aset',
                'saldo' => 25000000.00,
            ],
            [
                'nomor_akun' => '1008',
                'nama_akun' => 'Kendaraan Operasional',
                'tipe_akun' => 'Aset',
                'saldo' => 45000000.00,
            ],
            [
                'nomor_akun' => '1009',
                'nama_akun' => 'Bangunan',
                'tipe_akun' => 'Aset',
                'saldo' => 150000000.00,
            ],
            [
                'nomor_akun' => '1010',
                'nama_akun' => 'Tanah',
                'tipe_akun' => 'Aset',
                'saldo' => 75000000.00,
            ],
            [
                'nomor_akun' => '2003',
                'nama_akun' => 'Hutang Dagang',
                'tipe_akun' => 'Kewajiban',
                'saldo' => 4200000.00,
            ],
            [
                'nomor_akun' => '2004',
                'nama_akun' => 'Hutang Pajak',
                'tipe_akun' => 'Kewajiban',
                'saldo' => 1800000.00,
            ],
            [
                'nomor_akun' => '2005',
                'nama_akun' => 'Hutang Leasing',
                'tipe_akun' => 'Kewajiban',
                'saldo' => 35000000.00,
            ],
            [
                'nomor_akun' => '3002',
                'nama_akun' => 'Laba Ditahan',
                'tipe_akun' => 'Ekuitas',
                'saldo' => 25000000.00,
            ],
            [
                'nomor_akun' => '3003',
                'nama_akun' => 'Cadangan Umum',
                'tipe_akun' => 'Ekuitas',
                'saldo' => 5000000.00,
            ],
            [
                'nomor_akun' => '4002',
                'nama_akun' => 'Pendapatan Jasa',
                'tipe_akun' => 'Pendapatan',
                'saldo' => 12500000.00,
            ],
            [
                'nomor_akun' => '4003',
                'nama_akun' => 'Pendapatan Bunga',
                'tipe_akun' => 'Pendapatan',
                'saldo' => 850000.00,
            ],
            [
                'nomor_akun' => '4004',
                'nama_akun' => 'Pendapatan Lain-lain',
                'tipe_akun' => 'Pendapatan',
                'saldo' => 2100000.00,
            ],
            [
                'nomor_akun' => '5002',
                'nama_akun' => 'Biaya Gaji',
                'tipe_akun' => 'Beban',
                'saldo' => 8500000.00,
            ],
            [
                'nomor_akun' => '5003',
                'nama_akun' => 'Biaya Transportasi',
                'tipe_akun' => 'Beban',
                'saldo' => 3200000.00,
            ],
            [
                'nomor_akun' => '5004',
                'nama_akun' => 'Biaya Listrik',
                'tipe_akun' => 'Beban',
                'saldo' => 1800000.00,
            ],
            [
                'nomor_akun' => '5005',
                'nama_akun' => 'Biaya Telepon',
                'tipe_akun' => 'Beban',
                'saldo' => 950000.00,
            ],
            [
                'nomor_akun' => '5006',
                'nama_akun' => 'Biaya Sewa',
                'tipe_akun' => 'Beban',
                'saldo' => 5500000.00,
            ],
            [
                'nomor_akun' => '5007',
                'nama_akun' => 'Biaya Asuransi',
                'tipe_akun' => 'Beban',
                'saldo' => 1200000.00,
            ],
            [
                'nomor_akun' => '5008',
                'nama_akun' => 'Biaya Perawatan',
                'tipe_akun' => 'Beban',
                'saldo' => 2800000.00,
            ],
            [
                'nomor_akun' => '5009',
                'nama_akun' => 'Biaya Administrasi',
                'tipe_akun' => 'Beban',
                'saldo' => 1650000.00,
            ],
            [
                'nomor_akun' => '5010',
                'nama_akun' => 'Biaya Promosi',
                'tipe_akun' => 'Beban',
                'saldo' => 3200000.00,
            ],
            [
                'nomor_akun' => '1011',
                'nama_akun' => 'Investasi Jangka Panjang',
                'tipe_akun' => 'Aset',
                'saldo' => 30000000.00,
            ],
            [
                'nomor_akun' => '1012',
                'nama_akun' => 'Piutang Karyawan',
                'tipe_akun' => 'Aset',
                'saldo' => 850000.00,
            ],
            [
                'nomor_akun' => '1013',
                'nama_akun' => 'Uang Muka Pembelian',
                'tipe_akun' => 'Aset',
                'saldo' => 5200000.00,
            ],
            [
                'nomor_akun' => '2006',
                'nama_akun' => 'Hutang Jangka Pendek',
                'tipe_akun' => 'Kewajiban',
                'saldo' => 15000000.00,
            ],
            [
                'nomor_akun' => '2007',
                'nama_akun' => 'Hutang Jangka Panjang',
                'tipe_akun' => 'Kewajiban',
                'saldo' => 75000000.00,
            ],
            [
                'nomor_akun' => '3004',
                'nama_akun' => 'Saham Treasury',
                'tipe_akun' => 'Ekuitas',
                'saldo' => -5000000.00,
            ],
            [
                'nomor_akun' => '4005',
                'nama_akun' => 'Penjualan Barang',
                'tipe_akun' => 'Pendapatan',
                'saldo' => 45000000.00,
            ],
            [
                'nomor_akun' => '4006',
                'nama_akun' => 'Diskon Penjualan',
                'tipe_akun' => 'Pendapatan',
                'saldo' => -1200000.00,
            ],
            [
                'nomor_akun' => '5011',
                'nama_akun' => 'Biaya Bahan Bakar',
                'tipe_akun' => 'Beban',
                'saldo' => 4200000.00,
            ],
            [
                'nomor_akun' => '5012',
                'nama_akun' => 'Biaya Konsumsi',
                'tipe_akun' => 'Beban',
                'saldo' => 950000.00,
            ],
            [
                'nomor_akun' => '5013',
                'nama_akun' => 'Biaya Training',
                'tipe_akun' => 'Beban',
                'saldo' => 1800000.00,
            ],
            [
                'nomor_akun' => '5014',
                'nama_akun' => 'Biaya Audit',
                'tipe_akun' => 'Beban',
                'saldo' => 5500000.00,
            ],
            [
                'nomor_akun' => '5015',
                'nama_akun' => 'Biaya Legal',
                'tipe_akun' => 'Beban',
                'saldo' => 2200000.00,
            ],
            [
                'nomor_akun' => '1014',
                'nama_akun' => 'Deposit Bank',
                'tipe_akun' => 'Aset',
                'saldo' => 25000000.00,
            ],
            [
                'nomor_akun' => '1015',
                'nama_akun' => 'Sertifikat Deposito',
                'tipe_akun' => 'Aset',
                'saldo' => 50000000.00,
            ],
            [
                'nomor_akun' => '1016',
                'nama_akun' => 'Obligasi',
                'tipe_akun' => 'Aset',
                'saldo' => 35000000.00,
            ],
            [
                'nomor_akun' => '2008',
                'nama_akun' => 'Kewajiban Pajak Tangguhan',
                'tipe_akun' => 'Kewajiban',
                'saldo' => 8500000.00,
            ],
            [
                'nomor_akun' => '2009',
                'nama_akun' => 'Hutang Deviden',
                'tipe_akun' => 'Kewajiban',
                'saldo' => 12000000.00,
            ],
            [
                'nomor_akun' => '3005',
                'nama_akun' => 'Laba Tahun Berjalan',
                'tipe_akun' => 'Ekuitas',
                'saldo' => 18000000.00,
            ],
            [
                'nomor_akun' => '4007',
                'nama_akun' => 'Royalti',
                'tipe_akun' => 'Pendapatan',
                'saldo' => 3200000.00,
            ],
            [
                'nomor_akun' => '4008',
                'nama_akun' => 'Pendapatan Sewa Aset',
                'tipe_akun' => 'Pendapatan',
                'saldo' => 9500000.00,
            ],
            [
                'nomor_akun' => '5016',
                'nama_akun' => 'Biaya Penyusutan',
                'tipe_akun' => 'Beban',
                'saldo' => 8500000.00,
            ],
            [
                'nomor_akun' => '5017',
                'nama_akun' => 'Biaya Amortisasi',
                'tipe_akun' => 'Beban',
                'saldo' => 2200000.00,
            ],
            [
                'nomor_akun' => '5018',
                'nama_akun' => 'Biaya Bunga',
                'tipe_akun' => 'Beban',
                'saldo' => 3800000.00,
            ],
            [
                'nomor_akun' => '5019',
                'nama_akun' => 'Biaya Komisi',
                'tipe_akun' => 'Beban',
                'saldo' => 1650000.00,
            ],
            [
                'nomor_akun' => '5020',
                'nama_akun' => 'Biaya Pemasaran',
                'tipe_akun' => 'Beban',
                'saldo' => 7200000.00,
            ],
        ];

        foreach ($coas as $coa) {
            Coa::firstOrCreate(
                ['nomor_akun' => $coa['nomor_akun']],
                $coa
            );
        }
    }
}
