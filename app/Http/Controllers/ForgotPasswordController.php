<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['status' => 'success', 'message' => 'Correo electrónico de restablecimiento enviado con éxito']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Error al enviar el correo electrónico de restablecimiento'], 400);
        }
    }
}