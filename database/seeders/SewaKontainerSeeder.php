<?php

namespace Database\Seeders;

use App\Models\SewaCustomer;
use App\Models\SewaInvoice;
use App\Models\SewaKontainer;
use App\Models\SewaTagihan;
use App\Models\SewaTarif;
use App\Models\SewaTipe;
use App\Models\SewaTransaksi;
use App\Models\SewaUkuran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SewaKontainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = base_path('sewa.json');
        if (! file_exists($filePath)) {
            $this->command->error('File sewa.json tidak ditemukan di root directory!');

            return;
        }

        $data = json_decode(file_get_contents($filePath), true);
        if (! $data) {
            $this->command->error('Format file sewa.json tidak valid!');

            return;
        }

        // Truncate Transactions, Billing, Invoices with foreign key checks disabled
        $this->command->info('Mengosongkan data billing, pembayaran, dan kontrak...');
        Schema::disableForeignKeyConstraints();
        SewaTagihan::truncate();
        SewaInvoice::truncate();
        SewaTransaksi::truncate();
        Schema::enableForeignKeyConstraints();

        DB::transaction(function () use ($data) {
            // 1. Customers
            if (isset($data['customers'])) {
                $this->command->info('Mengimpor Customers...');
                foreach ($data['customers'] as $c) {
                    SewaCustomer::updateOrCreate(
                        ['id_customer' => $c['id_customer']],
                        ['nama_customer' => $c['nama_customer']]
                    );
                }
            }

            // 2. Tipes
            if (isset($data['tipes'])) {
                $this->command->info('Mengimpor Tipes...');
                foreach ($data['tipes'] as $t) {
                    SewaTipe::updateOrCreate(
                        ['id_tipe' => $t['id_tipe']],
                        ['nama_tipe' => $t['nama_tipe']]
                    );
                }
            }

            // 3. Ukurans
            if (isset($data['ukurans'])) {
                $this->command->info('Mengimpor Ukurans...');
                foreach ($data['ukurans'] as $u) {
                    SewaUkuran::updateOrCreate(
                        ['id_ukuran' => $u['id_ukuran']],
                        ['deskripsi_ukuran' => $u['deskripsi_ukuran']]
                    );
                }
            }

            // 4. Kontainers
            if (isset($data['kontainers'])) {
                $this->command->info('Mengimpor Kontainers...');
                foreach ($data['kontainers'] as $k) {
                    SewaKontainer::updateOrCreate(
                        ['no_kontainer' => $k['no_kontainer']],
                        [
                            'id_customer' => $k['id_customer'],
                            'id_tipe' => $k['id_tipe'],
                            'id_ukuran' => $k['id_ukuran'],
                            'status_aktif' => $k['status_aktif'] ?? true,
                        ]
                    );
                }
            }

            // 5. Tarifs
            if (isset($data['tarifs'])) {
                $this->command->info('Mengimpor Tarifs...');
                foreach ($data['tarifs'] as $tr) {
                    SewaTarif::updateOrCreate(
                        ['id_tarif' => $tr['id_tarif']],
                        [
                            'id_customer' => $tr['id_customer'],
                            'id_tipe' => $tr['id_tipe'],
                            'id_ukuran' => $tr['id_ukuran'],
                            'tarif_bulanan' => $tr['tarif_bulanan'] ?? 0,
                            'tarif_harian' => $tr['tarif_harian'] ?? 0,
                            'tanggal_mulai_berlaku' => $tr['tanggal_mulai_berlaku'],
                            'tanggal_akhir_berlaku' => $tr['tanggal_akhir_berlaku'] ?? null,
                        ]
                    );
                }
            }
        });

        $this->command->info('Impor data master dari sewa.json selesai, transaksi dikosongkan!');
    }
}
