<?php

// app/Http/Controllers/ForgotPasswordController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Verifica si el usuario existe
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['email' => ['El usuario con este correo electrónico no existe.']], 400);
        }

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            // Obtén el token asociado al correo electrónico
            $token = $this->getResetToken($request->get('email'));

            // Obtén la URL del front-end y agrega el token
            $frontendUrl = 'https://cargod.netlify.app/reset-password';
            $resetUrl = $frontendUrl . '/' . $token;

            return response()->json([
                'message' => trans($response),
                'reset_url' => $resetUrl,
            ]);
        }

        return response()->json(['email' => [trans($response)]], 400);
    }

  
    protected function getResetToken($email)
{
    // Get the user based on the email
    $user = User::where('email', $email)->first();

    if (!$user) {
        return null; // Or handle the case when the user is not found
    }

    // Create a token for the user
    return Password::createToken($user);
}
}
