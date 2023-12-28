<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __('Hemos enviado por correo electrónico el enlace para restablecer su contraseña.')])
            : response()->json(['email' => [__($status)]], 400);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Validar si el token es válido
        $response = $this->broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Realizar el restablecimiento de la contraseña
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Evento para indicar que la contraseña ha sido restablecida
                event(new PasswordReset($user));
            }
        );

        // Verificar el estado de la operación y responder en consecuencia
        return $response == Password::PASSWORD_RESET
            ? response()->json(['message' => __($response)])
            : response()->json(['email' => [__($response)]], 400);
    }

    protected function broker()
    {
        return Password::broker();
    }
}
