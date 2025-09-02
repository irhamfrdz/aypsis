<?php
namespace Database\Factories;

use App\Models\Kontainer;
use Illuminate\Database\Eloquent\Factories\Factory;

class KontainerFactory extends Factory
{
    protected $model = Kontainer::class;

    public function definition()
    {
        return [
            'awalan_kontainer' => $this->faker->lexify('ABCD'),
            'nomor_seri_kontainer' => $this->faker->numerify('######'),
            'akhiran_kontainer' => $this->faker->randomDigitNotNull,
            'nomor_seri_gabungan' => $this->faker->lexify('ABCD').$this->faker->numerify('######').$this->faker->randomDigitNotNull,
            'ukuran' => $this->faker->randomElement(['20', '40']),
            'tipe_kontainer' => $this->faker->word,
            'status' => 'Tersedia',
            'pemilik_kontainer' => $this->faker->company,
            'tahun_pembuatan' => $this->faker->year,
            'kontainer_asal' => $this->faker->city,
            'tanggal_beli' => $this->faker->date(),
            'tanggal_jual' => null,
            'kondisi_kontainer' => 'Baik',
            'tanggal_masuk_sewa' => null,
            'tanggal_selesai_sewa' => null,
            'keterangan' => $this->faker->sentence,
            'keterangan1' => $this->faker->sentence,
            'keterangan2' => $this->faker->sentence,
        ];
    }
}
