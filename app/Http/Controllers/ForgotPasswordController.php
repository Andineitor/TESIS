<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function showResetForm(Request $request, $token)
    {
        // Validar el token y obtener el usuario asociado
        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario y el token son válidos
        if (!$user || !Password::tokenExists($user, $token)) {
            return response()->json(['error' => 'Token inválido o usuario no encontrado'], 400);
        }

        // Obtener la URL del front-end y redirigir con el token
        $frontendUrl = 'https://cargod.netlify.app/reset-password';
        $resetUrl = $frontendUrl . '/' . $token;

        return redirect($resetUrl);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __('Hemos enviado por correo electrónico el enlace para restablecer su contraseña.')])
            : response()->json(['email' => __('La dirección de correo electrónico no se encontró en nuestros registros.')], 400);
    }
}