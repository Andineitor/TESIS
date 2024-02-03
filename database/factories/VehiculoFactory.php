<?php

namespace Database\Factories;
use Illuminate\Support\Str;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehiculoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tipo_vehiculo' => $this->faker->word,
            'marca' => $this->faker->word,
            'placas' => Str::random(7),
            'numero_pasajero' => $this->faker->numberBetween(1, 5),
            'image_url' => $this->faker->randomElement([
                'https://res.cloudinary.com/db8fwxjlc/image/upload/v1706826714/vehiculos/bmirrmsbzsmhlngqgvax.jpg',
                'https://res.cloudinary.com/db8fwxjlc/image/upload/v1706654474/vehiculos/wkmphdom9nyfayyk7fgr.png',
                'https://res.cloudinary.com/db8fwxjlc/image/upload/v1706826569/vehiculos/bjzy2xdokkmc0mgxgbdd.jpg'
            ]),
            'costo_alquiler' => $this->faker->randomFloat(2, 50, 200),
            // 'contacto' => $this->faker->word,
            'descripcion' => $this->faker->sentence,
         
            'solicitud_id' => function () {
                return Solicitud::factory()->create(['estado' => $this->faker->randomElement(['pendiente', 'aceptado'])])->id;
            },            'user_id' => function () {
                return User::factory()->create()->id;
            },
        ];


        
    }
}
