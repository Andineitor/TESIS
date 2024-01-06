<?php

namespace Database\Factories;
use Illuminate\Support\Str;

use App\Models\Solicitud;
use App\Models\User;
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
            'tipo_vehiculo' => $this->faker->word,
            'marca' => $this->faker->word,
            'placas' => Str::random(7),
            'numero_pasajero' => $this->faker->numberBetween(1, 5),
            'image_url' => $this->faker->imageUrl(),
            'costo_alquiler' => $this->faker->randomFloat(2, 50, 200),
            'contacto' => $this->faker->word,
            'descripcion' => $this->faker->sentence,
            'solicitud_id' => Solicitud::factory()->create()->id,
            'user_id' => function () {
                // Aquí asignamos el user_id por defecto. Puedes cambiar el valor según tus necesidades.
                return User::factory()->create()->id;
            },
        ];
    }
}
