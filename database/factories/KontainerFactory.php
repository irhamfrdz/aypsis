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
            'tanggal_selesai_sewa' => null,
            'keterangan' => $this->faker->sentence,
        ];
    }
}
