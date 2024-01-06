<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => bcrypt($password)])->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['status' => 'success', 'message' => 'Contraseña restablecida con éxito']);
        } elseif ($status == Password::INVALID_TOKEN) {
            return response()->json(['status' => 'failed', 'message' => 'El token de restablecimiento de contraseña no es válido'], 400);
        } elseif ($status == Password::INVALID_USER) {
            return response()->json(['status' => 'failed', 'message' => 'No se encontró un usuario con esa dirección de correo electrónico'], 400);
        } elseif ($status == Password::RESET_THROTTLED) {
            return response()->json(['status' => 'failed', 'message' => 'Demasiados intentos de restablecimiento de contraseña. Por favor, espere antes de intentarlo de nuevo'], 400);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Error al restablecer la contraseña'], 400);
        }
    }
}
