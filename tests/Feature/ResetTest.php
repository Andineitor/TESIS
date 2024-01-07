<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use Illuminate\Support\Str;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;


    public function testForgotPassword()
    {
        // Crear un usuario de prueba
        $user = User::factory()->create();

        $response = $this->postJson('api/forgot-password', ['email' => $user->email]);

        $response->assertStatus(200);
        $response->assertJson(['message' => __('Correo enviado con éxito')]);
    }

    public function testResetPassword()
    {
        // Crear un usuario de prueba
        $user = User::factory()->create();

        // Simular el envío de un correo de restablecimiento de contraseña
        $token = Password::createToken($user);
        
        // Nueva contraseña
        $newPassword = 'new_password';

        // Realizar la solicitud para restablecer la contraseña
        $response = $this->postJson('api/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => __('Contraseña cambiada exitosamente')]);

        // Verificar que la contraseña se haya actualizado en la base de datos
        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }
}
