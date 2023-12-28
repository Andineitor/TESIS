<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
        ? response()->json(['status' => __('Le hemos enviado por correo electrónico el enlace para restablecer su contraseña.')])
        : response()->json(['email' => __('La dirección de correo electrónico proporcionada no se encontró en nuestros registros.')], 400);
}

    /**
     * Display the password reset view for the given token.
     *
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
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

        // Verifica el $status y devuelve la URL de redirección según corresponda
        if ($status == Password::PASSWORD_RESET) {
            // Obtén el token desde la solicitud
            $token = $request->input('token');
        
            // Puedes personalizar la URL de redirección aquí y agregar el token
            $redirectUrl = "https://cargod.netlify.app/reset-password/{$token}";

            return response()->json(['status' => __($status), 'redirect_url' => $redirectUrl]);
        } else {
            return response()->json(['email' => [__($status)]], 400);
        }
    }
}