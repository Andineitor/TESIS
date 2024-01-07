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

    public function testCambioDeEstadoExitoso()
    {
        // Crea un usuario y un vehículo asociado
        $user = User::factory()->create();
        $vehiculo = Vehiculo::factory()->create(['user_id' => $user->id]);

        // Crea una solicitud
        $solicitud = Solicitud::factory()->create();

        // Simula un usuario autenticado
        $this->actingAs($user);

        // Realiza una solicitud directa a la API para cambiar el estado
        $response = $this->json('POST', "/api/estado/{$vehiculo->id}", [
            'estado' => $solicitud->id,
        ]);

        // Verifica que la respuesta contiene la información esperada
        $response->assertJson(['message' => 'ID de estado cambiado exitosamente']);
        $response->assertStatus(200);

        // Verifica que el vehículo haya sido actualizado correctamente en la base de datos
        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
            'solicitud_id' => $solicitud->id,
        ]);
    }



    ///
    public function testIndexAceptados()
    {
        try {
            // Obtener todos los vehículos con solicitudes aceptadas
            $vehiculosAceptados = Vehiculo::whereHas('solicitud', function ($query) {
                $query->where('estado', 'aceptados');
            })->get();

            // Realizar una solicitud HTTP al endpoint correspondiente
            $response = $this->get('/aceptados');

            // Verificar que la respuesta tenga un código HTTP 200 (éxito)
            $response->assertStatus(200);

            // Obtener la colección de vehículos desde la respuesta JSON
            $responseVehiculos = collect($response->json('vehiculos_aceptados'));

            // Verificar que la colección de la respuesta tiene la misma cantidad de vehículos aceptados
            $this->assertCount($vehiculosAceptados->count(), $responseVehiculos);

            return response()->json(['success' => true, 'vehiculos_aceptados' => $responseVehiculos]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener los vehículos aceptados. ' . $e->getMessage()]);
        }
    }


    public function testIndexPendientes()
    {
        try {
            // Obtener todos los vehículos con solicitudes pendientes
            $vehiculosAceptados = Vehiculo::whereHas('solicitud', function ($query) {
                $query->where('estado', 'pendiente');
            })->get();

            // Realizar una solicitud HTTP al endpoint correspondiente
            $response = $this->get('/aceptados');

            // Verificar que la respuesta tenga un código HTTP 200 (éxito)
            $response->assertStatus(200);

            // Obtener la colección de vehículos desde la respuesta JSON
            $responseVehiculos = collect($response->json('vehiculos_aceptados'));

            // Verificar que la colección de la respuesta tiene la misma cantidad de vehículos aceptados
            $this->assertCount($vehiculosAceptados->count(), $responseVehiculos);

            return response()->json(['success' => true, 'vehiculos_aceptados' => $responseVehiculos]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener los vehículos aceptados. ' . $e->getMessage()]);
        }
    }
}