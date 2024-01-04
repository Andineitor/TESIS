<?php

namespace Tests\Feature;

use App\Models\Contrato;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PropietarioTest extends TestCase
{
    use RefreshDatabase;

    // Prueba para verificar que el registro de un vehículo sin proporcionar una imagen sea exitoso
    public function testRegistroExitoso()
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

        // Autenticar al usuario utilizando Sanctum
        Sanctum::actingAs($user);

        // Realizar la solicitud para registrar un vehículo (sin proporcionar una imagen)
        $response = $this->json('post', 'api/vehiculos', [
            'tipo_vehiculo' => 'Automóvil',
            'marca' => 'Toyota',
            'placas' => 'ABC123',
            'numero_pasajero' => 5,
            'costo_alquiler' => 100.00, 
            'contacto' => '12345678',
            'descripcion' => 'Hola Mundo',
        ]);

        // Verificar el estado de la respuesta
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Asegurar que el vehículo está registrado en la base de datos
        $this->assertDatabaseHas('vehiculos', [
            'placas' => 'ABC123',
            'image_url' => null, // Verificar que image_url esté configurado como nulo
        ]);
    }

    // Prueba para verificar que el índice de vehículos aceptados devuelve una respuesta exitosa y la cantidad correcta de vehículos
    public function testIndex()
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

        // Simular la autenticación 
        Sanctum::actingAs($user);

        // Crear vehículos con la librería factory
        Vehiculo::factory()->count(3)->create();

        // Hacer la solicitud a la ruta
        $response = $this->json('get', 'api/aceptados');

        // Verificar que la prueba fue exitosa
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Asegurar que no hay vehículos con solicitudes pendientes
        $response->assertJsonCount(0, 'vehiculos');
    }
}
