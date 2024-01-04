<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    public function testRegistro()
    {
        // Simula una solicitud POST al endpoint de registro
        $response = $this->json('post', 'api/registro', [
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'cedula' => '987654321',
            'direccion' => 'andiloor2809@gmail.com',
            'celular' => '123456789',
            'email' => 'andiloor2809@gmail.com',
            'role_id' => 1,
            'password' => 'password123',
        ]);
        // Verifica que la respuesta tenga el código 201 (creado)
        $response->assertStatus(201);

        // Verifica que el usuario se haya creado correctamente en la base de datos
        $this->assertDatabaseHas('users', [
            'nombre' => $response['user']['nombre'],
            'apellido' => $response['user']['apellido'],
            'email' => $response['user']['email'],
        ]);

        // Verifica que la contraseña del usuario en la base de datos sea la correcta
        $user = User::where('email', $response['user']['email'])->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function testLogin()
    {
        // Obtiene el usuario creado durante el registro
        $response = $this->json('post', 'api/registro', [
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'cedula' => '987654321',
            'direccion' => 'andiloor2809@gmail.com',
            'celular' => '123456789',
            'email' => 'andiloor2809@gmail.com',
            'role_id' => 1,
            'password' => 'password123',
        ]);

        $user = User::where('email', $response['user']['email'])->first();

        // Simula una solicitud POST al endpoint de login
        $response = $this->json('post', 'api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // Verifica que la respuesta tenga el código 200 (OK)
// Verifica que la respuesta tenga el código 200 (OK)
// Verifica que la respuesta tenga el código 200 (OK)
        $response->assertStatus(200);

        // Verifica que la respuesta contenga un token de autenticación
        $this->assertNotNull($response->json()['token']);


    }


    //log out
    // Test para la función logout
    public function testLogout()
    {
        // Crear un usuario para las pruebas
        $user = User::factory()->create();

        // Autenticar al usuario usando Sanctum
        Sanctum::actingAs($user);

        // Simular una solicitud POST al endpoint de logout
        $response = $this->json('post', 'api/logout');

        // Verificar que la respuesta tenga el código 200 (OK)
        $response->assertStatus(200);

        // Verificar que el token del usuario ha sido revocado
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
        ]);

    }


    // Test para la función update
    public function testUpdate()
    {
         // Crear un usuario para las pruebas
         $user = User::factory()->create();

        // Autenticar al usuario usando Sanctum
        Sanctum::actingAs($user);
        
        // Simular una solicitud PUT al endpoint de update
        $response = $this->json('put', 'api/update/' . $user->id, [
            'nombre' => 'juan',
            'apellido' => 'pedro',
            'cedula' => '987654321',
            'direccion' => 'andiloor2809@gmail.com',
            'celular' => '123456789',
            'email' => 'nuevoemail@example.com',
            'role_id' => 1,
            'password' => 'nuevapassword',
        ]);

        // Verificar que la respuesta tenga el código 200 (OK)
        $response->assertStatus(200);

        // Recargar el usuario desde la base de datos para obtener los cambios
        $user = $user->fresh();

        // Verificar que los datos del usuario se han actualizado correctamente
        $this->assertEquals('nuevoemail@example.com', $user->email);
        // Agrega más aserciones según tus necesidades
    }

}
