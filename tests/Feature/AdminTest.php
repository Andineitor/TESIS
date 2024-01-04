<?php

namespace Tests\Feature;

use App\Models\Solicitud;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCambiarEstadoDelVehiculo()
    {
        // Crear un usuario
        $user = User::create([
            'nombre' => 'roberto',
            'apellido' => 'juan',
            'cedula' => '987654321',
            'direccion' => 'andiloor2809@gmail.com',
            'celular' => '123456789',
            'email' => 'andiloor2809@gmail.com',
            'role_id' => 1,
            'password' => 'password123',
        ]);
    

        // Autenticar al usuario usando Sanctum
        Sanctum::actingAs($user);

        // Crear un vehículo
        $vehiculo = Vehiculo::factory()->create();

        // Crear una solicitud
        $solicitud = Solicitud::factory()->create();

        // Realizar una solicitud para cambiar el estado del vehículo
        $response = $this->json('put', "/estado/{id}", [
            'estado' => $solicitud->id,
        ]);

        // Verificar que la solicitud se haya procesado correctamente
        $response->assertStatus(200)
            ->assertJson(['message' => 'ID de estado cambiado exitosamente']);

        // Verificar que el estado del vehículo se haya actualizado en la base de datos
        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
            'solicitud_id' => $solicitud->id,
        ]);
    }
}
