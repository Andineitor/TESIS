<?php

namespace Tests\Feature;

use App\Models\Vehiculo;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RecuperacionTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexAceptados()
    {
        // Crea un usuario y autentícalo
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Crea vehículos con solicitudes aceptadas
        $vehiculosAceptados = Vehiculo::factory()->has(
            Solicitud::factory()->state(['estado' => 2])
        )->count(3)->create();

        // Hace la solicitud a la ruta indexAceptados
        $response = $this->json('get', 'api/solicitudes/aceptados');

        // Verifica que la respuesta sea exitosa
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verifica que la respuesta contiene los vehículos aceptados
        $response->assertJsonCount(count($vehiculosAceptados), 'vehiculos_aceptados');
    }

    public function testIndexPendientes()
    {
        // Crea un usuario y autentícalo
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Crea vehículos con solicitudes pendientes
        $vehiculosPendientes = Vehiculo::factory()->has(
            Solicitud::factory()->state(['estado' => 1])
        )->count(3)->create();

        // Hace la solicitud a la ruta indexPendientes
        $response = $this->json('get', 'api/solicitudes/pendientes');

        // Verifica que la respuesta sea exitosa
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verifica que la respuesta contiene los vehículos pendientes
        $response->assertJsonCount(count($vehiculosPendientes), 'vehiculos_pendientes');
    }
}
