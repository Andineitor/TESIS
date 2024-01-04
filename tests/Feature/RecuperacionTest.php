<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tests\TestCase;

class RecuperacionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testUserCanResetPassword()
    {
        // Crear un usuario en la base de datos
        $user = User::factory()->create();

        // Olvidar la contraseña del usuario
        $response = $this->post('/forgot-password', ['email' => $user->email]);

        // Obtener el token de restablecimiento de la base de datos
        $token = Password::getRepository()->create($user);

        // Restablecer la contraseña
        $newPassword = 'newpassword123';
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        // Verificar que la contraseña del usuario haya sido actualizada
    $hashedPassword = $user->fresh()->password;
    $this->assertTrue(Hash::check($newPassword, $hashedPassword));

    // Imprimir información de depuración
    dump("New Password: $newPassword");
    dump("Hashed Password: $hashedPassword");

    // Verificar que la respuesta sea exitosa
    $response->assertJson(['status' => __('Cambio de clave exitoso')]);
}
    // public function testUserCanResetPasswordWithValidToken()
    // {
    //     // Crear un usuario
    //     $user = User::factory()->create();

    //     // Solicitar un enlace de restablecimiento de contraseña
    //     $token = Password::createToken($user);

    //     // Hacer una solicitud para restablecer la contraseña con el token válido
    //     $response = $this->postJson(route('password.reset'), [
    //         'email' => $user->email,
    //         'token' => $token,
    //         'password' => 'newpassword',
    //         'password_confirmation' => 'newpassword',
    //     ]);

    //     // Verificar que la contraseña se ha restablecido correctamente
    //     $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    // }

    
}
