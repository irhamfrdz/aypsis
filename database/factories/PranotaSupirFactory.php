<?php

namespace Database\Factories;

use App\Models\PranotaSupir;
use Illuminate\Database\Eloquent\Factories\Factory;

class PranotaSupirFactory extends Factory
{
    protected $model = PranotaSupir::class;

    public function definition()
    {
        return [
            'nomor_pranota' => 'PR-' . $this->faker->unique()->numerify('#####'),
            'tanggal_pranota' => $this->faker->date('Y-m-d'),
            'total_biaya_memo' => $this->faker->randomFloat(2, 50000, 500000),
            'adjustment' => null,
            'alasan_adjustment' => null,
            'total_biaya_pranota' => $this->faker->randomFloat(2, 50000, 500000),
            'catatan' => $this->faker->sentence,
            'status_pembayaran' => 'Belum Lunas',
        ];
    }
}
