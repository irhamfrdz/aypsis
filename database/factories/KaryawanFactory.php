<?php

namespace Database\Factories;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

class KaryawanFactory extends Factory
{
    protected $model = Karyawan::class;

    public function definition()
    {
        return [
            'nik' => $this->faker->unique()->numerify('##########'),
            'nama_lengkap' => $this->faker->name,
            'nama_panggilan' => $this->faker->firstName,
            'pekerjaan' => 'Supir Truck',
            'plat' => $this->faker->bothify('B #### XX'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
