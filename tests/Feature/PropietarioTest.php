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

    public function testRegistroExitoso()
    {
        // Create a user
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
    
        // Authenticate the user using Sanctum
        Sanctum::actingAs($user);
    
        // Realize the request to register a vehicle (without providing an image)
        $response = $this->json('post', 'api/vehiculos', [
            'tipo_vehiculo' => 'Automóvil',
            'marca' => 'Toyota',
            'placas' => 'ABC123',
            'numero_pasajero' => 5,
            'costo_alquiler' => 100.00, // Provide a decimal value with two decimal places
            'contacto' => 'Your Contact',
            'descripcion' => 'Your Description',
        ]);
        
    
        // Assert the response status and content
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    
        // Assert that the vehicle is registered in the database
        $this->assertDatabaseHas('vehiculos', [
            'placas' => 'ABC123',
            'image_url' => null, // Check that image_url is set to null
        ]);
    }
    


    

    public function testIndex()
{
    // Create a user
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

    // Authenticate the user using Sanctum
    Sanctum::actingAs($user);

    // Create vehicles with no pending request
    Vehiculo::factory()->count(3)->create();

    // Create a vehicle with a pending request

    // Make a request to the index method
    $response = $this->json('get', 'api/aceptados');

    // Assert the response status and content
    $response->assertStatus(200)
    ->assertJson(['success' => true]);

// Assert that there are no vehicles with pending requests
$response->assertJsonCount(0, 'vehiculos');
}
}
