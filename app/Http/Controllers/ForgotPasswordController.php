<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
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