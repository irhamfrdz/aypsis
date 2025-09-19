<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            ['name' => 'Bank Central Asia (BCA)', 'code' => 'BCA', 'keterangan' => 'Bank swasta terbesar di Indonesia'],
            ['name' => 'Bank Mandiri', 'code' => 'BMRI', 'keterangan' => 'Bank milik negara hasil merger'],
            ['name' => 'Bank Negara Indonesia (BNI)', 'code' => 'BNI', 'keterangan' => 'Bank milik negara'],
            ['name' => 'Bank Rakyat Indonesia (BRI)', 'code' => 'BRI', 'keterangan' => 'Bank milik negara fokus UMKM'],
            ['name' => 'Bank Tabungan Negara (BTN)', 'code' => 'BTN', 'keterangan' => 'Bank milik negara fokus perumahan'],
            ['name' => 'Bank CIMB Niaga', 'code' => 'BNGA', 'keterangan' => 'Bank swasta hasil merger Bank Niaga dan Bank Lippo'],
            ['name' => 'Bank Danamon', 'code' => 'BDMN', 'keterangan' => 'Bank swasta'],
            ['name' => 'Bank Permata', 'code' => 'BNLI', 'keterangan' => 'Bank hasil merger Bank Bali dan Bank Universal'],
            ['name' => 'Bank Mega', 'code' => 'MEGA', 'keterangan' => 'Bank swasta milik CT Corp'],
            ['name' => 'Bank Syariah Indonesia (BSI)', 'code' => 'BSIM', 'keterangan' => 'Bank syariah hasil merger 3 bank syariah BUMN'],
            ['name' => 'Bank BTPN', 'code' => 'BTPN', 'keterangan' => 'Bank swasta fokus UMKM'],
            ['name' => 'Bank OCBC NISP', 'code' => 'NISP', 'keterangan' => 'Bank swasta asal Singapura'],
            ['name' => 'Bank Panin', 'code' => 'PNBN', 'keterangan' => 'Bank swasta'],
            ['name' => 'Bank UOB Indonesia', 'code' => 'UOB', 'keterangan' => 'Bank asing asal Singapura'],
            ['name' => 'Bank DBS Indonesia', 'code' => 'DBS', 'keterangan' => 'Bank asing asal Singapura'],
            ['name' => 'Bank HSBC Indonesia', 'code' => 'HSBC', 'keterangan' => 'Bank asing asal Inggris'],
            ['name' => 'Bank Standard Chartered', 'code' => 'SC', 'keterangan' => 'Bank asing asal Inggris'],
            ['name' => 'Bank Maybank Indonesia', 'code' => 'IBUK', 'keterangan' => 'Bank asing asal Malaysia'],
            ['name' => 'Bank Bukopin', 'code' => 'BBKP', 'keterangan' => 'Bank swasta hasil merger'],
            ['name' => 'Bank Sinarmas', 'code' => 'BSIM', 'keterangan' => 'Bank swasta milik Sinar Mas Group'],
            ['name' => 'Bank Jabar Banten (BJB)', 'code' => 'BJBR', 'keterangan' => 'Bank daerah Jawa Barat dan Banten'],
            ['name' => 'Bank DKI Jakarta', 'code' => 'BDKI', 'keterangan' => 'Bank daerah DKI Jakarta'],
            ['name' => 'Bank Jateng', 'code' => 'BJTM', 'keterangan' => 'Bank daerah Jawa Tengah'],
            ['name' => 'Bank Jatim', 'code' => 'BJTM', 'keterangan' => 'Bank daerah Jawa Timur'],
            ['name' => 'Bank BPD Bali', 'code' => 'BALI', 'keterangan' => 'Bank daerah Bali'],
        ];

        foreach ($banks as $bank) {
            Bank::updateOrCreate(
                ['code' => $bank['code']],
                [
                    'name' => $bank['name'],
                    'keterangan' => $bank['keterangan']
                ]
            );
        }
    }
}
