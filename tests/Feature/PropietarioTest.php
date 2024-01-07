<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropietarioTest extends TestCase
{
    use RefreshDatabase;

    // Prueba para verificar que el registro de un vehículo sin proporcionar una imagen sea exitoso
   // Prueba para verificar que el registro de un vehículo sin proporcionar una imagen sea exitoso
   public function testRegistroExitoso()
   {
       // Crea un usuario usando factory
       $user = User::factory()->create();

       $response = $this->actingAs($user)
           ->json('POST', 'api/vehiculos', [
               'placas' => 'some_plate',
               'tipo_vehiculo' => 'some_type',
               'contacto' => 'some_contact',
               'marca' => 'a',
               'numero_pasajero' => 1,
               'costo_alquiler' => 1,
               'descripcion' => 'dwsw',
               'user_id' => 1,
               'solicitud_id' => '1',
            'image_url' => 'dummy_image_url', 
           ]);

       // Comprueba que el estado sea exitoso con estado 200
       $response->assertStatus(200)
           ->assertJson([
               'success' => true,
               'message' => 'Vehículo registrado con éxito. Placas: some_plate',
           ]);

       $this->assertDatabaseHas('vehiculos', ['placas' => 'some_plate']);
   }
   


   
   public function testIndex()
{
    try {
        // Simula la autenticación del usuario
        $user = User::factory()->create();
        $this->actingAs($user);

        // Realiza una solicitud HTTP al endpoint correspondiente
        $response = $this->get('/tu-endpoint');

        // Verifica que la respuesta tenga un código HTTP 200 (éxito)
        $response->assertStatus(200);

        // Obtén la colección de vehículos desde la respuesta JSON
        $responseVehiculos = collect($response->json('estadoPropietario'));

        // Verifica que la colección de la respuesta no esté vacía
        $this->assertNotEmpty($responseVehiculos);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error al obtener los vehículos. ' . $e->getMessage()]);
    }
}


}
