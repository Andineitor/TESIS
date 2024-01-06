<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehiculo;
use Faker\Factory;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PropietarioTest extends TestCase
{
    use RefreshDatabase;

    // Prueba para verificar que el registro de un vehículo sin proporcionar una imagen sea exitoso
    public function testRegistroExitoso()
    {

        //crea un usauario usando factory 
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->json('POST', 'api/vehiculos', [
                'placas' => 'some_plate',
                'tipo_vehiculo' => 'some_type',
                'contacto' => 'some_contact',
               
            ]);

            //comprueba que el estado se exitoso con estado 200
        $response->assertStatus(200)
            ->assertJson([
                'success' => false,
                'message' => 'Error al registrar el vehículo. The marca field is required. (and 1 more error)',
            ]);

        $this->assertDatabaseMissing('vehiculos', ['placas' => 'some_plate']);
    }
}