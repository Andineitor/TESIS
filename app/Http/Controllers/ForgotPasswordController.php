<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use App\Notifications\CustomResetPasswordNotification;
use App\Models\User; // Asegúrate de importar el modelo User

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['email' => __('La dirección de correo electrónico no se encontró en nuestros registros.')], 400);
        }

        $token = Password::createToken($user);
        try {
            $user->notify(new CustomResetPasswordNotification($token));
        } catch (\Exception $e) {
            // Log o manejo de errores
          
        // Enviar la notificación con el token
        $user->notify(new CustomResetPasswordNotification($token));
  return response()->json(['error' => __('Error al enviar la notificación')], 500);
        }
        return $this->sendResetLinkResponse($request, Password::RESET_LINK_SENT);
    }
}