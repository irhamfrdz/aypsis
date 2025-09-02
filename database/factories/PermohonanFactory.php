<?php

namespace Database\Factories;

use App\Models\Permohonan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PermohonanFactory extends Factory
{
    protected $model = Permohonan::class;

    public function definition()
    {
        return [
            'nomor_memo' => 'MEMO-' . $this->faker->unique()->numerify('####'),
            'tanggal_memo' => $this->faker->date('Y-m-d'),
            'tujuan' => $this->faker->city(),
            'kegiatan' => 'pengiriman',
            'ukuran' => '20',
            'jumlah_kontainer' => 1,
            // Default to AYP to avoid vendor-specific behavior in most tests; tests can override as needed
            'vendor_perusahaan' => 'AYP',
            'status' => 'draft',
            'jumlah_uang_jalan' => 100000,
            'total_harga_setelah_adj' => 100000,
            'catatan' => $this->faker->sentence,
        ];
    }
}
