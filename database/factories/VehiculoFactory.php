<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehiculo>
 */
class VehiculoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'tipo_vehiculo' => $this->faker->word,
            'marca' => $this->faker->word,
            'placas' => $this->faker->unique()->word,
            'numero_pasajero' => $this->faker->numberBetween(1, 10),
            'image_url' => $this->faker->imageUrl(),
            'costo_alquiler' => $this->faker->randomFloat(2, 50, 500),
            'contacto' => $this->faker->word,
            'descripcion' => $this->faker->sentence,
            'solicitud_id' => 1,
            'contrato_id' => null,
        ];
    }
}
