<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/home'; // Puedes cambiar esta ruta según tus necesidades

    public function showResetForm($token)
    {
        // Cambia la URL a la ruta completa de tu frontend
        $frontendUrl = 'https://cargod.netlify.app/reset-password/';
        return redirect($frontendUrl . $token); // Redirige directamente al frontend con el token
    }

    protected function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );
    
        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['status' => __('Cambio de clave exitoso')]);
        } else {
            // Agregar un comentario en caso de error
            $user = User::where('email', $request->email)->first();
            $user->comments()->create([
                'comment' => 'Error al cambiar la contraseña: ' . $status,
            ]);
    
            return response()->json(['email' => [__($status)]], 400);
        }
    }
}