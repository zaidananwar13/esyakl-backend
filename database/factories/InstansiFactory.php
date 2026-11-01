<?php

namespace Database\Factories;

use App\Models\Instansi;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class InstansiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Instansi::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_instansi' => Instansi::factory(),
            'nama' => $this->faker->company(),
            'lokasi' => $this->faker->address(),
            'foto' => $this->faker->image(),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ];
    }
}
