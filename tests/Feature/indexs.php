<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class indexs extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_vehicles_for_authenticated_user()
    {
        // Crea un usuario y vehículos asociados para la prueba
        $user = User::factory()->create();
        $vehiculos = Vehiculo::factory(3)->create(['user_id' => $user->id]);

        // Actúa como el usuario autenticado
        $this->actingAs($user);

        // Realiza la solicitud a la ruta del controlador
        $response = $this->getJson('/ruta-de-tu-controlador');

        // Asegúrate de que la respuesta sea exitosa y tenga la estructura esperada
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'estadoPropietario' => $vehiculos->toArray(),
            ]);
    }

    /** @test */
    public function it_returns_error_for_unauthenticated_user()
    {
        // Realiza la solicitud a la ruta del controlador sin autenticación
        $response = $this->getJson('/ruta-de-tu-controlador');

        // Asegúrate de que la respuesta sea un error y tenga el mensaje esperado
        $response->assertStatus(200)
            ->assertJson([
                'success' => false,
                'message' => 'Usuario no autenticado',
            ]);
    }
}
