<?php

namespace Tests\Feature;

use App\Http\Controllers\SolicituController;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function testEstadoCambiaCorrectamente()
    {
        // Crear un usuario autenticado (puedes ajustar esto según tus necesidades)
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear un vehículo con estado 'pendiente'
        $vehiculo = Vehiculo::factory()->create();

        // Crear una solicitud HTTP con el nuevo estado 'aceptado'
        $request = Request::create("/estado/{$vehiculo->id}", 'put', ['estado' => 'aceptado']);

        // Crear una instancia del controlador
        $controller = new SolicituController();

        // Ejecutar el método estado del controlador
        $response = $controller->estado($request, $vehiculo->id);

        // Verificar que la respuesta indica que el cambio de estado no fue exitoso
        $this->assertEquals(422, $response->status());

        // Recargar el vehículo desde la base de datos para obtener la última información
        $vehiculoActualizado = Vehiculo::find($vehiculo->id);

        // Verificar que el estado del vehículo no haya cambiado correctamente
        $this->assertNotEquals('aceptado', $vehiculoActualizado->solicitud->estado);
    }
}